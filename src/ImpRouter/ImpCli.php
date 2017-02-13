<?php

namespace M2Max\ImpRouter;

use Composer\Script\Event;

class ImpCli
{
  private static $VENDOR = 'src';

  public static function postInstall(Event $event) {
    if(Config::$ENV == 'dev')
      self::$VENDOR  = 'src';
    else {
      self::$ENV = 'vendor';
    }


    $root_path = substr(__DIR__, 0, strrpos(__DIR__, self::$VENDOR)+count(self::$VENDOR)-1);

    if(!copy(dirname(__DIR__).'/.htaccess.default', $root_path.'.htaccess')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }

    if(!copy(dirname(__DIR__).'/index.default.php', $root_path.'index.php')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }

    if(!copy(dirname(__DIR__).'/route.default.json', $root_path.'route.json')) {
      throw new \Exception("Unable to copy htaccess file, check write permissions");
    }
  }
}
