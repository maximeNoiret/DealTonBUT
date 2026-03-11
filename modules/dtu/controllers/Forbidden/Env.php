<?php

namespace controllers\Forbidden;

use controllers\Main;
use core\controllers\Controller;
use Exception;
use \views\Forbidden;

class Env implements Controller
{

  const string PATH = '/.env';

    /**
     * @throws Exception
     */
    function control(): void
  {
    echo new Forbidden()->render('Forbidden - DealTonBUT', Main::STYLESHEET);
  }

  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH;
  }
}