<?php

namespace M2Max\ImpRouter;

use Composer\Script\Event;

class ImpCli
{
  private static $VENDOR = 'src';

  public static function install() {
    if(Config::$ENV == 'dev')
      self::$VENDOR  = 'src';
    else {
      self::$ENV = 'vendor';
    }

    echo "\n";
    echo '[[ Install ImpRouter ]]';
    $root_path = substr(__DIR__, 0, strrpos(__DIR__, self::$VENDOR)+count(self::$VENDOR)-1);

    echo "\n";
    echo ' --> Initialize .htaccess';
    if(!copy(dirname(__DIR__).'/.htaccess.default', $root_path.'.htaccess')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }

    echo "\n";
    echo ' --> Initialize index.php';
    if(!copy(dirname(__DIR__).'/index.default.php', $root_path.'index.php')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }

    echo "\n";
    echo ' --> Initialize route.json';
    if(!copy(dirname(__DIR__).'/route.default.json', $root_path.'route.json')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }
  }

  public static function example() {
    $root_path = substr(__DIR__, 0, strrpos(__DIR__, self::$VENDOR)+count(self::$VENDOR)-1);

    if(!file_exists($root_path.'app') && !mkdir($root_path.'app')) {
      throw new \Exception("Unable to create app directory, check write permissions");
    }

    if(!copy(dirname(__DIR__).'/example/app/DefaultController.php', $root_path.'app/DefaultController.php')) {
      throw new \Exception("Unable to create DefaultController file, check write permissions");
    }

    $json = json_decode(file_get_contents($root_path.'route.json'), true);
    $json['root_path'] = '/app';
    $json['route']["/example"] = [
      "controller" => "\\MyDefaultNamespace\\DefaultController",
      "action" => "index",
      "method" => "get"
    ];
    file_put_contents($root_path.'route.json', json_encode($json));
  }

  public static function reset(Event $event) {
    $root_path = substr(__DIR__, 0, strrpos(__DIR__, self::$VENDOR)+count(self::$VENDOR)-1);

    if(file_exists($root_path.'route.json') && !unlink($root_path.'route.json')) {
      throw new \Exception("Unable to remove route.json, check write permissions");
    }

    if(file_exists($root_path.'index.php') && !unlink($root_path.'index.php')) {
      throw new \Exception("Unable to remove index.php, check write permissions");
    }

    if(file_exists($root_path.'.htaccess') && !unlink($root_path.'.htaccess')) {
      throw new \Exception("Unable to remove .htaccess, check write permissions");
    }

    if(file_exists($root_path.'app')) {
      if(self::confirmRemovePath()) {
        self::rrmdir($root_path.'app');
      }
    }

    if(count($event->getArguments()) > 0 && $event->getArguments()[0] == 'noinstall')
      return;

    self::install();
  }

  private static function rrmdir($src) {
    $dir = opendir($src);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                rrmdir($full);
            }
            else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
  }

  private static function confirmRemovePath() {
    echo "Do you want remove app directory ? [n/y]: ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim(strtolower($line)) == 'y') {
      return true;
    }
    else if(trim($line) == 'n') {
      return false;
    }
    else {
      echo 'Are you kidding ? Y or N !!';
      echo "\n";
      return self::confirmRemovePath();
    }
  }
}
