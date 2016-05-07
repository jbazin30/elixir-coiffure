<?php

/**
 * Classe de manipulation de fichier/dossier : Méthode locale
 */
class File_local extends File {

    // Méthode
    public $method = 'local';

    /**
     * Connnexion au serveur
     * -----
     * $server ::		Adresse du serveur
     * $login ::		Login
     * $password ::		Mot de passe
     * $port ::			Port
     */
    protected function _connexion($server, $login, $password, $port, $path) {
        $this->ROOT_path = $path;
        $this->local_path = $path;
        return (TRUE);
    }

    /**
     * Change de répertoire courant
     * -----
     * $path ::		Nouveau répertoire courant
     */
    protected function _chdir($path) {
        return (@chdir($path));
    }

    /**
     * Renomme un fichier
     * -----
     * $from ::		Nom du fichier d'origine
     * $to ::		Nom du fichier de destination
     */
    protected function _rename($from, $to) {
        return (@rename($from, $to));
    }

    /**
     * Change les droits d'un fichier
     * -----
     * $file ::		Nom du fichier
     * $mode ::		Mode du chmod
     */
    protected function _chmod($file, $mode) {
        return (@chmod($file, $mode));
    }

    /**
     * Copie un fichier vers une destination
     * -----
     * $src ::		Fichier source
     * $dst ::		Fichier destination
     */
    protected function _put($src, $dst) {
        $result = @rename($src, $dst);
        if (!$result && ((file_exists($dst) && is_writable($dst)) || is_writable(dirname($dst)))) {
            $fd = fopen($dst, 'wb');
            if (!$fd) {
                return (FALSE);
            }
            fwrite($fd, file_get_contents($src));
            fclose($fd);
            return (TRUE);
        }
        return ($result);
    }

    /**
     * Supprime un fichier
     * -----
     * $filename ::		Nom du fichier à supprimer
     */
    protected function _unlink($filename) {
        return (@unlink($filename));
    }

    /**
     * Créé un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _mkdir($dir) {
        return (@mkdir($dir));
    }

    /**
     * Supprime un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _rmdir($dir) {
        return (@rmdir($dir));
    }

    /**
     * Ferme la connexion
     */
    protected function _close() {
        
    }

}
