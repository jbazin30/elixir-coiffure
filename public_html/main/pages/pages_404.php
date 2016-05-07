<?php

/**
 * Affichage personnalisé des erreurs 404
 */
class Frame_child {

    public function __construct() {
        // On défini le template
        Globale::$Tpl->set_filenames(['pages_404' => 'pages/pages_404.tpl']);

        // On assigne les clés de langue au template
        Globale::$Tpl->assign_vars([
            'CONTENU' => Header::$lang->lang('t_404'),
            'T_REDIR' => Header::$lang->lang('t_redir'),
        ]);

        // On parse le template
        Globale::$Tpl->pparse('pages_404');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
