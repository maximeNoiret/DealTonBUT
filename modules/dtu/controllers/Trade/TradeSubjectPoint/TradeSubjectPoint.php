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
            $error = null;

            //Point Transfer
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formType = $_POST['form_type'] ?? '';

                if ($formType === 'points_transfer') {

                    $from = $_POST['from_subject'] ?? '';
                    $to = $_POST['to_subject'] ?? '';
                    $points = floatval($_POST['points'] ?? 0);

                    if ($from === $to) {
                        $error = 'error_same_subject';
                    } else {
                        $availableFrom = $db->getPoints($email, $from);
                        $availableTo = $db->getPoints($email, $to);

                        if ($availableFrom < $points) {
                            $error = 'error_insufficient_points';
                        }
                        elseif (($availableTo + $points) > 20) {
                            $error = 'error_exceed_max_points';
                        }

                        else {
                            $db->transferPoints($email, $points, $from, $to);
                            $error = 'success_transfer';
                        }
                    }
                }

                //ICS Import
                if ($formType === 'ics_import') {

                    if (!isset($_FILES['ics_file']) || $_FILES['ics_file']['error'] !== 0) {
                        $error = 'error_upload';
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

                        $error = 'success_import';
                    }
                }
            }

            $subjectsRows = $db->getSubject($email);
            $view = new TradeSubjectPointView();
            $view->setData($error, $subjectsRows);
            echo $view->render("Échanger Points - DealTonBUT", static::STYLESHEET);
        }
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === static::PATH && ($meth === 'GET' || $meth === 'POST');
    }
}