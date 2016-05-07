<?php

/**
 * Affichage de la page fiche client
 */
class Frame_child {

	protected $fac_num, $cli_num, $lst_paiement;

	public function __construct() {

		// On défini le template principal
		Globale::$Tpl->set_filenames(['dialog' => 'dialog.tpl']);
		Globale::$Tpl->create_block('dialog_facturation');
		Globale::$Tpl->create_block('dialog_get_waiting_fac');
		Globale::$Tpl->pparse('dialog');

		Globale::$Tpl->set_filenames(['pages_facturation' => 'pages/pages_facturation.tpl']);

		$this->fac_num = Http::request('f', 'get');
		$this->cli_num = Http::request('cli', 'get');

		if (isset($this->fac_num)) {
			Globale::$Tpl->create_block('show_fac');

			$sql = '
SELECT `fac_num`,
       `cli_num`,
       `cli_nom`,
       `cli_prenom`,
       `fac_provisoire`,
       Sum(`lg_pres_prix_base`)    AS "t1",
       Sum(`lg_pres_prix_applied`) AS t2
FROM   `facture` f
       INNER JOIN `client` c
               ON f.`fac_cli_num` = c.`cli_num`
       LEFT JOIN `ligne_facture` lf
              ON f.`fac_num` = lf.`lg_fac_num`
WHERE  `fac_num` = :fac_num
GROUP  BY `fac_num`,
          `cli_num`,
          `cli_nom`,
          `cli_prenom`
';
			$paramx = [
				':fac_num' => $this->fac_num
			];
			Globale::$resultat = Globale::$db->request($sql, $paramx);

			Globale::$Tpl->assign_vars([
				'FAC_NUM' => Globale::$resultat['fac_num'],
				'CLI_NUM' => Globale::$resultat['cli_num'],
				'CLI_NOM' => Globale::$resultat['cli_nom'],
				'CLI_PRENOM' => Globale::$resultat['cli_prenom'],
				'TOTAL_BASE' => Fonction::build_web_price(Globale::$resultat['t1']),
				'TOTAL_APPLIED' => Fonction::build_web_price((Globale::$resultat['fac_provisoire']) ? Globale::$resultat['t1'] : Globale::$resultat['t2'])
			]);

			$sql2 = '
SELECT *
FROM   `ligne_facture` l
       INNER JOIN `prestation` p
               ON l.`lg_pres_num` = p.`pres_num`
WHERE  `lg_fac_num` = :fac_num
UNION
SELECT *
FROM   `ligne_facture` l
       INNER JOIN `prestation` p
               ON l.`lg_pres_num` = p.`pres_num`
WHERE  `lg_fac_num` IN (SELECT `fac_num`
                        FROM   `facture`
                        WHERE  `fac_included` = :fac_num)
';
			Globale::$db->requete($sql2, $paramx);
			$result = Globale::$db->tableau();
			if (count($result) > 0) {
				Globale::$Tpl->create_block('is_lg_fac');
				foreach ($result as $res) {
					Globale::$Tpl->assign_block_vars('lst_ligne_fac', [
						'FAC_NUM' => $res['lg_fac_num'],
						'PRES_NUM' => $res['pres_num'],
						'PRES_LIBEL' => $res['pres_libelle'],
						'PRES_PRIX_BASE' => Fonction::build_web_price((Globale::$resultat['fac_provisoire']) ? $res['pres_prix'] : $res['lg_pres_prix_base']),
						'PRES_PRIX_APPLIED' => Fonction::build_web_price((Globale::$resultat['fac_provisoire']) ? $res['pres_prix'] : $res['lg_pres_prix_applied']),
						'PRES_REMISE' => $res['lg_pres_remise'],
					]);
				}
			}

			$sql3 = 'SELECT * FROM `famille` ORDER BY `fam_order` ASC';
			Globale::$db->requete($sql3);
			$famx = Globale::$db->tableau();

			foreach ($famx as $res) {
				Globale::$Tpl->assign_block_vars('lst_fam', [
					'FAM_NUM' => $res['fam_num'],
					'FAM_LIBEL' => $res['fam_libelle']
				]);
			}

			$sql4 = 'SELECT `fac_num` FROM `facture` WHERE `fac_included` = :fac_num ORDER BY `fac_num` ASC';
			Globale::$db->requete($sql4, ['fac_num' => $this->fac_num]);
			$facx = Globale::$db->tableau();
			if (count($facx) > 0) {
				Globale::$Tpl->create_block('is_included_fac');
				foreach ($facx as $res) {
					Globale::$Tpl->assign_block_vars('lst_fac_included', [
						'FAC_NUM' => $res['fac_num'],
					]);
				}
			}
		} elseif (isset($this->cli_num)) {
			$sql = '
INSERT INTO `facture`
            (`fac_num`,
             `fac_cli_num`,
             `fac_date`,
             `fac_provisoire`)
VALUES     (NULL,
            :cli_num,
            :fac_date,
            true) ';
			$paramx = [
				':cli_num' => $this->cli_num,
				':fac_date' => date('d/m/Y')
			];
			Globale::$db->requete($sql, $paramx);
			$fac_num = Globale::$db->last_insert_id();
			$url = 'index.php?p=facturation&amp;f=' . $fac_num;
			Http::redirect($url);
		}

		Globale::$header->parse_header();
		Globale::$Tpl->pparse('pages_facturation');
		///
		Globale::$footer->afficheFooter();
	}

}
