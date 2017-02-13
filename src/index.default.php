<?php

require __DIR__.'/vendor/autoload.php';

use M2Max\ImpRouter\ImpRouter;

try {
  new ImpRouter('route.json');
}
catch(Exception $e) {
  echo 'Error :: '.$e->getMessage();
}
