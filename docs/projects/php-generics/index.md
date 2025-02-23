# PHP Generics

This project introduces real generics in PHP, utilizing RFC-style syntax `Class<T>` with runtime type checking. 
It enhances code flexibility and type safety by generating concrete classes from generic ones, facilitating more robust and maintainable PHP applications.

Here’s an example of how the [PHP Generics library](https://github.com/mrsuh/php-generics) works:

```php
<?php

namespace App;

class Box<T> {

   private ?T $data = null;

   public function set(T $data): void {
       $this->data = $data;
   }

   public function get(): ?T {
       return $this->data;
   }
}
```

```php
<?php

namespace App;

class Usage {

   public function run(): void
   {
       $stringBox = new Box<string>();
       $stringBox->set('cat');
       var_dump($stringBox->get()); // string "cat"

       $intBox = new Box<int>();
       $intBox->set(1);
       var_dump($intBox->get()); // integer 1
   }
}
```

The library uses `monomorphization` to implement generics in PHP.
You can [read more](/articles/2022/generics-implementation-approaches/) about the different approaches to implementing generics.

## How It Works

The library generates concrete classes from generic classes with unique names based on the name and arguments of the generic class. These classes are stored in the `cache` folder:

```php
Box<string> -> BoxForString
Box<int> -> BoxForInt        
```

Composer then autoloads the concrete classes instead of the original generic classes.

```json
{
   "autoload": {
       "psr-4": {
           "App\\": ["cache/","src/"]
       }
   }
}
```

[Learn more](/articles/2022/how-php-engine-builds-ast/) about how the library works in this article or check out [these slides](https://phprussia.ru/moscow/2022/abstracts/9165).

## Memory Usage

*Items in collection: 10000*

| type                      | var_class_sizeof(bytes) | var_sizeof(bytes) | memory_get_usage(bytes) |
|---------------------------|-------------------------|-------------------|-------------------------|
| array(count: 10000)       | 0                       | 822,224           | 1,051,320               |
| psalm(count: 10000)       | 1,510                   | 822,432           | 1,051,560               |
| monomorphic(count: 10000) | 1,528                   | 822,432           | 1,051,560               |
| type-erased(count: 10000) | 1,512                   | 822,432           | 1,051,560               |

## Performance

```bash
PHPBench (1.2.3) running benchmarks...
with configuration file: /app/phpbench.json
with PHP version 8.1.3, xdebug ❌, opcache ❌

\App\Tests\TypHintBench

    benchWithoutType........................R1 I6 - Mo240.713μs (±0.47%)
    benchWithArrayType......................R1 I70 - Mo247.663μs (±0.45%)
    benchWithMixedType......................R2 I59 - Mo249.293μs (±0.54%)
    benchWithClassType......................R1 I26 - Mo306.533μs (±0.48%)

Subjects: 4, Assertions: 0, Failures: 0, Errors: 0
+--------------+--------------------+-----+------+-----+-----------+-----------+--------+
| benchmark    | subject            | set | revs | its | mem_peak  | mode      | rstdev |
+--------------+--------------------+-----+------+-----+-----------+-----------+--------+
| TypHintBench | benchWithoutType   |     | 1000 | 100 | 674.272kb | 240.713μs | ±0.47% |
| TypHintBench | benchWithArrayType |     | 1000 | 100 | 674.272kb | 247.663μs | ±0.45% |
| TypHintBench | benchWithMixedType |     | 1000 | 100 | 674.272kb | 249.293μs | ±0.54% |
| TypHintBench | benchWithClassType |     | 1000 | 100 | 674.272kb | 306.533μs | ±0.48% |
+--------------+--------------------+-----+------+-----+-----------+-----------+--------+
```

PHP uses dynamic typing, so it’s no surprise that adding types slightly reduces performance. Additionally, arrays consume less memory compared to class wrappers.
You can [read more](/articles/2022/comparing-php-collections/) about performance and memory comparisons here.
