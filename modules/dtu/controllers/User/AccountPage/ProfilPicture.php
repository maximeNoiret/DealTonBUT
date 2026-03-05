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
    private const PATH = '/user/update-PDP';
    /**
     * @var string The method to access this page
     */
    private const METH = 'POST';

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
        if (!isset($_SESSION['email'])) {
            header('Location: /login');
            exit();
        }

        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {

            // Vérification de la taille et du type de fichier
            $file = $_FILES['profile_picture'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if($file['size'] > 5 * 1024 * 1024) {
                header('Location: /user/account?error=file_too_large');
                exit();
            }
            // Modification du nom du fichier pour normaliser et éviter les conflits
            if (in_array($extension, $allowed)) {

                $safeName = preg_replace(
                    '/[^a-z0-9]/',
                    '',
                    explode('@', $_SESSION['email'])[0]
                );

                $newName = 'pdp_' . $safeName . '_' . time() . '.' . $extension;
                $destinationPath = $_SERVER['DOCUMENT_ROOT']. '/_assets/images/profil_picture/' . $newName;
                if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
                    // Supprimer l'ancienne photo de profil si elle existe et n'est pas la photo par défaut
                    $accountDB = AccountDB::getInstance();
                    if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])) {
                        $oldFileName = $_SESSION['profile_picture'];
                        $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . '/_assets/images/profil_picture/' . $oldFileName;

                        if ($oldFileName !== 'account_pp.webp' && $oldFileName !== 'default.png' && file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    // Mettre à jour la base de données avec le nouveau nom de fichier
                    $accountDB->updateProfilPicture($_SESSION['email'], $newName);
                    // Mettre à jour la session avec le nouveau nom de fichier
                    $_SESSION['profile_picture'] = $newName;
                    header('Location: /user/account?success=1');
                    exit();

                } else {
                    header('Location: /user/account?error=dbTransfer_failed');
                    exit();
                }
            }
            header('Location: /user/account?error=invalid_file');
            exit();
        }
    }

    static function resolve(string $path, string $meth): bool
    {
        return $path === self::PATH && $meth === self::METH;
    }
}