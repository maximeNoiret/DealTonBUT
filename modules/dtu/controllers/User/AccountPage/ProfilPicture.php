<?php

namespace controllers\User\AccountPage;

use models\AccountDB;
use core\controllers\Controller;

/**
 * @brief Class that handle the update of the profile picture of the user
 */
class ProfilPicture implements Controller
{
    /**
     * @var string The path to access this page
     */
    private const string PATH = '/user/update-PDP';
    /**
     * @var string The method to access this page
     */
    private const string METH = 'POST';

    /**
     * @description
     * Check if the mime type of the file is one of the allowed types (jpg, jpeg, png, webp, gif)
     * @param $filePath
     * @return bool
     */
    public function validate_mime_type(string $filePath): bool
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp' , 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) { return false; }
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        return is_string($mimeType) && in_array($mimeType, $allowedMimeTypes);
    }

    /**
     * @description
     * Check if the user is logged in, if not redirect to the login page
     * Check if the file is uploaded without error
     * Check if the file is of the allowed type and size
     * Move the file to the destination folder with a new name
     * Update the database with the new file name
     * Delete the old profile picture if it exists and is not the default one
     * Update the session with the new file name
     * Redirect to the account page with a success message or an error message if something went wrong
     * @return void
     */
    public function control(): void
    {
        $sessionEmail = $_SESSION['email'] ?? null;
        if (!is_string($sessionEmail)) {
            header('Location: /login');
            exit();
        }

        //Vérifier que le fichier a été uploadé sans erreur
        $file = $_FILES['profile_picture'] ?? null;
        if (!is_array($file) || ($file['error'] ?? null) !== UPLOAD_ERR_OK) {
            header('Location: /user/account?error=upload_failed');
            exit();
        }

        $tmpName  = is_string($file['tmp_name'] ?? null) ? $file['tmp_name'] : '';
        $fileName = is_string($file['name'] ?? null) ? $file['name'] : '';
        $fileSize = is_int($file['size'] ?? null) ? $file['size'] : 0;

        $docRoot = is_string($_SERVER['DOCUMENT_ROOT'] ?? null) ? $_SERVER['DOCUMENT_ROOT'] : '';

        // Vérifier la taille du fichier
        if ($fileSize > 3 * 1024 * 1024) {
            header('Location: /user/account?error=file_too_large');
            exit();
        }
        // Vérifier le type MIME du fichier
        if (!$this->validate_mime_type($tmpName)) {
            header('Location: /user/account?error=invalid_file_type');
            exit();
        }

        // Vérifier l'extension du fichier
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowed)) {
            header('Location: /user/account?error=invalid_file');
            exit();
        }
        // Mettre un nom de fichier unique
        $emailParts = explode('@', $sessionEmail);
        $safeName = preg_replace('/[^a-z0-9]/', '', $emailParts[0]) ?? 'profile';

        $newName = 'pdp_' . $safeName . '_' . time() . '.' . $extension;
        $destinationPath = $docRoot . '/_assets/images/profil_picture/' . $newName;

        if (!move_uploaded_file($tmpName, $destinationPath)) {
            header('Location: /user/account?error=dbTransfer_failed');
            exit();
        }

        //Suppr l'ancienne pdp si elle existe et n'est pas la pdp par défaut
        $accountDB = AccountDB::getInstance();
        $oldFileName = $_SESSION['profile_picture'] ?? null;

        if (is_string($oldFileName) && !empty($oldFileName)) {
            $oldFilePath = $docRoot . '/_assets/images/profil_picture/' . $oldFileName;

            if ($oldFileName !== 'account_pp.webp' && $oldFileName !== 'default.png' && file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $accountDB->updateProfilPicture($sessionEmail, $newName);
        $_SESSION['profile_picture'] = $newName;

        header('Location: /user/account?success=1');
        exit();
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}