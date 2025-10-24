<?php

namespace controllers\User\PasswordForgot;

use core\controllers\Controller;
use views\User\ForgotPassword\ForgotPasswordView;
//use views\User\ForgotPasswordView;

class PasswordForgot implements Controller
{
  const string PATH = '/user/forgot';
  const string METH = 'GET';
  /**
   * @var array<string> STYLESHEET
   */
    const array STYLESHEET = [
        '/_assets/styles/loginSingnin.css',
        '/_assets/styles/style.css'
    ];

    /**
   * @return void
   * @description Control the forgot password page view rendering.
   */
  function control(): void
  {
    echo (new ForgotPasswordView())->render("Forgot Password - DealTonBUT", self::STYLESHEET);
  }

  /**
   * @description Resolve the path and method to access the Forgot Password page
   * @param string $path
   * @param string $meth
   * @return bool
   */
  static function resolve(string $path, string $meth): bool
  {
    return $path === self::PATH && $meth === self::METH;
  }
}
