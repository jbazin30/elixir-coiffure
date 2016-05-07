<?php

/**
 * Classe de gestions des log fichier
 */
class Log_file extends Log {

    /**
     * Ajoute un produit
     * 	@param array $valeur informations sur le produit
     */
    public function ajouter() {
        $file = ROOT . 'logs/access.log';
        $open = fopen($file, 'a+');

        Globale::$date->setDateFromTimeStamp(time());
        $time = Globale::$date->str('%J%/%m3%/%Y%:%h%:%mn%:%s%');

        fputs($open, $_SERVER['REMOTE_ADDR'] . ' - - [' . $time . '] "' . $_SERVER['REQUEST_METHOD'] . ' http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '"' . "\r\n");
        fclose($open);
    }

}
