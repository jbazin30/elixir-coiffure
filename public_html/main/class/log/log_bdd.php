<?php

/**
 * Classe de gestion des log mysql
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Log_bdd extends Log {

    /**
     * Ajoute un produit
     * 	@param array $valeur informations sur le produit
     */
    public function ajouter($valeur, $client, $tb) {
        $requete = Globale::$db->requete('SELECT * FROM ' . $tb . ' WHERE log_client = ' . Globale::$db->escape($client) . ' AND log_contenu = "' . Globale::$db->escape($valeur) . '"');

        if (Globale::$db->compte($requete) === 0) {
            Globale::$db->requete('INSERT INTO ' . $tb . ' VALUES(null, ' . $client . ', "' . $valeur . '", ' . time() . ', 1);');
        } else {
            Globale::$db->requete('UPDATE ' . $tb . ' SET log_nb = log_nb + 1, log_date = ' . time() . ' WHERE log_client = ' . Globale::$db->escape($client) . ' AND log_contenu = "' . Globale::$db->escape($valeur) . '"');
        }

        Globale::$db->free($requete);
    }

    /**
     * Affiche la liste des produits récemment consultés
     * 	@return string
     */
    public function lire($client, $operator, $date, $prod = true) {
        // On converti la date saisie en timestamp pour la comparaison avec la base de données
        @list($jour, $mois, $annee) = explode('/', $date);

        $today = @mktime(0, 0, 0, $mois, $jour, $annee);

        if (empty($date)) {
            $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $operator = '<=';
        }

        if ($prod) {
            $requete = Globale::$db->requete('SELECT prod_ref_art, log_nb, log_date, user_lname, user_fname, user_compagny FROM logs, produit, clients WHERE log_contenu = prod_id AND log_client = user_id AND log_client = ' . Globale::$db->escape($client) . ' AND log_date ' . Globale::$db->escape($operator) . ' ' . Globale::$db->escape($today));
        } else {
            $requete = Globale::$db->requete('SELECT log_contenu, log_nb, log_date, user_lname, user_fname, user_compagny FROM logs, clients WHERE log_client = user_id AND log_client = ' . Globale::$db->escape($client) . ' AND log_date ' . Globale::$db->escape($operator) . ' ' . Globale::$db->escape($today) . ' AND log_contenu REGEXP "^[a-z]"');
        }

        return $requete;
    }

}

?>