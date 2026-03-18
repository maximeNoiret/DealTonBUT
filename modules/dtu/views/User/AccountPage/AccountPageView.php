<?php

namespace views\User\AccountPage;

use controllers\User\AccountPage\Account;
use core\views\AbstractView;
use models\AccountDB;
use views\Trade\Offer\Offer;

// WARN: maybe find a way to not access a model in a view?

class AccountPageView extends AbstractView
{

  /**
   * @description Method that give the path tp the corresponding .html
   * @return string
   */
    function path(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'AccountPage.html';
    }

    private ?string $error = null;
    public function setFlash(?string $error): void {
        $this->error = $error;
    }

  /**
   * @description Define value for each keys in the associated .html file
   * @return array<string,mixed> : The array that contain the real value that are associated by a key
   */
  function templateValues(): array
  {
      $photo = $_SESSION['profile_picture'] ?? 'account_pp.webp';
      if (empty($photo)) {
          $photo = 'account_pp.webp';
      }

      $flash = '';
      if($this->error !== null)
      {
          $flash = match ($this->error)
          {
              'file_too_large' => '<span class="error-text">Le fichier téléchargé est trop volumineux (max 5 Mo).</span>',
              'invalid_file_type' => '<span class="error-text">Type de fichier invalide (allowed: jpg, jpeg, png, webp, gif).</span>',
              'dbTransfer_failed' => '<span class="error-text">Une erreur est survenue lors de la mise à jour de la photo de profil dans la base de données.</span>',
              'invalid_file' => '<span class="error-text">Le fichier téléchargé est invalide.</span>',
              'update_success' => '<span class="success-text">Photo de profil mise à jour avec succès.</span>',
              default => '<span class="error-text">Une erreur inconnue s\'est produite.</span><br>' . htmlspecialchars($this->error)
          };
      }
      else
      {
          $flash = '';
      }

      return [
        'FLASH' => $flash,
        'USERNAME' => $_SESSION['username'] ?? '',
        'EMAIL' => $_SESSION['email'] ?? '',
        'USEROFFERS' => Account::getUserOffers(),
        'USERBALANCE' => $_SESSION['balance'] ?? 0,
        'USERBOUGHTOFFERS' => Account::getUserBoughtOffers(),
        'NAME' => \models\Account::getName(),
        'CURRENT_PHOTO' => $photo
        ];
  }

  /**
   * @description Contain the title of the page, that will be shown on the navbar
   * @return string
   */
  function navbarText(): string {
    return 'Mon compte';
  }
}