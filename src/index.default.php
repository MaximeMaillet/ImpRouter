<?php

require __DIR__.'/vendor/autoload.php';

use M2Max\ImpRouter\ImpRouter;

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
  ImpRouter::init('route.json');
}
catch(Exception $e) {
  echo 'Error :: '.$e->getMessage();
}
