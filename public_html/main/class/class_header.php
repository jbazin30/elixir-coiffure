<?php

/**
 * Class d'affichage de l'en-tête de la page
 */
class Header {

    public static $lang = [];
    private $title, $page;

    /**
     * Constructeur de la class
     * 	défini le template en fonction du navigateur
     */
    public function __construct() {
        Globale::$Tpl->set_filenames(['header' => 'header.tpl']);
    }

    /**
     * Affiche l'en-tête de la page
     * 	assigne le titre à la page, gère des meta tag de refresh, affiche quelques info sur l'utilisateur et parse le template
     */
    public function afficheHeader($title, $page) {
        $this->title = $title;
        $this->page = $page;

        // Si la page demandée n'existe pas, on redirige vers la page personnalisé 404
        if (isset($this->page) && $this->page == '404') {
            Globale::$meta = Http::redirect('index.html', 5);

            Globale::$redirect = true;
        }

        // Si on est en redirection, on parse le header tout de suite, sans attendre un autre contenu
        if (Globale::$redirect) {
            self::parse_header();
        }
    }

    /**
     * On parse les variables au moment de parser le template entier à cause des redirections automatique.
     */
    public function parse_var() {
        // On assigne les clés de langue à des variables Tpl
        Globale::$Tpl->assign_vars([
            'META' => Globale::$meta,
            'TITLE' => $this->title,
            'CURRENT_PAGE' => $this->page,
            'CURRENT_MODE' => Http::request('mode', 'get'),
            'URL_BACK' => REFERER_PATH,
            'DATE' => utf8_encode(strftime('%A %d %B %Y'))
        ]);
    }

    /**
     * On parse le template
     */
    public function parse_header() {
        self::parse_var();
        Globale::$Tpl->pparse('header');
    }

    public static function load_lang($langue = 'fr') {
        Header::$lang = new Lang($langue);
    }

}
