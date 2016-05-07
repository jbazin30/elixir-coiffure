<?php

/**
 * Affichage de la page fiche client
 */
class Frame_child {

    protected $add_value = null;

    public function __construct() {

        // On défini le template principal
        Globale::$Tpl->set_filenames(['pages_depense' => 'pages/pages_depense.tpl']);

        // On récupère les infos du client
        $sql = 'SELECT * FROM `achat`';
        Globale::$db->requete($sql);
        Globale::$resultat = Globale::$db->tableau();
        if (Globale::$resultat) {
            Globale::$Tpl->create_block('is_lst_ach');
            foreach (Globale::$resultat as $res) {
                Globale::$Tpl->assign_block_vars('lst_ach', [
                    'ACH_NUM' => $res['ach_num'],
                    'ACH_LIBEL' => $res['ach_libelle'],
                    'ACH_PRIX' => Fonction::build_web_price($res['ach_prix']),
                    'ACH_DATE' => $res['ach_date'],
                ]);
            }
        }

        Globale::$header->parse_header();
        Globale::$Tpl->pparse('pages_depense');

        ///
        Globale::$footer->afficheFooter();
    }

}
