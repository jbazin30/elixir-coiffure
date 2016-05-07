<?php

/**
 * Class de gestion des erreurs
 */
class Error {

    /**
     * Gestion des erreurs
     * 	@param string $errno Numéro de l'erreur
     * 	@param string $errstr Intitulé de l'erreur
     * 	@param string $errfile Fichier contenant l'erreur
     * 	@param string $errline Ligne contenant l'erreur
     * 	@return true
     */
    public static function error_handler($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return;
        }

        switch ($errno) {
            case E_ERROR:
                echo '<b>Erreur fatale [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                exit;
                break;

            case E_WARNING:
                echo '<b>Warning [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                break;

            case E_NOTICE:
                echo '<b>Notice [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                break;

            case E_DEPRECATED:
                echo '<b>Deprecated [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                break;

            case C_ERROR:
                echo '<b>Erreur fatale [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                break;

            case C_NOTICE:
                echo '<b>Notice [n° ' . $errno . '] : <i>' . $errstr . '</i></b> à la ligne <i><b>' . $errline . '</b></i> dans le fichier <i><b>' . $errfile . '</b></i><br />';
                break;

            default:
                echo "Type d'erreur inconnu : [$errno] $errstr<br /><br />";
                break;
        }

        /* Ne pas exécuter le gestionnaire interne de PHP */
        return true;
    }

}
