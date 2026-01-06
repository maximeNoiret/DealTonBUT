<?php

namespace views\Trade\TradeSubjectPoint;

use core\views\AbstractView;
use dtu\models\SubjectDB;
use models\AccountDB;

class TradeSubjectPointView extends AbstractView {

    private $flash = '';
    private $subjectsRows = [];

    public function setData($flash, $subjectsRows) {
        $this->flash = $flash;
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

        $flash = $this->flash;
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
            'FLASH'        => $flash
        ];
    }

    function navbarText(): string {
        return 'Échanger Points';
    }
}