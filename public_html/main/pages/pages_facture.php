<?php

/**
 * Affichage de la page facture
 */
class Frame_child {

    protected $fac, $num, $total = 0;

    public function __construct() {

        // On défini le template principal
        Globale::$Tpl->set_filenames(['pages_facture' => 'pages/pages_facture.tpl']);

        $this->fac = Http::request('f');
        $this->num = Http::request('c');

        $sql = 'SELECT `lg_pres_prix_applied`, `pres_libelle` FROM `facture` f
            INNER JOIN `ligne_facture` lg ON f.`fac_num` = lg.`lg_fac_num`
            INNER JOIN `prestation` p ON p.`pres_num` = lg.`lg_pres_num`
            WHERE f.`fac_num` = :fac_num';
        $paramx = [':fac_num' => $this->fac];
        Globale::$db->requete($sql, $paramx);
        Globale::$resultat = Globale::$db->tableau(PDO::FETCH_NUM);
        foreach (Globale::$resultat as $res) {
            Globale::$Tpl->assign_block_vars('lst_fac', [
                'FAC_PRICE_APPLIED' => $res[0],
                'PRES_LIBEL' => $res[1]
            ]);
            $this->total += $res[0];
        }
        ///
        Globale::$Tpl->assign_vars([
            'TOTAL' => Fonction::build_web_price($this->total),
            'CLI_NUM' => $this->num
        ]);
        Globale::$header->parse_header();
        Globale::$Tpl->pparse('pages_facture');
        ///
        Globale::$footer->afficheFooter();
    }

}
