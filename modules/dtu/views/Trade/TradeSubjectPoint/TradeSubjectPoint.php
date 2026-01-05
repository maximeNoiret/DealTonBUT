<?php

namespace views\Trade\TradeSubjectPoint;

use core\views\AbstractView;
use dtu\models\SubjectDB;
use models\AccountDB;

class TradeSubjectPoint extends AbstractView {

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'TradeSubjectPointTemplate.html';
    }

  /**
   * @throws Exception
   */
  function templateValues(): array {
        $db = SubjectDB::getInstance();
        $email = $_SESSION['email'] ?? '';
        $flash = '';

        //Point Transfer
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formType = $_POST['form_type'] ?? '';

            if ($formType === 'points_transfer') {

                $from = $_POST['from_subject'] ?? '';
                $to = $_POST['to_subject'] ?? '';
                $points = floatval($_POST['points'] ?? 0);

                if ($from === $to) {
                    $flash = '<div class="flash error">Tu dois choisir deux matières différentes.</div>';
                } else {
                    $available = $db->getPoints($email, $from);

                    if ($available < $points) {
                        $flash = '<div class="flash error">Tu n\'as pas assez de points.</div>';
                    } else {
                        $db->transferPoints($email, $points, $from, $to);
                        $flash = '<div class="flash success">Transfert réussi.</div>';
                    }
                }
            }

            //ICS Import
            if ($formType === 'ics_import') {

                if (!isset($_FILES['ics_file']) || $_FILES['ics_file']['error'] !== 0) {
                    $flash = '<div class="flash error">Erreur lors de l\'upload du fichier.</div>';
                } else {

                    $subjects = [];
                    $lines = file($_FILES['ics_file']['tmp_name']);

                    foreach ($lines as $line) {
                        if (strpos($line, 'SUMMARY:') === false) {
                            continue;
                        }
                        $parts = explode(':', $line, 2);
                        if (!isset($parts[1])) {
                            continue;
                        }
                        $subject = trim($parts[1]);
                        if (!preg_match('/[SR]/', $subject)) {
                            continue;
                        }
                        $subjects[] = $subject;
                    }

                    // Suppression des doublons
                    $subjects = array_unique($subjects);

                    // Insertion en base
                    foreach ($subjects as $subject) {
                        $db->insertSubjectSafe($email, $subject, 0);
                    }

                    $flash = '<div class="flash success">Matières importées avec succès.</div>';
                }
            }
        }

        $subjectsRows = $db->getSubject($email);
        $fromOptions = '';
        $toOptions = '';

        foreach ($subjectsRows as $row) {
            $subject = $row['subject_name'];
            $pts = $db->getPoints($email, $subject);

            $option = '<option value="' . htmlspecialchars($subject) . '">' .
                htmlspecialchars($subject) . ' (' . $pts . ' pts)</option>';

            $fromOptions .= $option;
            $toOptions .= $option;
        }

        return [
            'FROM_OPTIONS' => $fromOptions,
            'TO_OPTIONS'   => $toOptions,
            'FLASH'        => $flash
        ];
    }

    function navbarText(): string {
        return 'Échanger Points';
    }
}
