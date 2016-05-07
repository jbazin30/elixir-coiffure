<?php

/**
 * Permet d'enregistrer le backup sur le ftp
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Backup_ftp extends Backup {

    private $fd;

    /**
     * Ouverture de la sortie
     */
    public function open($filename) {
        $dir = ROOT . 'cache/sql_backup/';
        if (!is_writable($dir) && !@chmod($dir, 0777)) {
            trigger_error('Le dossier ' . $dir . ' doit être chmodé en 777', C_ERROR);
        }

        $this->fd = fopen($dir . $filename, 'w');
    }

    /**
     * Ecriture dans la sortie
     */
    public function write($str) {
        fwrite($this->fd, $str);
    }

    /**
     * Fermeture de la sortie
     */
    public function close() {
        fclose($this->fd);
    }

}

/* EOF */