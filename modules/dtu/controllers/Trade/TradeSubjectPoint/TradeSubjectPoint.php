<?php

namespace controllers\Trade\TradeSubjectPoint;

use core\controllers\Controller;
use views\Trade\TradeSubjectPoint\TradeSubjectPointView as TradeSubjectPointView;
use dtu\models\SubjectDB;

class TradeSubjectPoint implements Controller {

    public const PATH = '/trade/points';
    public const METH = 'GET';

    const STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/TradeSubjectPoints.css',
        '/_assets/styles/navbar.css'
    ];

    function control(): void {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
        } else {

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

                        foreach ($subjects as $subject) {
                            $rand_point = rand(0, 20);
                            $db->insertSubjectSafe($email, $subject, $rand_point);
                        }

                        $flash = '<div class="flash success">Matières importées avec succès.</div>';
                    }
                }
            }

            $subjectsRows = $db->getSubject($email);
            $view = new TradeSubjectPointView();
            $view->setData($flash, $subjectsRows);
            echo $view->render("Échanger Points - DealTonBUT", static::STYLESHEET);
        }
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === static::PATH && ($meth === 'GET' || $meth === 'POST');
    }
}