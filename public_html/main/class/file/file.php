<?php

/**
 * Classe de manipulation de fichier/dossier
 */

/**
 * Permet de manipuler des fichiers sur le serveur via trois méthodes différentes :
 * 	- Manipulation des fichiers en local (soumis au safe mode)
 * 	- Manipulation des fichiers via l'extension FTP (necessite les identifiants de connexion au serveur de fichier)
 * 	- Manipulation des fichiers via l'envoie de commandes au socket du server avec fsockopen() (necessite les identifiants de connexion au serveur de fichier)
 */
class File {

    // Chemin d'accès vers la racine
    public $ROOT_path = './';
    // Chemin local d'accès vers la racine
    public $local_path = './';

    // Liste des erreurs possibles
    const FILE_CANT_CONNECT_SERVER = 1;
    const FILE_CANT_AUTHENTIFICATE = 2;
    const FILE_CANT_CHDIR = 3;
    const FILE_FSOCKOPEN_DISABLED = 4;
    const FILE_FTP_EXTENSION_DISABLED = 5;

    /**
     * Retourne une instance de la bonne classe File à utiliser
     * -----
     * $use_ftp ::		Si on utilise une connexion FTP (TRUE / FALSE)
     */
    public static function factory($use_ftp) {
        if ($use_ftp) {
            $identifier = Display::check_ftp();
            if (extension_loaded('ftp')) {
                $file = new File_ftp();
            } else {
                $file = new File_socket();
            }
            $file->connexion($identifier['host'], $identifier['login'], $identifier['password'], $identifier['port'], $identifier['path']);
        } else {
            $file = new File_local();
            $file->connexion('', '', '', '', ROOT);
        }
        return ($file);
    }

    /**
     * Connnexion au serveur
     * -----
     * $server ::		Adresse du serveur
     * $login ::		Login
     * $password ::		Mot de passe
     * $port ::			Port
     */
    public function connexion($server, $login, $password, $port, $path) {
        $result = $this->_connexion($server, $login, $password, $port, $path);
        if ($result === TRUE) {
            return;
        }

        switch ($result) {
            case File::FILE_CANT_CONNECT_SERVER :
                trigger_error(Header::$lang->lang('file_cant_connect_server'), C_NOTICE);
                break;

            case File::FILE_CANT_AUTHENTIFICATE :
                trigger_error(sprintf(Header::$lang->lang('file_cant_authentificate'), $login), C_NOTICE);
                break;

            case File::FILE_CANT_CHDIR :
                trigger_error(sprintf(Header::$lang->lang('file_cant_chdir'), $path), C_NOTICE);
                break;

            case File::FILE_FSOCKOPEN_DISABLED :
                trigger_error('file_fsockopen_disabled', C_NOTICE);
                break;

            case File::FILE_FTP_EXTENSION_DISABLED :
                trigger_error('file_ftp_extension_disabled', C_NOTICE);
                break;
        }
    }

    /**
     * Change les droits d'un fichier
     * -----
     * $file ::		Nom du fichier
     * $mode ::		Mode du chmod
     * $debug ::	Débugage du CHMOD ?
     */
    public function chmod($file, $mode, $debug = TRUE) {
        $result = $this->_chmod($this->ROOT_path . $file, $mode);
        if (!$result && $debug) {
            trigger_error(sprintf(Header::$lang->lang('file_cant_chmod'), $this->ROOT_path . $file), C_NOTICE);
        }
    }

    /**
     * Ecrit des données dans un fichier
     * -----
     * $filename ::		Nom du fichier
     * $content ::		Contenu à écrire
     */
    public function write($filename, $content) {
        // Nom du fichier
        $tmp = $this->uniq_name('save/', 'class_file_');

        // On écrit le fichier
        if (is_writable($this->local_path . 'save')) {
            $this->chmod('save/', 0777, FALSE);
        }

        $fd = fopen($this->local_path . $tmp, 'wb');
        if (!$fd) {
            trigger_error('file_cant_create_tmp', C_NOTICE);
        }
        fwrite($fd, $content);
        fclose($fd);
        @chmod($this->local_path . $tmp, 0666);

        // On remplace le fichier de destination par le fichier temporaire qu'on vient de faire
        $this->mkdir(dirname($filename));
        $this->copy($tmp, $filename);

        // On supprime le fichier créé une fois le transfert effectué
        @unlink($this->local_path . $tmp);

        return (TRUE);
    }

    /**
     * Copie un fichier vers un répertoire
     * -----
     * $src ::		Fichier source
     * $dst ::		Fichier destination
     */
    public function copy($src, $dst) {
        //$this->_unlink($dst);
        if (is_dir($this->local_path . $src)) {
            if ($src[strlen($src) - 1] != '/') {
                $src .= '/';
            }

            if ($dst[strlen($dst) - 1] != '/') {
                $dst .= '/';
            }

            $fd = opendir($this->local_path . $src);
            while ($file = readdir($fd)) {
                if ($file[0] != '.') {
                    $this->copy($src . $file, $dst . $file);
                }
            }
            closedir($fd);
        } else {
            if (!$this->_put($this->ROOT_path . $src, $this->ROOT_path . $dst)) {
                trigger_error(sprintf(Header::$lang->lang('file_cant_put_file'), $src, $dst), C_NOTICE);
            }

            if (!is_writable($this->local_path . $dst)) {
                $this->chmod($dst, 0666);
            }
        }
    }

    /**
     * Créé un répertoire et les répertoires de son arborescence
     * -----
     * $dir ::		Nom du répertoire
     */
    public function mkdir($dir) {
        if (is_dir($dir)) {
            return;
        }

        $dirs = explode('/', $dir);
        $path = './';
        foreach ($dirs AS $cur) {
            $path .= $cur . '/';
            if (!file_exists($this->local_path . $path) && $cur != '.' && $cur != '..') {
                $result = $this->_mkdir($this->ROOT_path . $path);
                if (!$result) {
                    trigger_error(sprintf(Header::$lang->lang('file_cant_mkdir'), $this->local_ROOT . $path), C_NOTICE);
                }
                $this->chmod($path, 0777);
            }
        }
    }

    /**
     * Supprime un répertoire et les répertoires de son arborescence
     * -----
     * $dir ::		Nom du répertoire
     */
    public function rmdir($dir) {
        if (!$this->_rmdir($this->ROOT_path . $dir)) {
            trigger_error(sprintf(Header::$lang->lang('file_cant_remove_dir'), $this->local_path . $dir), C_NOTICE);
        }
    }

    /**
     * Créé un nom de fichier temporaire
     * -----
     * $dir ::		Nom du dossier
     * $prefix ::	Préfixe du fichier
     */
    protected function uniq_name($dir, $prefix) {
        do {
            $filename = $dir . $prefix . md5(rand(0, time()));
        } while (file_exists($this->local_path . $filename));
        return ($filename);
    }

    /**
     * Change de répertoire courant
     * -----
     * $path ::		Nouveau répertoire courant
     */
    public function chdir($path) {
        return ($this->_chdir($this->ROOT_path . $path));
    }

    /**
     * Renomme un fichier
     * -----
     * $from ::		Nom du fichier d'origine
     * $to ::		Nom du fichier de destination
     */
    public function rename($from, $to) {
        return ($this->_rename($this->ROOT_path . $from, $this->ROOT_path . $to));
    }

    /**
     * Supprime un fichier
     * -----
     * $filename ::		Nom du fichier à supprimer
     */
    public function unlink($filename) {
        if (is_dir($this->local_path . $filename)) {
            $fd = opendir($this->local_path . $filename);
            while ($file = readdir($fd)) {
                if ($file != '.' && $file != '..') {
                    $this->unlink($filename . '/' . $file);
                }
            }
            closedir($fd);

            return ($this->_rmdir($this->ROOT_path . $filename));
        } else {
            return ($this->_unlink($this->ROOT_path . $filename));
        }
    }

    /**
     * Destructeur
     */
    public function __destruct() {
        $this->_close();
    }

}
