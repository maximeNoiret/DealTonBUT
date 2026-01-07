<?php

namespace controllers\Trade\TradeSubjectPoint;

use core\controllers\Controller;
use views\Trade\TradeSubjectPoint\TradeSubjectPointView as TradeSubjectPointView;
use dtu\models\SubjectDB;
use models\AccountDB;


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

            $dbSubject = SubjectDB::getInstance();
            $email = $_SESSION['email'] ?? '';
            $error = null;

            $dbBalance = AccountDB::getInstance();


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
                        $availableFrom = ($from === 'DTC_BALANCE') ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $from);
                        $availableTo   = ($to === 'DTC_BALANCE')   ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $to);


                        if ($availableFrom < $points) {
                            $error = 'error_insufficient_points';
                        }
                        elseif (($availableTo + $points) > 20) {
                            $error = 'error_exceed_max_points';
                        }

                        else {
                            if ($from === 'DTC_BALANCE') {
                                $dbBalance->setBalance($email, $availableFrom - $points);
                            } else {
                                $dbSubject->transferPoints($email, $points, $from, $to);
                            }

                            if ($to === 'DTC_BALANCE') {
                                $dbBalance->setBalance($email, $availableTo + $points);
                            }
                            elseif ($from === 'DTC_BALANCE') {
                                $dbSubject->transferPoints($email, $points, $from, $to);
                            }

                            $error = 'success_transfer';
                        }

                    }
                }

                //ICS Import
                if ($formType === 'ics_import') {
                    $maxsize= 2 * 1024 * 1024; // Taille max 2MB

                    if (!isset($_FILES['ics_file']) || $_FILES['ics_file']['error'] !== 0) {
                        $error = 'error_upload';
                    }

                    else {
                        $extension = strtolower(pathinfo($_FILES['ics_file']['name'], PATHINFO_EXTENSION));
                        if ($extension !== 'ics') {
                            $error = 'error_invalid_file_type';
                        }
                        elseif ($_FILES['ics_file']['size'] > $maxsize) {
                            $error = 'error_file_too_large';
                        }
                        else {

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
                                // Only keep subjects that start with S or R followed by a digit
                                if (!preg_match('/^[SR]\d/', $subject)) {
                                    continue;
                                }
                                $subjects[] = $subject;
                            }

                            // Suppression des doublons
                            $subjects = array_unique($subjects);

                            foreach ($subjects as $subject) {
                                $rand_point = rand(0, 20);
                                $dbSubject->insertSubjectSafe($email, $subject, $rand_point);
                            }

                            $error = 'success_import';
                        }
                    }
                }
            }

            $subjectsRows = $dbSubject->getSubject($email);
            $balance = $dbBalance->getBalance($email);
            $view = new TradeSubjectPointView();
            $view->setData($error, $subjectsRows, $balance);
            echo $view->render("Échanger Points - DealTonBUT", static::STYLESHEET);
        }
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === static::PATH && ($meth === 'GET' || $meth === 'POST');
    }
}
