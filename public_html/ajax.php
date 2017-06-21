<?php

/**
 * Déclaration des fonctions AJAX
 */
// On lance les sessions
session_start();

// On défini trois constantes
define('PHPEXT', substr(strrchr(__FILE__, '.'), 1));
define('ROOT', './');

// On inclus le fichier de chargement des classes et des instanciations respectives
include_once(ROOT . 'main/start.' . PHPEXT);

// Charset français
Http::header('Content-Type', 'text/html; charset=UTF-8');

// On ne fait pas de mise en cache
Http::no_cache();

// On défini des évènements AJAX
Globale::$ajax->add_event(Ajax::TXT, 'addService', 'ajax_add_service', Http::request('details', 'post'), Http::request('cli_num', 'post'), Http::request('fam_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'editService', 'ajax_edit_service', Http::request('svc_num', 'post'), Http::request('details', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'delService', 'ajax_del_service', Http::request('svc_num', 'post'));
Globale::$ajax->add_event(Ajax::JSON, 'getService', 'ajax_get_service', Http::request('svc_num', 'post'));

Globale::$ajax->add_event(Ajax::TXT, 'addDepense', 'ajax_add_depense', Http::request('libel', 'post'), Http::request('prix', 'post'), Http::request('date', 'post'));

Globale::$ajax->add_event(Ajax::TXT, 'addClient', 'ajax_add_client', Http::request('cli_nom', 'post'), Http::request('cli_prenom', 'post'), Http::request('cli_adr', 'post'), Http::request('cli_cp', 'post'), Http::request('cli_ville', 'post'), Http::request('cli_tel', 'post'), Http::request('cli_mobile', 'post'), Http::request('cli_email', 'post'), Http::request('cli_naiss', 'post'), Http::request('cli_genre', 'post'), Http::request('cli_sexe', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'editClient', 'ajax_edit_client', Http::request('cli_num', 'post'), Http::request('cli_nom', 'post'), Http::request('cli_prenom', 'post'), Http::request('cli_adr', 'post'), Http::request('cli_cp', 'post'), Http::request('cli_ville', 'post'), Http::request('cli_tel', 'post'), Http::request('cli_mobile', 'post'), Http::request('cli_email', 'post'), Http::request('cli_naiss', 'post'), Http::request('cli_genre', 'post'), Http::request('cli_sexe', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'delClient', 'ajax_del_client', Http::request('cli_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'getPres', 'ajax_get_pres', Http::request('fam_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'addPres', 'ajax_add_pres', Http::request('fac_num', 'post'), Http::request('lst_pres', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'delPresFac', 'ajax_del_pres_fac', Http::request('fac_num', 'post'), Http::request('pres_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'validateFac', 'ajax_validate_fac', Http::request('fac_num', 'post'), Http::request('pres_num', 'post'), Http::request('pres_remise', 'post'), Http::request('pres_prix_base', 'post'), Http::request('pres_prix_applied', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'delFac', 'ajax_del_fac', Http::request('fac_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'addPai', 'ajax_add_pai', Http::request('fac_num', 'post'), Http::request('montants_pai', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'getWaitingFac', 'ajax_get_waiting_fac', Http::request('fac_num', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'addWaitingFac', 'ajax_add_waiting_fac', Http::request('fac_num', 'post'), Http::request('lst_fac', 'post'));
Globale::$ajax->add_event(Ajax::TXT, 'delIncludedfac', 'ajax_del_included_fac', Http::request('fac_num', 'post'));

// On déclenche un évènement
Globale::$ajax->trigger(Http::request('mod', 'post'));

/**
 * Edite les services d'une fiche client
 * @param type $svc_num
 * @param type $details
 * @return string
 */
function ajax_edit_service($svc_num, $details) {
    $sql = 'UPDATE `service` SET svc_details = :details WHERE `svc_num` = :svc_num';
    $paramx = [
        ':svc_num' => $svc_num,
        ':details' => $details,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Ajoute un service dans une fiche client
 * @param type $details
 * @param type $cli_num
 * @param type $fam_num
 * @return string
 */
function ajax_add_service($details, $cli_num, $fam_num) {
    $sql = 'INSERT INTO `service`(`svc_num`, `svc_cli_num`, `svc_fam_num`, `svc_date`, `svc_details`) VALUES(null, :cli_num, :fam_num, :time, :details)';
    $paramx = [
        ':cli_num' => $cli_num,
        ':fam_num' => $fam_num,
        ':time' => date('d/m/Y'),
        ':details' => $details,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Supprime un service d'une fiche client
 * @param type $svc_num
 * @return string
 */
function ajax_del_service($svc_num) {
    $sql = 'DELETE FROM `service` WHERE `svc_num` = :svc_num';
    $paramx = [
        ':svc_num' => $svc_num,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Récupère la liste des services disponibles
 * @param type $svc_num
 * @return type
 */
function ajax_get_service($svc_num) {
    $sql = 'SELECT `fam_libelle`, `svc_date`, `svc_details` FROM `service` s INNER JOIN `famille` f ON s.`svc_fam_num` = f.`fam_num` WHERE `svc_num`= :svc_num';
    $paramx = [':svc_num' => $svc_num];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    Globale::$resultat = Globale::$db->row();
    if (Globale::$requete) {
        $return = Globale::$resultat;
        return json_encode($return);
    }
}

/**
 * Ajoute une dépense
 * @param type $libel
 * @param type $prix
 * @param type $date
 * @return string
 */
function ajax_add_depense($libel, $prix, $date) {
    $sql = 'INSERT INTO `achat` VALUES(null, :libel, :prix, :date)';
    $paramx = [
        ':libel' => $libel,
        ':prix' => $prix,
        ':date' => $date,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'Enregistré';
    }
}

/**
 * Ajoute un client
 * @param type $cli_nom
 * @param type $cli_prenom
 * @param type $cli_adr
 * @param type $cli_cp
 * @param type $cli_ville
 * @param type $cli_tel
 * @param type $cli_mobile
 * @param type $cli_email
 * @param type $cli_naiss
 * @param type $cli_genre
 * @param type $cli_sexe
 * @return string
 */
function ajax_add_client($cli_nom, $cli_prenom, $cli_adr, $cli_cp, $cli_ville, $cli_tel, $cli_mobile, $cli_email, $cli_naiss, $cli_genre, $cli_sexe) {
    $sql = 'INSERT INTO `client` VALUES(null, :cli_nom, :cli_prenom, :cli_adr, :cli_cp, :cli_ville, :cli_tel, :cli_mobile, :cli_email, :cli_naiss, :cli_genre, :cli_sexe)';
    $paramx = [
        ':cli_nom' => $cli_nom,
        ':cli_prenom' => $cli_prenom,
        ':cli_adr' => $cli_adr,
        ':cli_cp' => $cli_cp,
        ':cli_ville' => $cli_ville,
        ':cli_tel' => $cli_tel,
        ':cli_mobile' => $cli_mobile,
        ':cli_email' => $cli_email,
        ':cli_naiss' => $cli_naiss,
        ':cli_genre' => $cli_genre,
        ':cli_sexe' => $cli_sexe,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Edite une fiche client
 * @param type $cli_num
 * @param type $cli_nom
 * @param type $cli_prenom
 * @param type $cli_adr
 * @param type $cli_cp
 * @param type $cli_ville
 * @param type $cli_tel
 * @param type $cli_mobile
 * @param type $cli_email
 * @param type $cli_naiss
 * @param type $cli_genre
 * @param type $cli_sexe
 * @return string
 */
function ajax_edit_client($cli_num, $cli_nom, $cli_prenom, $cli_adr, $cli_cp, $cli_ville, $cli_tel, $cli_mobile, $cli_email, $cli_naiss, $cli_genre, $cli_sexe) {
    $sql = 'UPDATE `client` SET `cli_nom` = :cli_nom, `cli_prenom` = :cli_prenom, `cli_adr` = :cli_adr, `cli_cp` = :cli_cp, `cli_ville` = :cli_ville, `cli_tel` = :cli_tel, `cli_mobile` = :cli_mobile, `cli_email` = :cli_email, `cli_naiss` = :cli_naiss, `cli_genre` = :cli_genre, `cli_sexe` = :cli_sexe WHERE `cli_num` = :cli_num';
    $paramx = [
        ':cli_num' => $cli_num,
        ':cli_nom' => $cli_nom,
        ':cli_prenom' => $cli_prenom,
        ':cli_adr' => $cli_adr,
        ':cli_cp' => $cli_cp,
        ':cli_ville' => $cli_ville,
        ':cli_tel' => $cli_tel,
        ':cli_mobile' => $cli_mobile,
        ':cli_email' => $cli_email,
        ':cli_naiss' => $cli_naiss,
        ':cli_genre' => $cli_genre,
        ':cli_sexe' => $cli_sexe,
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Supprime un client
 * @param type $cli_num
 * @return string
 */
function ajax_del_client($cli_num) {
    $sql = 'DELETE FROM `client` WHERE `cli_num` = :cli_num';
    $paramx = [
        ':cli_num' => $cli_num
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Récupère les prestations d'une facture
 * @param type $fam_num
 * @return type
 */
function ajax_get_pres($fam_num) {
    $sql = 'SELECT `pres_num`, `pres_libelle`, `pres_prix` FROM `prestation` WHERE `fam_num`= :fam_num';
    $paramx = [':fam_num' => $fam_num];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    Globale::$resultat = Globale::$db->tableau();

    function walker(&$item1, $key) {
        $item1['pres_prix_build'] = Fonction::build_web_price($item1['pres_prix']);
    }

    if (Globale::$requete) {
        $return = Globale::$resultat;
        array_walk($return, 'walker');
        return json_encode($return);
    }
}

/**
 * Ajoute des prestations à une facture
 * @param type $fac_num
 * @param type $lst_pres
 * @return string
 */
function ajax_add_pres($fac_num, $lst_pres) {
    $datafields = [
        'lg_fac_num',
        'lg_pres_num'
    ];
    foreach ($lst_pres as $d) {
        $question_marks[] = '(' . $fac_num . ', ' . Fonction::placeholders('?', sizeof($d)) . ')';
    }

    $sql = "INSERT INTO `ligne_facture` (" . implode(",", $datafields) . ") VALUES " . implode(',', $question_marks);

    Globale::$db->requete($sql, $lst_pres);

    $insert = 'UPDATE `ligne_facture` lf INNER JOIN `prestation` p ON p.`pres_num` = lf.`lg_pres_num` SET lf.`lg_pres_prix_base` = p.`pres_prix` WHERE p.`pres_num` IN (' . implode(',', $lst_pres) . ')';

    Globale::$requete = Globale::$db->requete($insert);

    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Supprime une prestation d'une facture
 * @param type $fac_num
 * @param type $pres_num
 * @return string
 */
function ajax_del_pres_fac($fac_num, $pres_num) {
    $sql = 'DELETE FROM `ligne_facture` WHERE `lg_fac_num` = :fac_num AND `lg_pres_num` = :pres_num';
    $paramx = [
        ':fac_num' => $fac_num,
        ':pres_num' => $pres_num
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Valide une facture provisoire
 * @param type $fac_num
 * @param type $pres_num
 * @param type $pres_remise
 * @param type $pres_prix_base
 * @param type $pres_prix_applied
 * @return string
 */
function ajax_validate_fac($fac_num, $pres_num, $pres_remise, $pres_prix_base, $pres_prix_applied) {
    $sql = 'UPDATE `ligne_facture` lg, `facture` f SET `lg_pres_remise` = :pres_remise, `lg_pres_prix_base` = :pres_prix_base, `lg_pres_prix_applied` = :pres_prix_applied, f.`fac_provisoire` = 0 WHERE (f.`fac_num` = lg.`lg_fac_num`) AND `lg_fac_num` = :fac_num AND `lg_pres_num` = :pres_num';

    $paramx = [
        ':fac_num' => $fac_num,
        ':pres_num' => $pres_num,
        ':pres_remise' => $pres_remise,
        ':pres_prix_base' => $pres_prix_base,
        ':pres_prix_applied' => $pres_prix_applied
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Supprime une facture
 * @param type $fac_num
 * @return string
 */
function ajax_del_fac($fac_num) {
    $sql = 'DELETE f, lf FROM `facture` f LEFT JOIN `ligne_facture` lf ON f.`fac_num` = lf.`lg_fac_num` WHERE `fac_num` = :fac_num';
    $paramx = [
        ':fac_num' => $fac_num
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Ajoute les modes de paiement et les montants associés
 * @param type $fac_num
 * @param type $montants_pai
 * @return string
 */
function ajax_add_pai($fac_num, $montants_pai) {
    $sql = 'INSERT INTO `paiement` VALUES(null, :fac_num, :type_pai, :montant_pai)';

    foreach ($montants_pai as $k => $v) {
        if ($v != 0) {
            $paramx = [
                ':fac_num' => $fac_num,
                ':type_pai' => $k,
                ':montant_pai' => $v
            ];
            Globale::$requete = Globale::$db->requete($sql, $paramx);
        }
    }
    return 'successfull';
}

/**
 * Récupère les facture provisoire sauf celle courante
 * @param type $fac_num
 * @return type
 */
function ajax_get_waiting_fac($fac_num) {
    $sql = 'SELECT `fac_num`, `cli_nom`, `cli_prenom` FROM `client` c INNER JOIN `facture` f ON c.`cli_num` = f.`fac_cli_num` WHERE f.`fac_provisoire` = 1 AND f.fac_num != :fac_num';
    $paramx = [
        ':fac_num' => $fac_num
    ];
    Globale::$requete = Globale::$db->requete($sql, $paramx);
    Globale::$resultat = Globale::$db->tableau();

    if (Globale::$requete) {
        return json_encode(Globale::$resultat);
    }
}

/**
 * Associe des factures à une autre
 * @param type $fac_num
 * @param type $lst_fac
 * @return string
 */
function ajax_add_waiting_fac($fac_num, $lst_fac) {
    $paramx = [
        ':fac_num' => $fac_num
    ];
    foreach ($lst_fac as $d) {
        Globale::$requete = Globale::$db->requete('UPDATE `facture` SET `fac_included` = :fac_num, `fac_provisoire` = 0 WHERE `fac_num` = ' . $d, $paramx);
    }

    if (Globale::$requete) {
        return 'successfull';
    }
}

/**
 * Supprime l'association d'une facture
 * @param type $fac_num */
function ajax_del_included_fac($fac_num) {
    $paramx = [
        ':fac_num' => $fac_num
    ];
    Globale::$requete = Globale::$db->requete('UPDATE `facture` SET `fac_included` = 0, `fac_provisoire` = 1 WHERE `fac_num` = :fac_num', $paramx);

    if (Globale::$requete) {
        return 'successfull';
    }
}

// On termine le script courant
exit;
