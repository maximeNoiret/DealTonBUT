<?php

namespace dtu\views\User\AdminPanel;
use core\views\AbstractSubView;

class AccountAdminPanel extends AbstractSubView
{

  /**
   * @var string : The path to the .html file associated.
   */
  const string PATH = __DIR__ . DIRECTORY_SEPARATOR . 'AccountAdminPanel.html';

  /**
   * @description Constructor of the AccountAdminPanel, used to obtain the
   * information of the account as a class attribute.
   * @param array<string,string> $accountInfo The information of the account.
   */
  function __construct(private readonly array $accountInfo){
  }

  /**
   * @description Give the path to the .html file associated.
   * @return string The path to the .html file associated.
   */
  function path(): string
  {
    return self::PATH;
  }

  /**
   * @description Define value for each keys in the associated .html file.
   * @return array<string, string> The array that contain the real value that are associated by a key.
   * Being the email, the role and the balance of the user.
   */
  function templateValues(): array
  {
    return [
      'EMAIL_USER' => $this->accountInfo['email'],
      'ROLE' => $this->accountInfo['role'],
      'BALANCE' => $this->accountInfo['balance'],
    ];
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @note Since this is a subview, it doesn't have to show anything on the navbar, so we return an empty string
   * @return string The title of the page, that will be shown on the navbar
   */
  function navbarText(): string
  {
    return '';
  }
}