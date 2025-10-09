<?php

namespace controllers;

use controllers\Controller;

class MarketPlace implements Controller {
  
  private const PATH = '/marketplace';
  private const METH = 'GET';
  
  function control(): void {
    if ($_SESSION['logged-in'] == null || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    }
  } 

  static function resolve(string $path, string $meth): bool {
    return $path === self::PATH && $meth === self::METH;
  }


}
