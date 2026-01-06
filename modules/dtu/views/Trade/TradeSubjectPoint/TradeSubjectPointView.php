<?php

namespace views\Trade\TradeSubjectPoint;

use core\views\AbstractView;
use dtu\models\SubjectDB;
use models\AccountDB;

class TradeSubjectPointView extends AbstractView {

    private ?string $error = null;
    private $subjectsRows = [];

    public function setData(?string $error, $subjectsRows) {
        $this->error = $error;
        $this->subjectsRows = $subjectsRows;
    }

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'TradeSubjectPointTemplate.html';
    }

    /**
     * @throws Exception
     */
    function templateValues(): array {
        $db = SubjectDB::getInstance();
        $email = $_SESSION['email'] ?? '';

        $error = '';

        if ($this->error !== null)
        {
            $error = match ($this->error) {
                'error_same_subject' => '<span class="error-text">Vous ne pouvez pas échanger des points entre la même matière.</span>',
                'error_insufficient_points' => '<span class="error-text">Points insuffisants pour effectuer l\'échange.</span>',
                'success_transfer' => '<span class="success-text">Échange de points effectué avec succès.</span>',
                'error_upload' => '<span class="error-text">Erreur lors du téléchargement du fichier ICS.</span>',
                default => '<span class="error-text">Une erreur inconnue s\'est produite.</span><br>' . htmlspecialchars($this->error)
            };
        }


        $subjectsRows = $this->subjectsRows;

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
            'FLASH'        => $error
        ];
    }

    function navbarText(): string {
        return 'Échanger Points';
    }
}