<?php

/**
 * Classe de gestion des logs session
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Log_session extends Log {

    /**
     * Ajoute un produit
     * 	@param array $valeur informations sur le produit
     */
    public function ajouter($valeur) {
        if (empty($_SESSION['log_produit']) || !isset($_SESSION['log_produit'])) {
            $_SESSION['log_produit']['test'] = [];
        }

        if (count($_SESSION['log_produit']['test']) >= 10) {
            list($id, ) = each($_SESSION['log_produit']['test']);
            unset($_SESSION['log_produit']['test'][$id]);
        }

        foreach ($valeur as $key => $value) {
            if (!in_array($value, $_SESSION['log_produit']['test'])) {
                $_SESSION['log_produit']['test'][$key] = $value;
            }
        }
    }

    /**
     * Affiche la liste des produits récemment consultés
     * 	@return string
     */
    public function lire() {
        $return = '';

        if (!empty($_SESSION['log_produit']) || isset($_SESSION['log_produit'])) {
            $return .= '<div class="width-832 center bordertop borderleft">';

            foreach ($_SESSION['log_produit'] as $log) {
                foreach ($log as $key => $value) {
                    list($ref, $designation, $prix, $reduction, $fam, $cat) = explode('|', $key);

                    $return .= '<div class="width-full fond_gris borderbottom borderright height-80px pointer" onclick="self.location.href = \'detail-produit-' . $value . '-' . $fam . '-' . $cat . '.html\'"><div class="float_left margin-top-5 padding-left">';

                    if (file_exists(ROOT . DIR_MPIC . str_replace(' ', '-', strtolower($ref)) . '-1.jpg')) {
                        $return .= '<img src="./templates/images/miniatures/' . str_replace(' ', '-', strtolower($ref)) . '-1.jpg" alt="" class="border" />';
                    } else {
                        $return .= '<img src="./templates/images/miniatures/photo_not_available.jpg" alt="" class="border" />';
                    }

                    $return .= '</div><div class="float_left padding-left padding-top">' . Header::$lang->lang('t_ref_art') . ' : <span class="gras sougline">' . $ref . '</span><br /><br />' . Header::$lang->lang('t_design') . ' : ' . Fonction::tronquer(Fonction::ucfirstLetter($designation), 0, 70) . '</div><div class="float_right padding-right padding-top">';

                    if ($reduction != 0) {
                        $return .= '<span class="gras">' . $reduction . ' &euro;</span><br /><br /><img src="./templates/images/caddie.gif" alt="panier" class="float_right" /></div></div>';
                    } else {
                        $return .= '<span class="gras">' . $prix . ' &euro;</span><br /><br /><img src="./templates/images/caddie.gif" alt="panier" class="float_right" /></div></div>';
                    }
                }
            }

            $return .= '</div>';
        } else {
            $return .= Header::$lang->lang('no_last_produit');
        }

        return $return;
    }

}

?>