<?php

/**
 * Point d'entrée principal
 */
// On lance les sessions
session_start();

// On défini le jeu de caractère
header('Content-Type: text/html; charset=UTF-8');

// On défini deux constantes : une contenant le chemin racine du site et l'autre l'extension de fichier : php
define('ROOT', './');
define('ROOT_URL', 'http://localhost/coiffure/');
define('PHPEXT', substr(strrchr(__FILE__, '.'), 1));

// On défini l'adresse de provenance
if (empty($_SERVER['HTTP_REFERER'])) {
    define('REFERER_PATH', '/'); // au cas ou...
} else {
    //ROOT_URL est défini avant comme étant l'adresse de votre site...
    define('REFERER_PATH', str_replace(ROOT_URL, '', $_SERVER['HTTP_REFERER']));
}

// On inclus le fichier de chargement des classes et de leur instanciation
include_once( ROOT . 'main/start.' . PHPEXT );

$debut = microtime(true);

// On ne fait pas de mise en cache
Http::no_cache();

class Frame {

    /**
     * Récupère la page demandée pour la pseudo frame
     * @return <string> Nom de la page en cours
     */
    public static function frame_request_page() {
        if (Http::request('p') === NULL) {
            $page = 'index';
        } else {
            $page = Http::request('p');

            if (!preg_match('#^[a-z0-9_]*?$#i', $page) || !file_exists(ROOT . 'main/pages/pages_' . $page . '.' . PHPEXT)) {
                $page = '404';
            }
        }

        return ($page);
    }

    /**
     * Constructeur : charge la page demandée
     * @param <string> $page Nom de la page à afficher
     */
    public function __construct($page) {
        Globale::$Tpl = new Tpl(ROOT . 'templates/', ROOT . 'cache/');

        /**
         * On défini la langue à utiliser par défaut
         */
        if (!isset($_SESSION['langue']) || empty($_SESSION['langue'])) {
            $langue = Http::request('lang', 'get');

            if (empty($langue)) {
                $_SESSION['langue'] = LANG_DEFAULT;
            } else {
                $_SESSION['langue'] = $langue;
            }
        }

        if (!isset($_SESSION['nb_prod']) || empty($_SESSION['nb_prod'])) {
            $_SESSION['nb_prod'] = NB_PAR_PAGE;
        }

        Globale::$footer = new Footer();
        Globale::$header = new Header();

        Header::load_lang($_SESSION['langue']);
        Globale::$date->setLanguage($_SESSION['langue']);

        if (!isset($_SESSION['auth'])) {
            $_SESSION['auth'] = 0;
        }

        // Affiche l'en-tête de la page
        Globale::$header->afficheHeader('', $page);

        // Inclusion de la page fille, et instance de la classe
        class_import('pages_' . $page);
        new Frame_child();
    }

}

// On récupère les données de la page prinpale pour la pseudo frame
$page = Frame::frame_request_page();

/**
 * Instancie la class affichant la page
 */
new Frame($page);
