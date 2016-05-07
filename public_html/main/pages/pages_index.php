<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {
        // On défini le template
        Globale::$Tpl->set_filenames(['pages_index' => 'pages/pages_index.tpl']);

        $sql = 'SELECT `fac_num`, `cli_nom`, `cli_prenom` FROM `facture` f INNER JOIN `client` c ON f.`fac_cli_num` = c.`cli_num` WHERE `fac_provisoire` = 1 AND `fac_included` = 0';
        Globale::$db->requete($sql);
        Globale::$resultat = Globale::$db->tableau(PDO::FETCH_NUM);
        if (Globale::$resultat) {
            Globale::$Tpl->create_block('is_lst_fac_prov');
            foreach (Globale::$resultat as $res) {
                Globale::$Tpl->assign_block_vars('lst_fac', [
                    'FAC_NUM' => $res[0],
                    'FAC_CLI_NOM' => $res[1],
                    'FAC_CLI_PRENOM' => $res[2],
                ]);
            }
        }
        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_index');
        Globale::$Tpl->set_filenames(['dialog' => 'dialog.tpl']);
        Globale::$Tpl->create_block('dialog_index');
        Globale::$Tpl->assign_vars([
            'LST_SEXE' => Html::create_list('cli_sexe', '', Globale::$sexe),
            'LST_GEN' => Html::create_list('cli_genre', '', Globale::$genre),
        ]);
        Globale::$Tpl->pparse('dialog');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
