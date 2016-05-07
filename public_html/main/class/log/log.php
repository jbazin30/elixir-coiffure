<?php

/**
 * Classe permettant la génération de fils RSS au format RSS 2.0 ou ATOM
 */
class Log {

    /**
     * Retourne une instance de la classe Log
     */
    public static function &factory($type) {
        if (!in_array($type, array('session', 'bdd', 'file'))) {
            $type = 'file';
        }

        $classname = 'Log_' . $type;

        $obj = new $classname();
        return ($obj);
    }

}
