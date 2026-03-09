<?php

namespace models;

use core\models\DataBase;
use dtu\models\TradeDB;
use exceptions\AccountAlreadyExists;
use PDO;
use views\Trade\SeeOtherAccount\SeeUserOfferView;

class AccountDB extends DataBase {

  protected static $instance;

  public static function ownsOffer(mixed $email, int $param): bool
  {
    $dbConn = self::getInstance()->dbConn;
    $query = $dbConn->prepare(
      'SELECT ouid FROM offer WHERE owner = :email AND ouid = :ouid');
    $query->bindValue('email', $email);
    $query->bindValue('ouid', $param);
    $query->execute();
    return $query->fetch() !== false;
  }

  /**
   * @description Registers a new account in the database.
   * @param string $username The desired username for the new account.
   * @param string $email The email address associated with the new account.
   * @return void
   */
  public function registerAccount (
    string $username,
    string $email,
    string $role
  ): void {
    //$hashedpwd = password_hash($password, PASSWORD_DEFAULT);
    $query = $this->dbConn->prepare(
      'REPLACE INTO user_(email, username)
      VALUES (:email, :username)');

    $this->setRole($email, $role);
    $query->bindValue('email', $email);
    $query->bindValue('username', $username);
    $query->execute();
  }

  /**
   * @param string $email User whose role to set
   * @param string $role Role to set
   * @return bool True on success, false on failure
   */
  public function setRole(string $email, string $role): bool {
    $query = $this->dbConn->prepare(
      'UPDATE user_ SET role = :role WHERE email = :email');
    $query->bindValue('email', $email);
    $query->bindValue('role', $role);
    return $query->execute();
  }

  /**
   * @description Checks if an account with the given email exists.
   * @param string $email The email address to check.
   * @return bool True if the account exists, false otherwise.
   */
  public function accountExists(string $email): bool {
    $query = $this->dbConn->prepare('SELECT email FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== false;
  }

  /**
   * @description Retrieves account information for the given email and password.
   * @param string $email The email address of the account.
   * @param string $password The password of the account.
   * @return bool|array<string, string>
   */
  public function getAccount(string $email, string $password): bool|array {
    $query = $this->dbConn->prepare('
      SELECT username, email, hashedpwd, balance 
      FROM user_
      WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();

    /**
     * @var array<string, string> $user
     */
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return false;
    }

    // Verify password against stored hash
    if (password_verify($password, $user['hashedpwd'])) {
        // Return user data WITHOUT the password hash
        return [
            'username' => $user['username'],
            'email' => $user['email'],
            'balance' => $user['balance']
        ];
    }
    return false;
  }

  /**
   * @description Retrieves the balance for the given email.
   * @param string $email The email address of the account.
   * @return int|string|false|null The balance amount, or false/null if not found.
   */
  public function getBalance(string $email): int|string|false|null {
    $query = $this->dbConn->prepare('
      SELECT balance FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetchColumn();
  }

  public function setBalance(string $email, float $balance): bool {
      $query = $this->dbConn->prepare(
          'UPDATE user_ SET balance = :balance WHERE email = :email');
      $query->bindValue('email', $email);
      $query->bindValue('balance', $balance);
      return $query->execute();
    }
    /**
     * @description Checks if a password reset has already been requested for the given email.
     * @param string $email The email address to check.
     * @return bool True if a password reset has already been requested, false otherwise.
     */
  public function alreadyForgotPassword(string $email): bool {
    $query = $this->dbConn->prepare(
      'SELECT email FROM token WHERE email = :email AND deadline > CURRENT_TIMESTAMP');
    $query->bindValue('email', $email);
    $query->execute();
    return $query->fetch() !== false;
  }

  /**
   * @description Updates the password for the given email.
   * @param string $email The email address of the account.
   * @param string $hashedPassword The new hashed password.
   * @return bool True on success, false on failure.
   */
  public function updatePassword(string $email, string $hashedPassword): bool
  {
    $query = $this->dbConn->prepare(
      'UPDATE user_ SET hashedpwd = :hashedpwd WHERE email = :email');
    $query->bindValue('hashedpwd', $hashedPassword);
    $query->bindValue('email', $email);
    return $query->execute();
  }

  /**
   * @param string $email The email address associated with the token.
   * @param string $token The password reset token.
   * @return bool True if the token is valid for the given email, false otherwise.
   */
  public function checkToken(string $email, string $token): bool {
    $query = $this->dbConn->prepare(
      'SELECT email FROM token WHERE email = :email AND token = :token AND deadline > CURRENT_TIMESTAMP');
    $query->bindValue('email', $email);
    $query->bindValue('token', $token);
    $query->execute();
    $returnValue = $query->fetch() !== false;

    if ($returnValue) {
      // delete the token after use
      $query = $this->dbConn->prepare(
        'DELETE FROM token WHERE email = :email');
      $query->bindValue('email', $email);
      $query->execute();
    }

    return $returnValue;
  }

    /**
     * @description Inserts a password reset token for the given email.
     * @param string $email The email address associated with the token.
     * @param string $token The password reset token.
     * @return void
     */
  public function insertToken(string $email, string $token): void {
    $query = $this->dbConn->prepare('INSERT INTO token(email, token) VALUES (:email, :token)');
    $query->bindValue('email', $email);
    $query->bindValue('token', $token);
    $query->execute();
  }


    /**
     * @description Deletes a user from the database.
     * @param string $email The email address of the user to delete.
     * @return void
     */
  public function deleteUser(string $email): void {
    $query = $this->dbConn->prepare('DELETE FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
  }

  /**
   * @description Updates the SESSION balance of the user.
   * @param string $email The email address of the user.
   * @return void
   */
  public function updateBalance(string $email): void {
    $balance = $this->getBalance($email);
    if ($balance) {
      $_SESSION['balance'] = $balance;
    }
  }

  /**
   * @param string $email The email address of the user.
   * @return bool True if the account is verified, false otherwise.
   */
  public function isAccountVerified(string $email): bool {
    $query = $this->dbConn->prepare('SELECT role FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    /**
     * @var array<string, string>|false $result
     */
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result && $result['role'] != 'not-verified';
  }

  /**
   * @description Retrieves the email associated with a given token.
   * @param string $token The token
   * @return string The email associated with the token.
   */
  public function getEmailFromToken(string $token): string
  {
    $query = $this->dbConn->prepare(
      'SELECT email FROM token WHERE token = :token');
    $query->bindValue('token', $token);
    $query->execute();
    return (string) $query->fetchColumn();
  }

  /**
   * @description Retrieves the username associated with a given email.
   * @param $email string email address of the user
   * @return string The username associated with the given email.
   */
  public function getUserUsername(string $email): string
  {
    $query = $this->dbConn->prepare(
      'SELECT username FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    $res = $query->fetch();
    return $res['username'];
  }

  /**
   * @description Retrieves the role of a user.
   * @param string $email The email address of the user.
   * @return string The role associated with the given email, or an empty string if not found.
   */
  public function getRole(string $email): string
  {
    $query = $this->dbConn->prepare('SELECT role FROM user_ WHERE email = :email');
    $query->bindValue('email', $email);
    $query->execute();
    /**
     * @var array<string, string>|false $result
     */
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['role'] : '';
  }

  /**
   * @description Checks if the user associated with the given email has an admin role.
   * @param string $email The email address of the user.
   * @return bool True if the user is an admin, false otherwise.
   */
  public function isAdmin(string $email): bool {
    $role = $this->getRole($email);
    if ($role === 'admin') {

      return true;
    }
    else {
      return false;
    }
  }

  /**
   * @description Return all the account and their associated information.
   * @return array An array containing all the accounts, each account is
   * represented as an associative array with keys corresponding to the database
   * columns, the keys are email, username, hashed password, balance and role.
   */
  public function getAllAccount(): array {
    $query = $this->dbConn->prepare('SELECT * FROM user_');
    $query->execute();
    return $query->fetchAll();
  }

  /**
   * @description Return the html code to display all the accounts and their associated information.
   * @param array $accounts An array containing all the accounts it is obtained by the getAllAccount().
   * @return string The HTML code to display the email, role and balance of all
   * the accounts, as well as a delete button for each account.
   */
  public function getAllAccountHtml(array $accounts): string {
    //TODO : use the view AccountAdminPanelView instead of directly writing the html in this method
    $html = '<section class ="manage-account-panel">'."\n";
    foreach ($accounts as $account) {
      $html .='<article class="account-manage">'."\n";
      $html .='<p class="account-manage-element"><a href="/account/see?email='.$account['email'].'">'.$account['email'].'</a></p>'."\n";
      $html .='<p class="account-manage-element">'.$account['role'].'</p>'."\n";
      $html .='<p class="account-manage-element">'.$account['balance'].'</p>'."\n";

//      $html .='<a class="button-delete">{DELETE}</a>'."\n";

      $html .=
            '<form method="POST" action="/admin$delete-account='.$account['email'].'"" onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer définitivement votre compte ?\');">'
            .'<button type="submit" class="button-delete">DELETE</button>'."\n".'</form>'."\n";

      $html .='</article>'."\n";
    }
    $html .='</section>'."\n";

    return $html;
  }
}



