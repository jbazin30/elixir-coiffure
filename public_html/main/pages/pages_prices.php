<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {
        // On défini le template
        Globale::$Tpl->set_filenames(['pages_prices' => 'prestations_tarifs.tpl']);

        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_prices');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
