# Generics implementation approaches

[origin]https://dev.to/mrsuh/generics-implementation-approaches-3bf0

I want to introduce you to generics implementation approaches with some simple examples everyone can understand.
The description of each approach includes key features without details.
Basic examples are written in PHP because I'm a PHP developer, but there are also Hack/Java/C++ examples.

## Type erasure

This approach means generics arguments are dropped after compilation.

Before `type erasure`:
```php
<?php

class Container<T> {
    
    private T $data; 
    
    public function __construct(T $data) {
        $this->data = $data;
    }  
}

$intContainer = new Container<int>(1);
```

After `type erasure`:
```php
<?php

class Container {
    
    private $data; 
    
    public function __construct($data) {
        $this->data = $data;
    }  
}

$intContainer = new Container(1);
```

[pagebreak]

You can't do that because it doesn't make sense after `type erasure`:
```php
<?php

class Container<T> {       
    
    public function foo($data) {
        $className = T::class;
        
        T::staticFunction();
        
        $newObject = new T();
        
        if($data instanceof T) {
        
        }
    }  
}
```

During `static analysis` and `compile time` generics types can be reflected and checked, but after `type erasure` at `run time`, this is not possible.
This approach has a small performance/memory effect and some generics types restrictions (see example above).

Type erasure generics visibility table for better understanding:

|                 | static analysis time | compile time | runtime |
|-----------------|----------------------|--------------|---------|
| type reflection | yes                  | yes          | no*     |
| type checking   | yes                  | yes          | no      |

no* - generics types of some languages like `Java`/`Hack` may be reflected and not used by VM at `runtime`.

I can assume the PHP [Psalm annotations](https://psalm.dev/docs/annotating_code/templated_annotations) are a kind of `type erasure generics`, but there is no such thing in PHP as generic types at `compile/run time`.
PHP Psalm annotations visibility table:

|                 | static analysis time | compile time | runtime |
|-----------------|----------------------|--------------|---------|
| type reflection | yes                  | no           | no      |
| type checking   | yes                  | no           | no      |

[pagebreak]

Let's look at a real example of type erasure generics in `Java`:

test.java
```java
class Container<T> {

  private T data;

  public void set(T data) {
      this.data = data;
  }
}

class Programm {
    public static void main(String[] args) {
        Container<Integer> intContainer = new Container<Integer>();
        intContainer.set(1);

        Container<String> stringContainer = new Container<String>();
        stringContainer.set("hello");
    }
}
```

Use these commands to compile and run the script above:
```bash
docker run -it --rm -v $PWD:/app -w /app openjdk:latest javac test.java
docker run -it --rm -v $PWD:/app -w /app openjdk:latest java Programm
```

If you try to pass `string "hello"` to `intContainer.set()` you will get a `compile time` error:
```bash
test.java:13: error: incompatible types: String cannot be converted to Integer
        intContainer.set("hello");
                         ^
```

[pagebreak]

## Reification

Generic arguments are retained at `compile/run time` and can be reflected.

There is a real example of `reified` generics in `Hack`.
The keyword `reify` is very important to mark a generics as `reified`.

test.hack
```php
class MyContainer<reify T> {
    private T $var;

    public function set(T $var): void {
        $this->var = $var;
    }
}

<<__EntryPoint>>
function main(): void {
  $intContainer = new MyContainer<int>();
  $intContainer->set(1);

  $stringContainer = new MyContainer<string>();
  $stringContainer->set("hello");
}
```

You can run the script above with command:
```bash
docker run -it --rm -v $PWD:/app -w /app hhvm/hhvm:latest hhvm test.hack
```

If you try to pass `string "hello"` to `$intContainer->set()` you will get a `compile time` error:
```bash
Fatal error: Uncaught TypeError: Argument 1 passed to MyContainer::set() must be an instance of int, string given in /app/test.hack:6
```

[pagebreak]

At `runtime`, there is only one class `MyContainer` with many generic types.
Let's look inside the `$stringContainer` variable by `var_dump()`:
```bash
object(MyContainer) (2) {
  ["86reified_prop"]=>
  vec(1) {
    dict(1) {
      ["kind"]=>
      int(4)
    }
  }
  ["var":"MyContainer":private]=>
  string(5) "hello"
}
```
The example above describes the `MyContainer` class and the `kind` property.
I think `kind => 4` is a constant for type `string`.
It means you can get the generics type at `runtime`.

This approach has a small performance/memory effect.
Nikita Popov already had an [attempt](https://github.com/PHPGenerics/php-generics-rfc/issues/45) to implement this type of generics in `PHP`.

`Reified` generics visibility table:

|                 | static analysis time | compile time | runtime |
|-----------------|----------------------|--------------|---------|
| type reflection | yes                  | yes          | yes     |
| type checking   | yes                  | yes          | yes     |


[pagebreak]

## Monomorphization

A new class is generated for each generic argument combination.

Before `monomorphization`:
```php
<?php

class Container<T> {
    
    private T $data; 
    
    public function __construct(T $data) {
        $this->data = $data;
    }  
}

$intContainer = new Container<int>(1);
```

After `monomorphization`:
```php
<?php

class ContainerForInt {
    
    private int $data; 
    
    public function __construct(int $data) {
        $this->data = $data;
    }  
}

$intContainer = new ContainerForInt(1);
```

This approach has a big memory effect.
PHP doesn't support native generics, but you can test `monomorphic` generics with my [library](https://github.com/mrsuh/php-generics).

`Monomorphic` generics visibility table:

|                 | static analysis time | compilation time | run time |
|-----------------|----------------------|------------------|----------|
| type reflection | yes                  | yes              | no       |
| type checking   | yes                  | yes              | yes      |

[pagebreak]

`C++` templates are a real example of `monomorphization`:

test.cpp
```cpp
template <class T> class MyContainer
{
    private:
     T data;

    public:
     void set(T _data)  {
        data = _data;
     }
};

int main () {
  MyContainer<int> intContainer;
  intContainer.set(1);

  MyContainer<const char*> stringContainer;
  stringContainer.set("hello");

  return 0;
}
```

You can compile and run the script above with commands:
```bash
docker run -it --rm -v $PWD:/app -w /app gcc:latest g++ test.cpp -o test
docker run -it --rm -v $PWD:/app -w /app gcc:latest ./test
```

If you try to pass `string "hello"` to `intContainer.set()` you will get a `compile time` error:
```bash
test.cpp: In function 'int main()':
test.cpp:15:20: error: invalid conversion from 'const char*' to 'int' [-fpermissive]
   15 |   intContainer.set("hello");
      |                    ^~~~~~~
      |                    |
      |                    const char*
test.cpp:8:17: note:   initializing argument 1 of 'void MyContainer<T>::set(T) [with T = int]'
    8 |      void set(T _data) {
      |               ~~^~~~~
```

This approach increases `compile time`, but improves performance `runtime`.

[pagebreak]

I hope it was helpful to you.
Play around with the examples above - it's a really interesting process!
If you want to go further, read this great [article](https://thume.ca/2019/07/14/a-tour-of-metaprogramming-models-for-generics/) about generics.

Have a nice day!
