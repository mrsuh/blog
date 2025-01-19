# Nginx + Lua + Redis. Efficiently processing sessions and delivering data 

[origin]https://habr.com/ru/articles/270463

Suppose you have data you want to cache and serve without using heavy languages like PHP, while still ensuring the user is authenticated and authorized to access the data. 
Today, I’ll show you how to achieve this using Nginx, Lua, and Redis. This approach can offload the server and boost the response speed by several times.

First, you’ll need to build Nginx with the `nginx_lua_module`.

## Installation Guide

Install the Lua compiler (version 2.0 or 2.1).

Download and build [LuaJIT](http://luajit.org/download.html)
```bash
make && sudo make install
```

To compile Nginx with the Nginx devel kit, you’ll need the `http_rewrite_module`, which, in turn, requires the PCRE library. So, let’s install that first.
```bash
sudo apt-get update
sudo apt-get install libpcre3 libpcre3-dev
```

Download the required modules along with Nginx:
* [nginx devel kit](https://github.com/simpl/ngx_devel_kit/tags)
* [nginx lua module](https://github.com/openresty/lua-nginx-module/tags)
* [nginx](http://nginx.org)

Let’s configure and install `Nginx`
```bash
export LUAJIT_LIB=/usr/local/lib // path to  lua
export LUAJIT_INC=/usr/local/include/luajit-2.1 // path to luaJit

./configure
--prefix=/etc/nginx
--sbin-path=/usr/sbin/nginx
--conf-path=/etc/nginx/nginx.conf
--error-log-path=/var/log/nginx/error.log
--http-log-path=/var/log/nginx/access.log
--pid-path=/var/run/nginx.pid
--lock-path=/var/run/nginx.lock
--http-client-body-temp-path=/var/cache/nginx/client_temp
--http-proxy-temp-path=/var/cache/nginx/proxy_temp
--http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp
--http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp
--http-scgi-temp-path=/var/cache/nginx/scgi_temp
--user=nginx
--group=nginx  
--with-ld-opt="-Wl,-rpath,/path/to/lua/lib" // path to Lua library
--add-module=/path/to/ngx_devel_kit // path to nginx devel kit
--add-module=/path/to/lua-nginx-module // path to nginx lua module
--without-http_gzip_module

make -j2
sudo make install
```

Download the Lua library for working with Redis from [lua redis lib](https://github.com/openresty/lua-resty-redis) and copy it to the Lua library folder using the command:
```bash
sudo make install
```

Include the Lua Redis library in the Nginx configuration.
```bash
http {
...
    lua_package_path lua_package_path "/path/to/lib/lua/resty/redis.lua;;"; // path to lua redis library
...
}
```

That's it! Now you can write Lua scripts that are executed by Nginx.

## Lua script

To quickly and efficiently serve cached data, we'll preload the most frequently used data into Redis during cache warming, while less frequently used data will be added on demand. We'll use Lua on the Nginx side to serve the data. This approach eliminates the need for PHP, significantly speeding up data delivery and reducing server memory usage.

Here's how we'll write the Lua script for this.
*search.lua*
```lua
local string = ngx.var.arg_string
if string == nil then
    ngx.exec("/")
end

local path = "/?string=" .. string

local redis = require "resty.redis"
local red = redis:new()

red:set_timeout(1000) -- 1 sec

local ok, err = red:connect("127.0.0.1", 6379)
if not ok then
ngx.exec(path)
end

res, err = red:get("search:" .. string);

if res == ngx.null then
ngx.exec(path)
else
ngx.header.content_type = 'application/json'
ngx.say(res)
end
```

Include this file in nginx.conf and reload Nginx.
```bash
location /search-by-string {
   content_by_lua_file lua/search.lua;
}
```

Now, when a request is made to `/search-by-string?string=smth`, Lua will connect to Redis and try to find data with the key search:smth. If no data is found, the request will be handled by PHP. However, if the data is already cached in Redis, it will be immediately returned to the user.

What if we only want to serve data to authenticated users with a specific role?
In that case, we can store the session in Redis and check the user's role based on the session data before serving the content.

Since I work with the Symfony2 framework, I created a small bundle, [nginx-session-handler](https://github.com/mrsuh/nginx-session-handler), which allows storing sessions in Redis in a way that suits our needs.

In Redis, the session data will be stored as a hash value:
* `phpsession` - key prefix for the session;
* `php-session` - PHP session
* `user-role` - user's role

Now, we need to write a Lua script to handle this data:

*session.lua*
```lua
local redis = require "resty.redis"
local red = redis:new()

red:set_timeout(1000) -- 1 sec

local ok, err = red:connect("127.0.0.1", 6379)
if not ok then
  ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
end

local phpsession = ngx.var.cookie_PHPSESSID
local ROLE_ADMIN = "ROLE_ADMIN"

if phpsession == ngx.null then
  ngx.exit(ngx.HTTP_FORBIDDEN)
end

local res, err = red:hget("phpsession:" .. phpsession, "user-role")

if res == ngx.null or res ~= ROLE_ADMIN then
  ngx.exit(ngx.HTTP_FORBIDDEN)
end
```

We retrieve the user's session ID from the cookie and attempt to get their role from Redis using the query `HGET phpsession:id user-role`. If the user's session has expired, they are not authenticated, or they do not have the `ROLE_ADMIN` role, the server will return a 403 status code.
We then add this session-handling script before our data retrieval script. This ensures that only authenticated users with the ROLE_ADMIN role can access the data.
In practice, the session-handling script is required for multiple Nginx locations. To avoid duplicating code, we’ll include this script file wherever needed.

First, let’s slightly revise our session-handling script:

*session.lua*
```lua
local _M = {}

function _M.handle()

    local redis = require "resty.redis"
    local red = redis:new()

    red:set_timeout(1000) -- 1 sec

    local ok = red:connect("127.0.0.1", 6379)
    if not ok then
        ngx.exit(ngx.HTTP_INTERNAL_SERVER_ERROR)
    end

    local phpsession = ngx.var.cookie_PHPSESSID
    local ROLE_ADMIN = "ROLE_ADMIN"

    if phpsession == ngx.null then
        ngx.exit(ngx.HTTP_FORBIDDEN)
    end

    local res = red:hget("phpsession:" .. phpsession, "user-role")

    if res == ngx.null or res ~= ROLE_ADMIN then
        ngx.exit(ngx.HTTP_FORBIDDEN)
    end

end

return _M
```

Next, we need to compile the `session.lua` file into a `session.o` object file using the LuaJIT compiler and build Nginx with this file.

Compile the session.o file by running the Lua compiler command:
```bash
/path/to/luajit/bin/luajit -bg session.lua session.o
```

Add the following line to the Nginx build configuration:
```bash
--with-ld-opt="/path/to/session.o"
```

Build Nginx (refer to the steps above for building Nginx).

Once this is done, you can include the compiled file in any Lua script and call the `handle()` function to process the user's session.
```lua
local session = require "session"
session.handle()
```

## Tests 

*Machine configuration*
Processor: Intel® Xeon(R) CPU X3440 @ 2.53GHz × 8
Memory: 7.9 GiB

*ab -n 100 -c 100 php*
```bash
Server Software:        nginx/1.9.4

Concurrency Level:      100
Time taken for tests:   3.869 seconds
Complete requests:      100
Failed requests:        0
<b>Requests per second:    25.85 [#/sec] (mean)</b>
Time per request:       3868.776 [ms] (mean)
Time per request:       38.688 [ms] (mean, across all concurrent requests)
Transfer rate:          6.66 [Kbytes/sec] received

Connection Times (ms)
min  mean[+/-sd] median   max
Connect:        1    3   1.1      3       5
Processing:   155 2116 1053.7   2191    3863
Waiting:      155 2116 1053.7   2191    3863
Total:        160 2119 1052.6   2194    3864

Percentage of the requests served within a certain time (ms)
50%   2194
66%   2697
75%   3015
80%   3159
90%   3504
95%   3684
98%   3861
99%   3864
100%   3864 (longest request)
```

*ab -n 100 -c 100 lua*
```bash
Server Software:        nginx/1.9.4

Concurrency Level:      100
Time taken for tests:   0.022 seconds
Complete requests:      100
Failed requests:        0
<b>Requests per second:    4549.59 [#/sec] (mean)</b>
Time per request:       21.980 [ms] (mean)
Time per request:       0.220 [ms] (mean, across all concurrent requests)
Transfer rate:          688.66 [Kbytes/sec] received

Connection Times (ms)
min  mean[+/-sd] median   max
Connect:        2    4   0.9      4       6
Processing:     3   13   1.6     13      14
Waiting:        3   13   1.6     13      14
Total:          9   17   1.3     18      18

Percentage of the requests served within a certain time (ms)
50%     18
66%     18
75%     18
80%     18
90%     18
95%     18
98%     18
99%     18
100%     18 (longest request)
</spoiler>
Разница "количества запросов в секунду" в 175 раз.

Такой же тест с другими парметрами

*ab -n 10000 -c 100 php*
Server Software:        nginx/1.9.4

Concurrency Level:      100
Time taken for tests:   343.082 seconds
Complete requests:      10000
Failed requests:        0
<b>Requests per second:    29.15 [#/sec] (mean)</b>
Time per request:       3430.821 [ms] (mean)
Time per request:       34.308 [ms] (mean, across all concurrent requests)
Transfer rate:          7.51 [Kbytes/sec] received

Connection Times (ms)
min  mean[+/-sd] median   max
Connect:        0    0   0.3      0       4
Processing:   167 3414 197.5   3408    4054
Waiting:      167 3413 197.5   3408    4054
Total:        171 3414 197.3   3408    4055

Percentage of the requests served within a certain time (ms)
50%   3408
66%   3438
75%   3458
80%   3474
90%   3533
95%   3633
98%   3714
99%   3866
100%   4055 (longest request)
```

*ab -n 10000 -c 100 lua*
```bash
Server Software:        nginx/1.9.4

Concurrency Level:      100
Time taken for tests:   0.899 seconds
Complete requests:      10000
Failed requests:        0
<b>Requests per second:    11118.29 [#/sec] (mean)</b>
Time per request:       8.994 [ms] (mean)
Time per request:       0.090 [ms] (mean, across all concurrent requests)
Transfer rate:          1682.94 [Kbytes/sec] received

Connection Times (ms)
min  mean[+/-sd] median   max
Connect:        0    0   0.4      0       5
Processing:     1    9   3.4      7      19
Waiting:        1    9   3.5      7      18
Total:          2    9   3.4      7      21

Percentage of the requests served within a certain time (ms)
50%      7
66%     13
75%     13
80%     13
90%     13
95%     13
98%     13
99%     15
100%     21 (longest request)
```

The difference in "requests per second" is 381 times.
I hope my article was helpful. If you have any suggestions, comments, or know of any improvements, feel free to reach out.
