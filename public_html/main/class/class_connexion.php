<?php

/**
 * Class de gestion des connexions utilisateurs
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Connexion {

    public static function login($login, $pwd, $use_auto_connect = FALSE) {
        $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
        $user_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : NULL;

        Globale::$requete = Globale::$db->requete('SELECT clients.* FROM clients WHERE user_mail = "' . Globale::$db->escape($login) . '" AND user_pwd = "' . Globale::$db->escape(Password::hash($pwd, 'sha1', false)) . '" AND user_ban = 0');

        if (Globale::$db->compte(Globale::$requete) > 0) {
            Globale::$resultat = Globale::$db->tableau(Globale::$requete);
            $_SESSION['id'] = Globale::$resultat[0];
            $_SESSION['mail'] = Globale::$resultat[4];
            $_SESSION['pseudo'] = Globale::$resultat[2] . ' ' . Globale::$resultat[1];
            $_SESSION['remise_1'] = Globale::$resultat['user_remise_1'];
            $_SESSION['remise_2'] = Globale::$resultat['user_remise_2'];
            $_SESSION['last_connexion'] = Globale::$resultat['user_last_connexion'];
            $_SESSION['user_civil'] = Globale::$resultat['user_civil'];
            $_SESSION['user_nom'] = Globale::$resultat['user_lname'];
            $_SESSION['user_pnom'] = Globale::$resultat['user_fname'];
            $_SESSION['langue'] = Globale::$resultat['user_lang'];
            $_SESSION['auth'] = Globale::$resultat['user_auth'];

            $result = Globale::$db->request('SELECT date_connexion FROM connexion WHERE id_connexion = (SELECT MAX(id_connexion) FROM connexion WHERE id_personnel = ' . Globale::$resultat[0] . ')');

            $_SESSION['last_connexion'] = $result[0];

            Globale::$db->requete('INSERT INTO connexion VALUES(null, ' . $_SERVER['REQUEST_TIME'] . ', "' . $ip . '", "' . Globale::$db->escape($user_agent) . '", ' . intval(Globale::$resultat[0]) . ')');

            return true;
        } else {
            return false;
        }
    }

    public static function logout() {
        // On détruit les variables de session et on détruit la session
        unset($_SESSION['id'], $_SESSION['auth'], $_SESSION['pseudo'], $_SESSION['last_connexion'], $_SESSION['mail']);

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', $_SERVER['REQUEST_TIME'] - 42000, '/');
        }
    }

    public static function is_logged() {
        return ((isset($_SESSION['id']) && !empty($_SESSION['id'])) ? TRUE : FALSE);
    }

}

?>