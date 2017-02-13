# ImpRouter
ImpRouter is a PHP library which allow to include simple routing system.

## Installation ##
With Composer, to include this library into your dependencies, you need to require 2max/improuter:

    $ composer require 2max/improuter

Then, you can install files automatically (recommanded with project from scratch).
Add this lines in your composer.json

    "scripts": {
      "imp:install": "M2Max\\ImpRouter\\ImpCli::install"
    }

And execute this command :

    $ composer imp:install

If you wont use this way, you should create JSON files. You can found example in src/route.default.json.
*You can named your files as you want.*

*Example: route.json*

    {
      "root_path": "/",
      "route": {}
    }

Then, you should instanciate ImpRouter class in index.php :

    <?php
    require __DIR__.'/vendor/autoload.php';

    try {
      new M2Max\ImpRouter\ImpRouter('route.json');
    }
    catch(Exception $e) {
      echo 'Error :: '.$e->getMessage();
    }

## Getting Started ##
You should set your json files (*route.json*) for add routes.

*Example :*

    {
      "root_path": "/",
      "route": {
      	"/": {
      		"method": "get",
      		"controller": "\\Test",
      		"action": "test"
      	}
    }

*"root_path":* directory where are your source code
*"controller"*: name of your class
*"action":* name of your method

Then, think to create class and method and let's go !
