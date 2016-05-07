<?php

/**
 * Affichage de la page de recherche client
 */
class Frame_child {

    protected $action, $page;

    public function __construct() {
        // On défini le template principal
        Globale::$Tpl->set_filenames(['pages_search' => 'pages/pages_search.tpl']);
        $this->action = Http::request('a', 'get');

        Globale::$Tpl->create_block('in_search');

        // On récupère les infos du client
        $sql = 'SELECT DISTINCT `cli_num`, `cli_nom`, `cli_prenom`, `cli_ville`, `cli_sexe`, `gen_libelle` FROM `client` INNER JOIN `genre` ON `genre`.`gen_num` = `client`.`cli_genre` ORDER BY `cli_nom` ASC';
        Globale::$requete = Globale::$db->requete($sql);
        Globale::$resultat = Globale::$db->tableau();

        foreach (Globale::$resultat as $res) {
            Globale::$Tpl->assign_block_vars('lst_cli', ['CLI_NUM' => $res['cli_num'],
                'CLI_NOM' => $res['cli_nom'],
                'CLI_PNOM' => $res['cli_prenom'],
                'CLI_VILLE' => $res['cli_ville'],
                'CLI_GENRE' => $res['gen_libelle'],
                'CLI_SEXE' => Globale::$sexe[$res['cli_sexe']]
            ]);
        }

        if (isset($this->action)) {
            $this->page = 'facturation';
        } else {
            $this->page = 'client';
        }
        Globale::$Tpl->assign_vars(['PAGE' => $this->page]);

        Globale::$header->parse_header();
        Globale::$Tpl->pparse('pages_search');
        ///
        Globale::$footer->afficheFooter();
    }

}
