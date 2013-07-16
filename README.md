ZendCart
============================
Version 1.0

This model allows you to manage a shopping cart for e-commerce in an easy, simple and fast.

Installation
------------
For the installation uses composer [composer](http://getcomposer.org "composer - package manager").

```sh
php composer.phar require  zendcart/zendcart:dev-master
```

Add this project in your composer.json:

    ```json
    "require": {
        "zendcart/zendcart": "dev-master"
    }
    ```

Post Installation
------------
Configuration:
- Add the module of `config/application.config.php` under the array `modules`, insert `ZendCart`
- Create a file named `zendcart.global.php` under `config/autoload/`. 
- Add the following lines to the file you just created:

```php
<?php
return array(
    'zendcart' => array(
        'vat'  => 22
    ),
);
```

Contributors
=====================================

* Concetto Vecchio - info@cvsolutions.it
