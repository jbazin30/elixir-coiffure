<?php

/**
 * Class d'affichage du pied de la page
 */
class Footer {

    /**
     * Temps à la fin de l'exécution du code
     * 	@access public
     */
    public $fin;

    /**
     * Temps de génération de la page
     * 	@access public
     */
    public $temp_page;

    /**
     * Numéro aléatoire
     * 	@access public
     */
    public $num_image;

    /**
     * Montant total du panier
     * 	@access public
     */
    public $montant_panier;

    /**
     * Défini le template
     */
    public function __construct() {
        Globale::$Tpl->set_filenames(['footer' => 'footer.tpl']);
    }

    /**
     * Affiche le pied de page et assigne les clés de langue à des variables Tpl et parse le template
     */
    public function afficheFooter() {
        $fin = microtime(true);

        Globale::$Tpl->assign_vars([
            'NB_REQUETE' => Globale::$db->nbRequete(),
            'TIME' => round($fin - $GLOBALS['debut'], 3),
        ]);

        // On parse de template
        Globale::$Tpl->pparse('footer');

        // Fonction::printr($GLOBALS);
        // Fonction::printr($_SESSION);
        // Fonction::printr($_SERVER);
        // Fonction::printr($_POST);
        // Fonction::printr($_GET);
    }

}
