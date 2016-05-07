<?php

/**
 * Classe de manipulation de fichier/dossier : Méthode FTP
 */
class File_ftp extends File {

    // Ressource de connexion au serveur FTP
    private $stream;
    // Méthode
    public $method = 'ftp';

    /**
     * Connnexion au serveur
     * -----
     * $server ::		Adresse du serveur
     * $login ::		Login
     * $password ::		Mot de passe
     * $port ::			Port
     */
    protected function _connexion($server, $login, $password, $port, $path) {
        // Vérification de la gestion de l'extension FTP
        if (!extension_loaded('ftp')) {
            return (File::FILE_FTP_EXTENSION_DISABLED);
        }

        // Connexion au serveur
        $this->stream = ftp_connect($server, $port, 15);
        if ($this->stream === FALSE) {
            return (File::FILE_CANT_CONNECT_SERVER);
        }

        // Authentification
        if (!ftp_login($this->stream, $login, $password)) {
            return (File::FILE_CANT_AUTHENTIFICATE);
        }

        // On passe en mode passif (le client écoute la connexion)
        ftp_pasv($this->stream, TRUE);

        $this->ROOT_path = './';
        $this->local_path = ROOT;

        // On se déplace à la racine du forum
        if (!$this->_chdir($path)) {
            return (File::FILE_CANT_CHDIR);
        }
        return (TRUE);
    }

    /**
     * Change de répertoire courant
     * -----
     * $path ::		Nouveau répertoire courant
     */
    protected function _chdir($path) {
        return (@ftp_chdir($this->stream, $path));
    }

    /**
     * Renomme un fichier
     * -----
     * $from ::		Nom du fichier d'origine
     * $to ::		Nom du fichier de destination
     */
    protected function _rename($from, $to) {
        return (@ftp_rename($this->stream, $from, $to));
    }

    /**
     * Change les droits d'un fichier
     * -----
     * $file ::		Nom du fichier
     * $mode ::		Mode du chmod
     */
    protected function _chmod($file, $mode) {
        return (@ftp_chmod($this->stream, $mode, $file));
    }

    /**
     * Copie un fichier vers une destination
     * -----
     * $src ::		Fichier source
     * $dst ::		Fichier destination
     */
    protected function _put($src, $dst) {
        // Apparament ftp_put() a certains problèmes, probablement liés au safe mode.
        // On utilise donc ftp_fput() qui fonctionne sans problèmes.
        //		$result = ftp_put($this->stream, $dst, $src, FTP_BINARY);
        $fd = fopen($this->local_path . $src, 'rb');
        $result = ftp_fput($this->stream, $dst, $fd, FTP_BINARY);
        fclose($fd);
        return ($result);
    }

    /**
     * Supprime un fichier
     * -----
     * $filename ::		Nom du fichier à supprimer
     */
    protected function _unlink($filename) {
        ftp_delete($this->stream, $filename);
    }

    /**
     * Créé un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _mkdir($dir) {
        return (ftp_mkdir($this->stream, $dir));
    }

    /**
     * Supprime un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _rmdir($dir) {
        return (ftp_rmdir($this->stream, $dir));
    }

    /**
     * Ferme la connexion
     */
    protected function _close() {
        ftp_close($this->stream);
    }

}
