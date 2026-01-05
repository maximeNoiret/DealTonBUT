<?php

namespace controllers\Trade\TradeSubjectPoint;

use core\controllers\Controller;
use views\Trade\TradeSubjectPoint\TradeSubjectPoint as TradeSubjectPointView;

class TradeSubjectPoint implements Controller {
  
  public const PATH = '/trade/points';
  public const METH = 'GET';

  const array STYLESHEET = [
    '/_assets/styles/style.css',
    '/_assets/styles/TradeSubjectPoints.css',
    '/_assets/styles/navbar.css'
  ];
  
  function control(): void {
    if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
      header('Location: /user/login');
    } else {
      /**
       * @var array<string> $stylesheet
       */
      $stylesheet = static::STYLESHEET;
      echo (new TradeSubjectPointView())->render("Échanger Points - DealTonBUT", $stylesheet);
    }
  }

  public static function resolve(string $path, string $meth): bool {
    return $path === static::PATH && ($meth === 'GET' || $meth === 'POST');
  }
}
