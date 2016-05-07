<?php

/**
 * Démarrage et instanciation générale
 */
// On défini les types d'erreurs à reporter
error_reporting(E_ALL);
//set_time_limit(0);
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fra_fra');

// On inclus le fichier de configuration des constantes
include_once(ROOT . 'config/config.' . PHPEXT);

/**
 * Autochargement des class
 * @param <string> $classname Nom de la class
 */
function __autoload($classname) {
    $classname = strtolower($classname);
    class_import($classname);
}

/**
 * Inclus un fichier du dossier main/ de façon intelligente
 * @staticvar <array> $store Tableau des noms de class pour une seule inclusion (sécurité supplémentaire en plus de include_once())
 * @param <string> $filename Nom du fichier à inclure
 */
function class_import($filename) {
    static $store;

    if (!isset($store[$filename])) {
        $split = explode('_', $filename);

        if (file_exists(ROOT . 'main/class/class_' . $filename . '.' . PHPEXT)) {
            include_once(ROOT . 'main/class/class_' . $filename . '.' . PHPEXT);
        } elseif (file_exists(ROOT . 'main/class/' . $split[0] . '/' . $filename . '.' . PHPEXT)) {
            include_once(ROOT . 'main/class/' . $split[0] . '/' . $filename . '.' . PHPEXT);
        } elseif (file_exists(ROOT . 'main/' . $split[0] . '/' . $filename . '.' . PHPEXT)) {
            include_once(ROOT . 'main/' . $split[0] . '/' . $filename . '.' . PHPEXT);
        } elseif (file_exists(ROOT . 'main/' . $filename . '.' . PHPEXT)) {
            include_once(ROOT . 'main/' . $filename . '.' . PHPEXT);
        }
        $store[$filename] = TRUE;
    }
}

// On instancie les class chargées que l'on affecte à des variables de class static. Cela permet de les utiliser n'importe où

$argx = [
    'db' => SQL_DB,
    'host' => SQL_SERVER,
    'user' => SQL_LOGIN,
    'pass' => SQL_PASS
];

Globale::$db = new Db($argx);
Globale::$ajax = new Ajax();
Globale::$log = Log::factory('file');
Globale::$date = new Date();
Globale::$date->setLanguage('fr');
Globale::$highlight_sql = Highlight::factory('sql');

// On défini le gestionnaire d'erreur
set_error_handler(array('Error', 'error_handler'));
