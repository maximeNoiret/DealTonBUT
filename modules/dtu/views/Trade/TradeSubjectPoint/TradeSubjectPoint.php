<?php

namespace views\Trade\TradeSubjectPoint;

use core\views\AbstractView;
use models\DataBase;

class TradeSubjectPoint extends AbstractView {
  function path(): string {
    
    return __DIR__ . DIRECTORY_SEPARATOR . 'TradeSubjectPointTemplate.html';
  }

  function templateValues(): array {
    $db = DataBase::getInstance();
    /**
     * @var string $email
     */
    $email = $_SESSION['email'] ?? '';
    
    $subjectsRows = $db->getSubject($email);
    $subjects = [];
    foreach ($subjectsRows as $row) {
      /**
       * @var array<string, mixed> $row
       */
      $subjects[] = $row['subject_name'];
    }

    $flash = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      /**
       * @var string $from
       */
      $from = $_POST['from_subject'];
      /**
       * @var string $to
       */
      $to = $_POST['to_subject'];
      /**
       * @var string|float|int|bool $pointsValue
       */
      $pointsValue = $_POST['points'];
      $points = floatval($pointsValue);

      if ($from == $to) {
        $flash = '<div class="flash error">Tu dois choisir deux matières différentes.</div>';
      } else {
        $available = $db->getPoints($email, $from);
        if ($available < $points) {
          $flash = '<div class="flash error">Tu n\'as pas assez de points dans cette matière.</div>';
        } else {
          $db->transferPoints($email, $points, $from, $to);
          $flash = '<div class="flash success">Transfert réussi !</div>';
        }
      }
    }

    $fromOptions = '';
    $toOptions = '';
    
    foreach ($subjects as $subject) {
      /**
       * @var string $subject
       */
      $pts = $db->getPoints($email, $subject);
      $option = '<option value="' . $subject . '">' . $subject . ' (' . $pts . ' pts)</option>';
      $fromOptions .= $option;
      $toOptions .= $option;
    }

    return [
      'FROM_OPTIONS' => $fromOptions,
      'TO_OPTIONS' => $toOptions,
      'FLASH' => $flash,
    ];
  }

  function navbarText(): string {
    return 'Échanger Points';
  }
}