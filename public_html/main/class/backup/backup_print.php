<?php

/**
 * Permet d'afficher le backup à l'écran
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Backup_print extends Backup {

    /**
     * Ouverture de la sortie
     */
    public function open($filename) {
        header('Content-Type: text/plain');
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