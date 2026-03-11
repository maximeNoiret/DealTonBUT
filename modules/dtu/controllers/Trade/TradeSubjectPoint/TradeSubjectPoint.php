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

    public const string PATH = '/trade/points';
    public const string METH = 'GET';

    /**
     * @var array<string> STYLESHEET The different stylesheet used for the page
     */
    const array STYLESHEET = [
        '/_assets/styles/style.css',
        '/_assets/styles/TradeSubjectPoints.css',
        '/_assets/styles/navbar.css'
    ];

    /**
     * @description
     * Check if the mime type of the file is one of the allowed types (ics/calendar)
     * @param mixed $filePath
     * @return bool
     */
    public function validate_mime_type(mixed $filePath): bool
    {
        if (!is_string($filePath) || !is_file($filePath)) {
            return false;
        }
        $allowedMimeTypes = ['calendar/ics', 'text/calendar'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return false;
        }
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        return is_string($mimeType) && in_array($mimeType, $allowedMimeTypes);
    }

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
        } elseif (($_SESSION['role'] ?? '') === 'teacher') {
            header('Location: /marketplace');
        } else {
            // Récupération des données nécessaires pour la vue
            $dbSubject = SubjectDB::getInstance();
            $email = is_string($_SESSION['email'] ?? null) ? $_SESSION['email'] : '';
            $error = null;

            $dbBalance = AccountDB::getInstance();
            $post = $_POST;

            // Traitement du formulaire de transfert de points
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formType = is_string($_POST['form_type'] ?? null) ? $_POST['form_type'] : '';

                if ($formType === 'points_transfer') {

                    $from = is_string($_POST['from_subject'] ?? null) ? $_POST['from_subject'] : '';
                    $to   = is_string($_POST['to_subject']   ?? null) ? $_POST['to_subject']   : '';
                    $points = is_numeric($_POST['points'] ?? null) ? (float)$_POST['points'] : 0.0;
                    // Validation des entrées
                    if ($from === $to) {
                        $error = 'error_same_subject';
                    } else {
                        $availableFrom = ($from === 'DTC_BALANCE') ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $from);
                        $availableTo   = ($to === 'DTC_BALANCE')   ? $dbBalance->getBalance($email) : $dbSubject->getPoints($email, $to);

                        $availableFrom = is_numeric($availableFrom) ? (float)$availableFrom : 0.0;
                        $availableTo   = is_numeric($availableTo)   ? (float)$availableTo   : 0.0;
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
                // Vérification du type mime du fichier
                $icsFile = $_FILES['ics_file'] ?? null;
                $isIcsArray = is_array($icsFile);

                if ($formType === 'ics_import' && $isIcsArray && ($icsFile['error'] ?? -1) === UPLOAD_ERR_OK) {
                    $tmpName = $icsFile['tmp_name'] ?? null;
                    if (!$this->validate_mime_type($tmpName)) {
                        $error = 'error_invalid_file_type';
                    }
                }
                //Vérification du type de fichier et de la taille
                if ($formType === 'ics_import') {
                    $maxsize= 2 * 1024 * 1024; // Taille max 2MB

                    if (!is_array($icsFile) || ($icsFile['error'] ?? -1) !== UPLOAD_ERR_OK) {
                        $error = 'error_upload';
                    }
                    else {
                        $fileName = is_string($icsFile['name'] ?? null) ? $icsFile['name'] : '';
                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $fileSize = is_numeric($icsFile['size'] ?? null) ? (int)$icsFile['size'] : 0;
                        if ($extension !== 'ics') {
                            $error = 'error_invalid_file_type';
                        }
                        elseif ($fileSize > $maxsize) {
                            $error = 'error_file_too_large';
                        }
                        else {
                            // Lecture du fichier ICS et extraction des matières
                            $subjects = [];
                            $tmpPath = $icsFile['tmp_name'] ?? '';
                            $lines = is_string($tmpPath) && is_file($tmpPath) ? file($tmpPath) : false;

                            if ($lines === false) { $error = 'error_upload'; }
                            else foreach ($lines as $line) {
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
                                $subject = trim((string)preg_replace('/\s*(TD|TP|Examen|CM|Oral|\()\b.*$/i', '', $subject));
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

            /** @var array<int, array<string, mixed>> $subjectsRows */
            $subjectsRows = $dbSubject->getSubject($email);
            $balanceRaw = $dbBalance->getBalance($email);
            $balance = is_numeric($balanceRaw) ? (float)$balanceRaw : 0.0;

            $view = new TradeSubjectPointView();
            $view->setData($error, $subjectsRows, $balance);
            echo $view->render("Échanger Points - DealTonBUT", static::STYLESHEET);
        }
    }

    public static function resolve(string $path, string $meth): bool {
        return $path === static::PATH && ($meth === 'GET' || $meth === 'POST');
    }
}
