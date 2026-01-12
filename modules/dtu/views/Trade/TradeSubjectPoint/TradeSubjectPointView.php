<?php

namespace views\Trade\TradeSubjectPoint;

use core\views\AbstractView;
use dtu\models\SubjectDB;

class TradeSubjectPointView extends AbstractView {

    private ?string $error = null;
    private $subjectsRows = [];
    private int $balance = 0;

    public function setData(?string $error, array $subjectsRows, int $balance): void {
        $this->error = $error;
        $this->subjectsRows = $subjectsRows;
        $this->balance = $balance;
    }

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'TradeSubjectPointTemplate.html';
    }

    function templateValues(): array {
        $db = SubjectDB::getInstance();
        $email = $_SESSION['email'] ?? '';

        $error = '';

        if ($this->error !== null)
        {
            $error = match ($this->error) {
                'error_invalid_file_type' => '<span class="error-text">Type de fichier invalide. Veuillez télécharger un fichier ICS.</span>',
                'error_file_too_large' => '<span class="error-text">Le fichier téléchargé est trop volumineux (max 2 Mo).</span>',
                'error_same_subject' => '<span class="error-text">Vous ne pouvez pas échanger des points entre la même matière.</span>',
                'error_insufficient_points' => '<span class="error-text">Points insuffisants pour effectuer l\'échange.</span>',
                'error_exceed_max_points' => '<span class="error-text">L\'échange dépasserait le maximum de 20 points dans la matière cible.</span>',
                'success_transfer' => '<span class="success-text">Échange de points effectué avec succès.</span>',
                'error_upload' => '<span class="error-text">Erreur lors du téléchargement du fichier ICS.</span>',
                'success_import' => '<span class="success-text">Importation des matières réussie.</span>',
                default => '<span class="error-text">Une erreur inconnue s\'est produite.</span><br>' . htmlspecialchars($this->error)
            };
        }



        $subjectsRows = $this->subjectsRows;
        $fromOptions = '';
        $toOptions = '';

        $balance = '<option value="DTC_BALANCE">DTC Balance (' . htmlspecialchars((string)$this->balance) . ' pts)</option>';
        $fromOptions .= $balance;
        $toOptions .= $balance;

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