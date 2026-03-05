<?php

namespace controllers\Trade\TradeSubjectPoint;

use core\controllers\Controller;
use views\Trade\TradeSubjectPoint\TradeSubjectPointView as TradeSubjectPointView;
use dtu\models\SubjectDB;
use models\AccountDB;

/**
 * @brief Class that control the page that allow the user to exchange
 * his points between his subjects and his balance
 */
class TradeSubjectPoint implements Controller {

    public const PATH = '/trade/points';
    public const METH = 'GET';

    /**
     * @var array<string> STYLESHEET The different stylesheet used for the page
     */
    const STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/TradeSubjectPoints.css',
        '/_assets/styles/navbar.css'
    ];

    /**
     * @brief Main controller method for the subject points exchange page.
     *
     * @description
     * Handles the logic of the subject points exchange system.
     *
     * - Verifies that the user is authenticated.
     * - Processes the transfer of points between subjects or the DTC balance.
     * - Validates transfer constraints (available points, subject limit, etc.).
     * - Handles the import of subjects from an ICS calendar file.
     * - Extracts valid subjects from the ICS file and inserts them into the database.
     * - Retrieves user subjects and balance to display them in the view.
     * - Renders the TradeSubjectPointView with the appropriate data and messages.
     *
     * @return void
     */
    function control(): void {
        if (!isset($_SESSION['logged-in']) || $_SESSION['logged-in'] !== true) {
            header('Location: /user/login');
        } else {
            // Récupération des données nécessaires pour la vue
            $dbSubject = SubjectDB::getInstance();
            $email = $_SESSION['email'] ?? '';
            $error = null;

            $dbBalance = AccountDB::getInstance();


            // Traitement du formulaire de transfert de points
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formType = $_POST['form_type'] ?? '';

                if ($formType === 'points_transfer') {

                    $from = $_POST['from_subject'] ?? '';
                    $to = $_POST['to_subject'] ?? '';
                    $points = floatval($_POST['points'] ?? 0);
                    // Validation des entrées
                    if ($from === $to) {
                        $error = 'error_same_subject';
                    } else {
                        $availableFrom = ($from === 'DTC_BALANCE') ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $from);
                        $availableTo   = ($to === 'DTC_BALANCE')   ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $to);
                        // Vérification des points disponibles et des limites
                        if ($availableFrom < $points) {
                            $error = 'error_insufficient_points';
                        }
                        elseif ($to !== 'DTC_BALANCE' && ($availableTo + $points) > 20) {
                            $error = 'error_exceed_max_points';
                        }
                        // Effectuer le transfert
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
                            $dbBalance->updateBalance($email);
                        }

                    }
                }

                //ICS Import
                //Vérification du type de fichier et de la taille
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
                            // Lecture du fichier ICS et extraction des matières
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
                                // remove subjects that contain "Autonomie" or "Aide"
                                if (preg_match('/\b(Autonomie|Aide|Soutien|Certification)\b/i', $subject)) {
                                    continue;
                                }

                                // Truncate before TD, TP, Examen CM
                                $subject = preg_replace('/\s*(TD|TP|Examen|CM|Oral|\()\b.*$/i', '', $subject);
                                $subject = trim($subject);

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
