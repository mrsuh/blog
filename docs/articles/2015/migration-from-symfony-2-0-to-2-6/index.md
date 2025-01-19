# Migration from Symfony 2.0 to 2.6

[origin]https://habr.com/ru/articles/258403

This article explains the steps and challenges faced during the migration of a project from the outdated Symfony 2.0 to Symfony 2.6.

### Dependency Manager

Every Symfony project relies on dependencies. In Symfony 2.0, dependencies were managed using a deps file and installed with this command:
```bash
 php bin/vendors install
```
Now, the recommended tool for dependency management is Composer.

To start, download Composer into your project root:
```bash
php -r "readfile('https://getcomposer.org/installer');" | php
```

Create a composer.json file in the project root using the example from Symfony 2.6:
[Symfony 2.6 Composer File](https://github.com/symfony/symfony/blob/2.6/composer.json)

Run this command to install dependencies:
```bash
php composer.phar update
```

This will download Symfony 2.6 and its dependencies, creating a `composer.lock` file to track the versions used. 
After this, add any additional dependencies you need by either editing `composer.json` directly:
```php
"require": {
        "{dependency_name}": "{dependency_version}"
},
```

Or use the Composer command:
```bash
php composer.phar require {dependency_name}:{dependency_version}
```

With everything installed, you can now remove the old `deps` and `deps.lock` files. Additionally, update the `web/app.php` and `web/app_dev.php` files for compatibility:
`app.php`
```php
<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

`app_dev.php`
```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
|| isset($_SERVER['HTTP_X_FORWARDED_FOR'])
|| !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
header('HTTP/1.0 403 Forbidden');
exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

### Parameters

In Symfony 2.0, parameters were stored in the file `app/config/parameters.ini`. To update for Symfony 2.6, rename this file to `app/config/parameters.yml` and convert it to yaml format:

Before:
```php
[parameters]
locale = en
```
After:
```php
parameters:
locale: en
```

Next, update the `app/config/config.yml` file to include the new parameters file:
```php
imports:
    - { resource: parameters.yml }
```
The parameters file is now updated and connected.

### Backward Compatibility of Functions

Between Symfony 2.0 and 2.6, many changes occurred. Here are some key updates:

#### Forms

Some functions now throw exceptions if used on an already submitted form:
```php
add(), remove(), setParent(), bind() and setData()
```

You can call these functions in a listener in the formBuilder before the form is submitted. Alternatively, you can adjust your form logic. As a temporary fix, you can use this code:
```php
$formData = $form->getData();
$form = $this->createForm(new YourForm());
$form->setData($formData);
```
By recreating the form and passing its data back, you can safely use these functions.

#### Form Validation

The `CallbackValidation` class is no longer available in `FormBuilder`. Instead, use an `EventListener` with the `POST_SUBMIT` event after the form is submitted.

Before:
```php
$builder->addValidator(new CallbackValidator(function (FormInterface $form) {
$value = $form['date']->getData();
if ($value != null) {
$form['date']->addError(new FormError('Введена некорректная дата'));
}
}));
```

After:
```php
$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
$form = $event->getForm();
$value = $form['date']->getData();
if ($value != null) {
$form['date']->addError(new FormError('Введена некорректная дата'));
}
});
```

#### Adding Custom Options to Form Fields

When adding custom options to form fields (e.g., in `MyBundle\Forms\ExtensionExtensionForm.php`), you must specify the field type the options apply to:
For text fields only:
```php
public function getExtendedType()
{
    return 'text';
}
```

For all field types:
```php
public function getExtendedType()
{
    return 'form';
}
```

### Complete List of Changes

* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.1.md
* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.2.md
* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.3.md
* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.4.md
* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.5.md
* https://github.com/symfony/symfony/blob/2.7/UPGRADE-2.6.md

Symfony Official documentation: http://symfony.com/doc/current/cookbook/index.html
