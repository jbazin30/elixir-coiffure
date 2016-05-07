<?php

/**
 * Classe de manipulation de fichier/dossier : Méthode socket
 */
class File_socket extends File {

    // Socket de connexion
    private $sock;
    // Méthode
    public $method = 'socket';

    /**
     * Connnexion au serveur
     * -----
     * $server ::		Adresse du serveur
     * $login ::		Login
     * $password ::		Mot de passe
     * $port ::			Port
     */
    protected function _connexion($server, $login, $password, $port, $path) {
        $list = explode(',', @ini_get('disable_functions'));
        if (in_array('fsockopen', $list)) {
            return (File::FILE_FSOCKOPEN_DISABLED);
        }

        // Connexion socket au serveur
        $errno = 0;
        $errstr = '';
        $this->sock = @fsockopen($server, $port, $errno, $errstr, 15);

        if (!$this->sock || !$this->_read()) {
            return (File::FILE_CANT_CONNECT_SERVER);
        }

        // Login
        if (!$this->_send("USER $login")) {
            return (File::FILE_CANT_AUTHENTIFICATE);
        }

        // Mot de passe
        if (!$this->_send("PASS $password")) {
            return (File::FILE_CANT_AUTHENTIFICATE);
        }

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
        return ($this->_send("CWD $path"));
    }

    /**
     * Renomme un fichier
     * -----
     * $from ::		Nom du fichier d'origine
     * $to ::		Nom du fichier de destination
     */
    protected function _rename($from, $to) {
        $this->_send("RNFR $from");
        $this->_send("RNTO $to");
    }

    /**
     * Change les droits d'un fichier
     * -----
     * $file ::		Nom du fichier
     * $mode ::		Mode du chmod
     */
    protected function _chmod($file, $mode) {
        return ($this->_send("SITE CHMOD $mode $file"));
    }

    /**
     * Copie un fichier vers une destination
     * -----
     * $src ::		Fichier source
     * $dst ::		Fichier destination
     */
    protected function _put($src, $dst) {
        $this->_send("TYPE I");
        $socket = $this->_open_connexion();
        $this->_send("STOR $dst", FALSE);

        // On envoie le fichier sur le réseau
        if ($socket) {
            $fd = fopen($this->local_path . $src, 'rb');
            while (!feof($fd)) {
                fwrite($socket, fread($fd, 4096));
            }
            fclose($fd);
        } else {
            return (File::FILE_CANT_CONNECT_SERVER);
        }

        $this->_close_connexion($socket);
        return (TRUE);
    }

    /**
     * Supprime un fichier
     * -----
     * $filename ::		Nom du fichier à supprimer
     */
    protected function _unlink($filename) {
        $this->_send("DELE $filename");
    }

    /**
     * Créé un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _mkdir($dir) {
        return ($this->_send("MKD $dir"));
    }

    /**
     * Supprime un répertoire
     * -----
     * $dir ::		Nom du répertoire
     */
    protected function _rmdir($dir) {
        return ($this->_send("RMD $dir"));
    }

    /**
     * Lit la réponse du serveur sur la socket
     */
    protected function _read() {
        $str = '';
        do {
            $str .= @ fgets($this->sock, 512);
        } while (substr($str, 3, 1) != ' ');

        if (!preg_match('#^[1-3]#', $str)) {
            return (FALSE);
        }
        return ($str);
    }

    /**
     * Ecrit sur la socket
     */
    protected function _send($command, $check = TRUE) {
        fwrite($this->sock, $command . "\r\n");
        if ($check && !$this->_read()) {
            return (FALSE);
        }
        return (TRUE);
    }

    /**
     * Ouvre une connexion pour l'envoie de données sur le socket, renvoie le socket de connexion
     */
    protected function _open_connexion() {
        $this->_send("PASV", FALSE);
        if (!$read = $this->_read()) {
            return (File::FILE_CANT_CONNECT_SERVER);
        }

        // On lit la réponse (qui doit contenir une IP et un port)
        if (!preg_match('#[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]{1,3},[0-9]+,[0-9]+#', $read, $match)) {
            return (File::FILE_CANT_CONNECT_SERVER);
        }

        // On récupère l'IP et le port du serveur
        $split = explode(',', $match[0]);
        $ip = $split[0] . '.' . $split[1] . '.' . $split[2] . '.' . $split[3];
        $port = $split[4] * 256 + $split[5];

        // Connexion socket
        $errno = 0;
        $errstr = '';
        if (!$socket = fsockopen($ip, $port, $errno, $errstr, 15)) {
            return (FALSE);
        }
        return ($socket);
    }

    /**
     * Ferme la connexion ouverte par la méthode Socket_file::_open_connexion()
     * -----
     * $socket ::	File descriptor
     */
    protected function _close_connexion($socket) {
        return (fclose($socket));
    }

    /**
     * Ferme la connexion
     */
    protected function _close() {
        $this->_send("QUIT", FALSE);
        @fclose($this->sock);
    }

}
