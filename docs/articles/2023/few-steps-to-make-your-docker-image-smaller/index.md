# Few steps to make your docker image smaller

I have some projects where I need [bison](https://www.gnu.org/software/bison/). I decided to build my image when I didn't find official or fresh unofficial docker images.

To compile bison you need to install some apt dependencies and compile the last version of `autoconf` from the source code.
I decided to make it from `debian:bullseye-slim` image. It is the latest Debian light image which size is 80.5MB (without compression).
```bash
docker images --format="table {{.Repository}}\t{{.Tag}}\t{{ .Size }}"  | grep "REPOSITORY\|debian"
REPOSITORY    TAG             SIZE
debian        bullseye-slim   80.5MB
```

## bison-test:1.0

First I make a simple `Dockerfile` and build it.
```bash
FROM debian:bullseye-slim

ENV BISON_VERSION v3.8.2
ENV AUTOCONF_VERSION 2.71

# install apt dependencies
RUN set -eux; \
    apt-get update; \
    apt-get install -y \
        automake \
        autopoint \
        ca-certificates \
        flex \
        gettext \
        gcc \
        git \
        gperf \
        graphviz \
        help2man \
        libc6-dev \
        m4 \
        make \
        texinfo \
        wget \
        xsltproc

# compile autoconf
RUN wget ftp://ftp.gnu.org/gnu/autoconf/autoconf-$AUTOCONF_VERSION.tar.gz
RUN tar -xvzf autoconf-$AUTOCONF_VERSION.tar.gz
WORKDIR /autoconf-$AUTOCONF_VERSION
RUN ./configure
RUN make
RUN make install
WORKDIR /

# compile bison
RUN git clone --branch=$BISON_VERSION --depth=1 https://github.com/akimd/bison.git /bison
WORKDIR /bison
RUN git submodule update --init --recursive
RUN ./bootstrap
RUN ./configure
RUN make
RUN make install
WORKDIR /
```

```bash
docker build -t bison-test:1.0 .
```

Ok. Image contains many layers
```bash
docker history bison-test:1.0 --format="table {{.CreatedBy }}\t{{ .Size }}" | grep -v 0B
CREATED BY                                      SIZE
RUN /bin/sh -c make install # buildkit          5.86MB
RUN /bin/sh -c make # buildkit                  22.3MB
RUN /bin/sh -c ./configure # buildkit           2.02MB
RUN /bin/sh -c ./bootstrap # buildkit           18.1MB
RUN /bin/sh -c git submodule update --init -…   137MB
RUN /bin/sh -c git clone --branch=$BISON_VER…   6.55MB
RUN /bin/sh -c make install # buildkit          3.46MB
RUN /bin/sh -c make # buildkit                  990kB
RUN /bin/sh -c ./configure # buildkit           127kB
RUN /bin/sh -c tar -xvzf autoconf-$AUTOCONF_…   6.88MB
RUN /bin/sh -c wget ftp://ftp.gnu.org/gnu/au…   2MB
RUN /bin/sh -c set -eux;     apt-get update;…   348MB
/bin/sh -c #(nop) ADD file:3ea7c69e4bfac2ebb…   80.5MB
```

and the total size is 633MB.
```bash
docker images --format="table {{.Repository}}\t{{.Tag}}\t{{ .Size }}"  | grep "REPOSITORY\|bison-test"
REPOSITORY    TAG             SIZE
bison-test    1.0             633MB
```

## bison-test:2.0

Let's reduce layers.
Instead of creating many separate `RUN` commands, I wrote two scripts.
```bash
RUN set -eux; \
   wget ftp://ftp.gnu.org/gnu/autoconf/autoconf-$AUTOCONF_VERSION.tar.gz; \
   tar -xvzf autoconf-$AUTOCONF_VERSION.tar.gz; \
    cd /autoconf-$AUTOCONF_VERSION; \
   ./configure; \
   make; \
   make install

RUN set -eux; \
   git clone --branch=$BISON_VERSION --depth=1 https://github.com/akimd/bison.git /bison; \
   cd /bison; \
   git submodule update --init --recursive; \
   ./bootstrap; \
   ./configure; \
   make; \
   make install
```

The number of layers has decreased
```bash
docker history bison-test:2.0 --format="table {{.CreatedBy }}\t{{ .Size }}" | grep -v 0B
CREATED BY                                      SIZE
RUN /bin/sh -c set -eux;  git clone --branch…   188MB
RUN /bin/sh -c set -eux;  wget ftp://ftp.gnu…   13.5MB
RUN /bin/sh -c set -eux;     apt-get update;…   348MB
/bin/sh -c #(nop) ADD file:3ea7c69e4bfac2ebb…   80.5MB
```

but the total size is only 3 MB smaller.
```bash
docker images --format="table {{.Repository}}\t{{.Tag}}\t{{ .Size }}"  | grep "REPOSITORY\|bison-test"
REPOSITORY    TAG             SIZE
bison-test    2.0             630MB
```

## bison-test:3.0

Now I'll try to remove all the source files and add another `RUN` command to remove the apt dependencies and the apt cache.
```bash
RUN set -eux; \
   wget ftp://ftp.gnu.org/gnu/autoconf/autoconf-$AUTOCONF_VERSION.tar.gz; \
   tar -xvzf autoconf-$AUTOCONF_VERSION.tar.gz; \
    cd /autoconf-$AUTOCONF_VERSION; \
   ./configure; \
   make; \
   make install; \
   rm -rf /autoconf-$AUTOCONF_VERSION.tar.gz; \ # remove unnecessary source files
    rm -rf /autoconf-$AUTOCONF_VERSION # remove unnecessary source files

RUN set -eux; \
   git clone --branch=$BISON_VERSION --depth=1 https://github.com/akimd/bison.git /bison; \
   cd /bison; \
   git submodule update --init --recursive; \
   ./bootstrap; \
   ./configure; \
   make; \
   make install; \
   rm -rf /bison # remove unnecessary source files

# remove apt dependencies and cache
RUN set -eux; \
    apt-mark auto '.*' > /dev/null; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*
```

```bash
docker history bison-test:3.0 --format="table {{.CreatedBy }}\t{{ .Size }}" | grep -v 0B
CREATED BY                                      SIZE
RUN /bin/sh -c set -eux;     apt-mark auto '…   1.96MB
RUN /bin/sh -c set -eux;  git clone --branch…   5.83MB
RUN /bin/sh -c set -eux;  wget ftp://ftp.gnu…   3.46MB
RUN /bin/sh -c set -eux;     apt-get update;…   330MB
/bin/sh -c #(nop) ADD file:3ea7c69e4bfac2ebb…   80.5MB
```

The total size is 422MB. It's 211MB smaller than the first image `bison-test:1.0`
```bash
docker images --format="table {{.Repository}}\t{{.Tag}}\t{{ .Size }}"  | grep "REPOSITORY\|bison-test"
REPOSITORY    TAG             SIZE
bison-test    3.0             422MB
```

## bison-test:4.0

The last image is smaller, but too large compared to the original `debian:bullseye-slim` image.
When you see the previous image layers, you may notice that the `apt-install` layer is very big and the last command does not reduce the size of the image.
I need to install dependencies, compile bison and remove dependencies inside one layer.
Let's do it.
```bash
FROM debian:bullseye-slim

ENV BISON_VERSION v3.8.2
ENV AUTOCONF_VERSION 2.71

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        automake \
        autopoint \
        ca-certificates \
        flex \
        gettext \
        gcc \
        git \
        gperf \
        graphviz \
        help2man \
        libc6-dev \
        m4 \
        make \
        texinfo \
        wget \
        xsltproc; \
    \
   wget ftp://ftp.gnu.org/gnu/autoconf/autoconf-$AUTOCONF_VERSION.tar.gz; \
   tar -xvzf autoconf-$AUTOCONF_VERSION.tar.gz; \
    cd /autoconf-$AUTOCONF_VERSION; \
   ./configure; \
   make; \
   make install; \
   rm -rf /autoconf-$AUTOCONF_VERSION.tar.gz; \
   rm -rf /autoconf-$AUTOCONF_VERSION; \
   cd /; \
    \
   git clone --branch=$BISON_VERSION --depth=1 https://github.com/akimd/bison.git /bison; \
   cd /bison; \
   git submodule update --init --recursive; \
   ./bootstrap; \
   ./configure; \
   make; \
   make install; \
   rm -rf /bison; \
   cd /; \
    \
    apt-mark auto '.*' > /dev/null; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*
```

Great! The image has only two layers
```bash
docker history bison-test:4.0 --format="table {{.CreatedBy }}\t{{ .Size }}" | grep -v 0B
CREATED BY                                      SIZE
RUN /bin/sh -c set -eux;     apt-get update;…   11.3MB
/bin/sh -c #(nop) ADD file:3ea7c69e4bfac2ebb…   80.5MB
```

and the total size is 91.8 MB!
```bash
docker images --format="table {{.Repository}}\t{{.Tag}}\t{{ .Size }}"  | grep "REPOSITORY\|bison-test"
REPOSITORY    TAG             SIZE
bison-test    4.0             91.8MB
```

Now you know a few simple steps to make your image smaller:
* reduce the number of layers
* remove unnecessary files/dependencies inside the layer where you created it

You can see the original bison Dockerfile [here](https://github.com/mrsuh/docker-bison).
