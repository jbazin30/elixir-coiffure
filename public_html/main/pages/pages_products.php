<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {

        // On défini le template
        Globale::$Tpl->set_filenames(['pages_products' => 'products.tpl']);

        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_products');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
