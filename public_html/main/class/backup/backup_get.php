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
class Backup_get extends Backup {

    private $_return = '';

    /**
     * Ouverture de la sortie
     */
    public function open($filename) {
        $this->_return = '';
    }

    /**
     * Ecriture dans la sortie
     */
    public function write($str) {
        $this->_return .= $str;
    }

    /**
     * Fermeture de la sortie
     */
    public function close() {
        return ($this->_return);
    }

}

/* EOF */