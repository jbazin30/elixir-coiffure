<?php

/**
 * Permet de télécharger le backup
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Backup_download extends Backup {

    /**
     * Ouverture de la sortie
     */
    public function open($filename) {
        header('Content-Type: text/x-sql');
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
    }

    /**
     * Ecriture dans la sortie
     */
    public function write($str) {
        echo $str;
    }

    /**
     * Fermeture de la sortie
     */
    public function close() {
        exit;
    }

}

/* EOF */