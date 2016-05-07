<?php

/**
 * Affichage de la page fiche client
 */
class Frame_child {

    protected $date_nais, $now, $cli;

    public function __construct() {

        // On défini le template principal
        Globale::$Tpl->set_filenames(['pages_client' => 'pages/pages_client.tpl']);

        $this->cli = Http::request('cli');

        // On récupère les infos du client
        $sql = 'SELECT * FROM `client` WHERE `cli_num` = :num';
        $paramx = [':num' => $this->cli];
        $result = Globale::$db->request($sql, $paramx);

        Globale::$Tpl->assign_vars([
            'CLI_NUM' => $result['cli_num'],
            'CLI_NOM' => $result['cli_nom'],
            'CLI_PNOM' => $result['cli_prenom'],
            'CLI_ADR' => $result['cli_adr'],
            'CLI_CP' => $result['cli_cp'],
            'CLI_VILLE' => $result['cli_ville'],
            'CLI_TEL' => $result['cli_tel'],
            'CLI_MOBILE' => $result['cli_mobile'],
            'CLI_EMAIL' => $result['cli_email'],
            'CLI_NAIS' => $result['cli_naiss']
        ]);

        $sql2 = 'SELECT `svc_num`, `svc_date`, `fam_libelle` FROM service s INNER JOIN famille f ON s.svc_fam_num = f.fam_num WHERE svc_cli_num = :num ORDER BY svc_date DESC';
        Globale::$db->requete($sql2, $paramx);
        Globale::$resultat = Globale::$db->tableau(PDO::FETCH_NUM);

        if (Globale::$resultat) {
            Globale::$Tpl->create_block('is_lst_svc');
            foreach (Globale::$resultat as $res) {
                Globale::$Tpl->assign_block_vars('lst_svc', [
                    'SVC_NUM' => $res[0],
                    'SVC_DATE' => $res[1],
                    'FAM_LIBEL' => $res[2],
                ]);
            }
        }

        $sql3 = 'SELECT * FROM `facture` f WHERE f.`fac_cli_num` = :num';
        Globale::$db->requete($sql3, $paramx);
        Globale::$resultat = Globale::$db->tableau(PDO::FETCH_NUM);
        if (Globale::$resultat) {
            Globale::$Tpl->create_block('is_lst_fac');
            foreach (Globale::$resultat as $res) {
                Globale::$Tpl->assign_block_vars('lst_fac', [
                    'FAC_NUM' => $res[0],
                    'FAC_DATE' => $res[1]
                ]);
            }
        }

        Globale::$Tpl->assign_vars([
            'LST_GEN' => Html::create_list('cli_genre', $result['cli_genre'], Globale::$genre),
            'LST_SEXE' => Html::create_list('cli_sexe', $result['cli_sexe'], Globale::$sexe),
        ]);

        Globale::$header->parse_header();
        Globale::$Tpl->pparse('pages_client');
        ///
        Globale::$Tpl->set_filenames(['dialog' => 'dialog.tpl']);
        Globale::$Tpl->create_block('dialog_client');
        Globale::$Tpl->assign_vars(['DATE' => date('d/m/Y'),
            'LST_SEXE' => Html::create_list('cli_sexe', '', Globale::$sexe),
            'LST_GEN' => Html::create_list('cli_genre', '', Globale::$genre),
        ]);

        Globale::$Tpl->pparse('dialog');
        ///
        Globale::$footer->afficheFooter();
    }

}
