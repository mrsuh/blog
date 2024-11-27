# PHP var_sizeof()

The [library](https://github.com/mrsuh/php-var-sizeof) provides functions to get the size of any PHP variable in bytes
It should be a more accurate tool for calculating the total size of a PHP variable than `memory_get_usage()`, but it has [limitations](#h2-limitations).

## How it works

The `var_sizeof()` and `var_class_sizeof()` functions use FFI to access the internal structures of PHP variables.
They calculate the size of internal structures like `zval`, `_zend_array`, `_zend_object`, etc., along with any additional allocated memory for these structures.
However, they do not account for the memory used by handlers, functions, or similar components.

## Requirements

* PHP >= 7.4 (with FFI)
* Linux(x86_64/aarch64) / Darwin(x86_64/arm64)

## Usage

```bash
composer require mrsuh/php-var-sizeof
```

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$int = 1;
printf("variable \$int size: %d bytes\n", var_sizeof($int));

$array = array_fill(0, 100, $a);
printf("variable \$array size: %d bytes\n", var_sizeof($array));

$object = new \stdClass();
printf("variable \$object size: %d bytes\n", var_sizeof($object));
printf("class \$object size: %d bytes\n", var_class_sizeof($object));
```

## var_sizeof vs memory_get_usage

PHP 8.1.2 Linux(x86_64)

| type                                               | var_sizeof(bytes) | memory_get_usage(bytes) |
|----------------------------------------------------|-------------------|-------------------------|
| NULL                                               | 16                | 0                       |
| boolean(true)                                      | 16                | 0                       |
| integer(1)                                         | 16                | 0                       |
| double(1.5)                                        | 16                | 0                       |
| string("hello world")                              | 27                | 40                      |
| resource                                           | 48                | 416                     |
| callable                                           | 72                | 384                     |
| array(count: 0, list: true)                        | 336               | 0                       |
| array(count: 100, list: true)                      | 2,128             | 8,248                   |
| array(count: 1,000, list: true)                    | 16,464            | 36,920                  |
| array(count: 10,000, list: true)                   | 262,224           | 528,440                 |
| array(count: 100, list: false)                     | 5,192             | 8,248                   |
| array(count: 1,000, list: false)                   | 41,032            | 41,016                  |
| array(count: 10,000, list: false)                  | 655,432           | 655,416                 |
| EmptyClass{}                                       | 72                | 40                      |
| ClassWithArray{"array(count: 0, list: true)"}      | 408               | 56                      |
| ClassWithArray{"array(count: 100, list: true)"}    | 2,200             | 8,304                   |
| ClassWithArray{"array(count: 1,000, list: true)"}  | 16,536            | 36,976                  |
| ClassWithArray{"array(count: 10,000, list: true)"} | 262,296           | 528,496                 |
| ClassWithObject{"EmptyClass{}"}                    | 144               | 96                      |
| ArrayIterator{"array(count: 100, list: true)"}     | 2,264             | 8,376                   |
| ArrayIterator{"array(count: 100, list: false)"}    | 5,328             | 40,376                  |

| type                                               | var_class_sizeof(bytes) | var_sizeof(bytes) | memory_get_usage(bytes) |
|----------------------------------------------------|-------------------------|-------------------|-------------------------|
| EmptyClass{}                                       | 1,362                   | 72                | 40                      |
| ClassWithArray{"array(count: 0, list: true)"}      | 1,494                   | 408               | 56                      |
| ClassWithArray{"array(count: 100, list: true)"}    | 1,494                   | 2,200             | 8,304                   |
| ClassWithArray{"array(count: 1,000, list: true)"}  | 1,494                   | 16,536            | 36,976                  |
| ClassWithArray{"array(count: 10,000, list: true)"} | 1,494                   | 262,296           | 528,496                 |
| ClassWithObject{"EmptyClass{}"}                    | 1,495                   | 144               | 96                      |
| ArrayIterator{"array(count: 100, list: true)"}     | 2,437                   | 2,264             | 8,376                   |
| ArrayIterator{"array(count: 100, list: false)"}    | 2,437                   | 5,328             | 40,376                  |


## Limitations

* Works correctly only with userland objects and SPL \ArrayIterator
* Does not handle complex structures like extensions, resources, callables, or functions accurately
* To calculate the total size of an object, you need to use both `var_sizeof()` and `var_class_sizeof()`
