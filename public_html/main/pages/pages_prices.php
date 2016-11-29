<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {

		$tpl= Fonction::check_variable(Http::request('m', 'get')) ? Http::request('m', 'get') : 'coiffure' ;
		
		// On défini le template
		Globale::$Tpl->set_filenames(['pages_prices' => 'tarifs-' . $tpl . '.tpl']);

        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_prices');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
