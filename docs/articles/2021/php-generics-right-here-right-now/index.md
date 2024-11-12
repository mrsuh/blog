# PHP Generics. Right here. Right now.

Many PHP developers, including me, would like to see support for [generics](https://en.wikipedia.org/wiki/Generic_programming) in PHP. An [RFC](https://github.com/PHPGenerics/php-generics-rfc) for adding generics was created in 2016, but it hasn’t been finished yet. 
I looked into different ways to add generics to PHP but couldn’t find a solution that works well for regular developers. So, I decided to create my own solution in PHP. If you want to try it, you can use this library [mrsuh/php-generics](https://github.com/mrsuh/php-generics) and check out the [repo](https://github.com/mrsuh/php-generics-example) to experiment with it.

[Quote](https://www.reddit.com/r/PHP/comments/j65968/ama_with_the_phpstorm_team_from_jetbrains_on/g83skiz/?context=3)

> For those not overly familiar, there's three broad ways in which generics can be implemented:
>
> Type-erasure: Generic arguments are simply dropped, Foo<T> becomes Foo. It's not possible to reflect generic arguments at runtime, and type-erasure is typically applied under the assumption that type compatibility has been proven during compilation already.
> Reification: Generic arguments are retained at runtime and can be reflected (and, in PHP's case, can be verified at runtime).
> Monomorphization: For the user this is quite similar to reification, but implies that a new class is generated for each generic argument combination. Foo<T> will not store that class Foo has been instantiated with parameter T, it will instead create a new class Foo_T that is specialized for the given type parameter.

## How it works?

In a nutshell:
* parse generics classes;
* generate concrete classes based on them;
* say to `composer autoload` to load files from directory with generated classes first and then load classes from main directory.

Detailed algorithm.

Install library with composer (PHP >= 7.4)
```bash
composer require mrsuh/php-generics
```

Add one more directory("cache/") to composer autoload PSR-4 for generated classes.
It should be placed before the main directory.
composer.json
```json
{
   "autoload": {
       "psr-4": {
           "App\\": ["cache/","src/"]
       }
   }
}
```

For example, you need add several PHP files:
* generic class `Box`
* class `Usage` for use generic class
* script with composer autoload and `Usage` class

src/Box.php
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

src/Usage.php
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

bin/test.php
```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Usage;

$usage = new Usage();
$usage->run();
```

Generate concrete classes from generic classes by `composer dump-generics`
```bash
composer dump-generics -v
Generating concrete classes
 - App\BoxForString
 - App\BoxForInt
 - App\Usage
Generated 3 concrete classes in 0.062 seconds, 16.000 MB memory used
```

What the `composer dump-generics` command does?
* finds all generic uses in classes (`src/Usage.php` for example).
* generates concrete classes from generic classes with unique names based on name and arguments of generic class.
* replaces generic class names to concrete class names in places of use.

In this case should be generated:
* 2 concrete classes of generics `BoxForInt` и `BoxForString`;
* 1 concrete class `Usage` with replaced generics class names to concrete class names.

cache/BoxForInt.php
```php
<?php

namespace App;

class BoxForInt
{
   private ?int $data = null;
  
   public function set(int $data) : void
   {
       $this->data = $data;
   }
  
   public function get() : ?int
   {
       return $this->data;
   }
}
```

cache/BoxForString.php
```php
<?php

namespace App;

class BoxForString
{
   private ?string $data = null;
  
   public function set(string $data) : void
   {
       $this->data = $data;
   }
  
   public function get() : ?string
   {
       return $this->data;
   }
}
```

cache/Usage.php
```php
<?php

namespace App;

class Usage
{
   public function run() : void
   {
       $stringBox = new \App\BoxForString();
       $stringBox->set('cat');
       var_dump($stringBox->get());// string "cat"
      
       $intBox = new \App\BoxForInt();
       $intBox->set(1);
       var_dump($intBox->get());// integer 1
   }
}
```

Generate vendor/autoload.php with `composer dump-autoload` command
```bash
composer dump-autoload
Generating autoload files
Generated autoload files
```

Run script
```bash
php bin/test.php
```

Composer autoload first checks the "cache" directory and then the "src" directory to load the classes.
You can find the code for this example [here](https://github.com/mrsuh/php-generics-example)
More examples [here](https://github.com/mrsuh/php-generics/tests)

## Implementation features

### What syntax is used?
The [RFC](https://github.com/PHPGenerics/php-generics-rfc) does not define a specific syntax so i took [this one](https://github.com/PHPGenerics/php-generics-rfc/issues/45) implemented by Nikita Popov

Syntax example:
```php
<?php

namespace App;

class Generic<in T: Iface = int, out V: Iface = string> {
  
   public function test(T $var): V {
  
   }
}
```

## Syntax problems

I had to upgrade [nikic/php-parser](https://github.com/nikic/PHP-Parser) for parse code with new syntax.
You can see [here](https://github.com/mrsuh/PHP-Parser/pull/1/files#diff-14ec37995c001c0c9808ab73668d64db5d1acc1ab0f60a360dcb9c611ecd57ea) the grammar changes that had to be made for support generics.

Parser use [PHP implementation](https://github.com/ircmaxell/PHP-Yacc) of [YACC](https://wikipedia.org/wiki/Yacc).
The YACC([LALR](https://wikipedia.org/wiki/LALR(1))) algorithm and current PHP syntax make it impossible to describe the full syntax of generics due to collisions.

Collision example:
```php
<?php

const FOO = 'FOO';
const BAR = 'BAR';

var_dump(new \DateTime<FOO, BAR>('now')); // кажется, что здесь есть дженерик
var_dump( (new \DateTime < FOO) , ( BAR > 'now') ); // на самом деле нет
```
[Solution options](https://github.com/PHPGenerics/php-generics-rfc/issues/35#issuecomment-571546650)

Therefore, nested generics are not currently supported.
```php
<?php

namespace App;

class Usage {
   public function run() {
       $map = new Map<Key<int>, Value<string>>();//не поддерживается
   }
}
```

### Parameter names have not special restrictions
```php
<?php

namespace App;

class GenericClass<T, varType, myCoolLongParaterName> {
   private T $var1;
   private varType $var2;
   private myCoolLongParaterName $var3;   
}
```

### Several generic parameters support
```php
<?php

namespace App;

class Map<keyType, valueType> {
  
   private array $map;
  
   public function set(keyType $key, valueType $value): void {
       $this->map[$key] = $value;
   }
  
   public function get(keyType $key): ?valueType {
       return $this->map[$key] ?? null;
   }
}
```

### Default generic parameter support
```php
<?php

namespace App;

class Map<keyType = string, valueType = int> {
  
   private array $map = [];
  
   public function set(keyType $key, valueType $value): void {
       $this->map[$key] = $value;
   }
  
   public function get(keyType $key): ?valueType {
       return $this->map[$key] ?? null;
   }
}
```

```php
<?php

namespace App;

class Usage {
   public function run() {
       $map = new Map<>();//обязательно нужно добавить знаки "<>"
       $map->set('key', 1);
       var_dump($map->get('key'));
   }
}
```

### Where in class can generics be used?
* extends
* implements
* trait use
* property type
* method argument type
* method return type
* instanceof
* new
* class constants

An example of class that uses generics:
```php
<?php

namespace App;

use App\Entity\Cat;
use App\Entity\Bird;
use App\Entity\Dog;

class Test extends GenericClass<Cat> implements GenericInterface<Bird> {
 
  use GenericTrait<Dog>;
 
  private GenericClass<int>|GenericClass<Dog> $var;
 
  public function test(GenericInterface<int>|GenericInterface<Dog> $var): GenericClass<string>|GenericClass<Bird> {
      
       var_dump($var instanceof GenericInterface<int>);
      
       var_dump(GenericClass<int>::class);
      
       var_dump(GenericClass<array>::CONSTANT);
      
       return new GenericClass<float>();
  }
}
```

### Where in generic class can parameters be used?
* extends
* implements
* trait use
* property type
* method argument type
* method return type
* instanceof
* new
* class constants

And example of generic class:
```php
<?php

namespace App;

class Test<T,V> extends GenericClass<T> implements GenericInterface<V> {
 
  use GenericTrait<T>;
  use T;
 
  private T|GenericClass<V> $var;
 
  public function test(T|GenericInterface<V> $var): T|GenericClass<V> {
      
       var_dump($var instanceof GenericInterface<V>);
      
       var_dump($var instanceof T);
      
       var_dump(GenericClass<T>::class);
      
       var_dump(T::class);
      
       var_dump(GenericClass<T>::CONSTANT);
      
       var_dump(T::CONSTANT);
      
       $obj1 = new T();
       $obj2 = new GenericClass<V>();
      
       return $obj2;
  }
}
```

### How fast is it?
All concrete classes are pre-generated and can be cached(should not affect performance).

Generating many concrete classes should negatively impact performance when:
* resolves concrete classes;
* storing concrete classes in memory;
* type checking for each concrete class.

I think it's all individual for a specific case.

### Doesn't work without composer autoload

Autoload magic of concrete classes works with composer autoload only.
Nothing will not work because of syntax error if you include file by "require"
PhpUnit include test files by "require" only because of [its own reasons](https://github.com/sebastianbergmann/phpunit/issues/4039)
Therefore you can't use generic classes with PhpUnit.

### Reflection

PHP does type checks in [runtime](https://github.com/PHPGenerics/php-generics-rfc/issues/43).
Therefore, all generics arguments [must me available](https://github.com/PHPGenerics/php-generics-rfc/blob/cc7219792a5b35226129d09536789afe20eac029/generics.txt#L426-L430) through reflection in runtime.
It can't be, because information about generics arguments is erased after concrete classes are generated.

### IDE

* PhpStorm
  Doesn't support generic syntax because of [RFC](https://github.com/PHPGenerics/php-generics-rfc) is not complete yet.
  Doesn't have working [LSP plugin](https://plugins.jetbrains.com/plugin/10209-lsp-support). [LSP](https://en.wikipedia.org/wiki/Language_Server_Protocol) gives an opportunity to support different languages.
  Support of [Hack](https://hacklang.org)(which already support generics) [dropped](https://blog.jetbrains.com/phpstorm/2015/06/hack-language-support-in-phpstorm-postponed/).

* VSCode
  Support generics syntax after installation [Hack plugin](https://marketplace.visualstudio.com/items?itemName=pranayagarwal.vscode-hack)
  Doesn't have autocompletion

### Reflection
PHP выполняет проверки типов в [runtime](https://github.com/PHPGenerics/php-generics-rfc/issues/43). Значит, все аргументы дженериков [должны быть доступны](https://github.com/PHPGenerics/php-generics-rfc/blob/cc7219792a5b35226129d09536789afe20eac029/generics.txt#L426-L430) через reflection в runtime. А этого не может быть, потому что информация о аргументах дженериков после генерации конкретных классов стирается.

## What is Not Implemented According to the RFC

### Generics for Functions, Anonymous Functions, and Methods

```php
<?php

namespace App;

function foo<T,V>(T $arg): V {

}
```

### Type Checking for Generic Parameters

The type T must be a subclass of or implement the interface TInterface.
```php
<?php

namespace App;

class Generic<T: TInterface> {

}
```

### Variance of Parameters
```php
<?php

namespace App;

class Generic<in T, out V> {

}
```

Psalm Template Annotations
Features:
* does not change the language syntax;
* generics/templates are written using annotations;
* type checks are performed during static analysis using Psalm or supported IDEs.

## Existing Solutions in PHP

### [Psalm Template Annotations](https://psalm.dev/docs/annotating_code/templated_annotations/)

Features:
* does not change the language syntax;
* generics/templates are written using annotations;
* type checks are performed with static analysis using tools like [Psalm](https://psalm.dev) or supported IDEs.

```php
<?php
/**
* @template T
*/
class MyContainer {
 /** @var T */
  private $value;

/** @param T $value */
public function __construct($value) {
  $this->value = $value;
}

/** @return T */
public function getValue() {
  return $this->value;
}
}
```

### [spatie/typed](https://github.com/spatie/typed)

Features:
* does not change the language syntax;
* you can create a list with a specific type, but it cannot be used as a parameter type or return type of a function;
* type checks are performed at runtime.

```php
<?php

$list = new Collection(T::bool());

$list[] = new Post(); // TypeError
```  
```php
<?php

$point = new Tuple(T::float(), T::float());

$point[0] = 1.5;
$point[1] = 3;

$point[0] = 'a'; // TypeError
$point['a'] = 1; // TypeError
$point[10] = 1; // TypeError
```

### [TimeToogo/PHP-Generics](https://github.com/TimeToogo/PHP-Generics)

Features:
* does not change the language syntax;
* all instances of __TYPE__ are replaced with actual types, and based on this, specific classes are generated and saved to the file system;
* class replacement occurs during autoloading, and you need to use the built-in autoloader for this;
* type checks are performed at runtime.

```php
<?php

class Maybe {
private $MaybeValue;

public function __construct(__TYPE__ $Value = null) {
$this->MaybeValue = $Value;
}

public function HasValue() {
return $this->MaybeValue !== null;
}

public function GetValue() {
return $this->MaybeValue;
}

public function SetValue(__TYPE__ $Value = null) {
$this->MaybeValue = $Value;
}
}
```

```php
<?php

$Maybe = new Maybe\stdClass();
$Maybe->HasValue(); //false
$Maybe->SetValue(new stdClass());
$Maybe->HasValue(); //true
$Maybe->SetValue(new DateTime()); //ERROR
```

```php
<?php

$Configuration = new \Generics\Configuration();
$Configuration->SetIsDevelopmentMode(true);
$Configuration->SetRootPath(__DIR__);
$Configuration->SetCachePath(__DIR__ . '/Cache');
//Register the generic auto loader
\Generics\Loader::Register($Configuration);
```

### [ircmaxell/PhpGenerics](https://github.com/ircmaxell/PhpGenerics)

Features:
* a new syntax has been added;
* all instances of T are replaced with actual types, and based on this, specific classes are generated and loaded using eval();
* class replacement occurs during autoloading, and the built-in autoloader must be used for this;
* type checks are performed at runtime.

Test/Item.php
```php
<?php

namespace test;

class Item<T> {

protected $item;

public function __construct(T $item = null)
{
$this->item = $item;
}

public function getItem()
{
return $item;
}

public function setItem(T $item)
{
$this->item = $item;
}
}
```

Test/Test.php
```php
<?php

namespace Test;

class Test {
   public function runTest()
   {
       $item = new Item<StdClass>;
       var_dump($item instanceof Item); // true
       $item->setItem(new StdClass); // works fine
       // $item->setItem([]); // E_RECOVERABLE_ERROR
   }
}
```

test.php
```php
<?php

require "vendor/autoload.php";

$test = new Test\Test;
$test->runTest();
```

Differences from [mrsuh/php-generics](https://github.com/mrsuh/php-generics):
* specific classes are generated during autoload;
* specific classes are loaded using `eval()`;
* the standard `composer autoload` is overridden;
* the code was written a while ago, so it doesn't support the latest versions of PHP.

## Conclusion

I think I have achieved what I wanted: the library is easy to install and can be used in real projects. What is frustrating, however, is that, for understandable reasons, popular IDEs don't fully support the new generics syntax, so it's currently difficult to use it.
If you have suggestions or questions, feel free to leave them [here](https://github.com/mrsuh/php-generics/issues).
