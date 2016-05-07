<?php

/**
 * Class de méthode static
 */
class Fonction {

    public static $a = [];

    /**
     * Charge un fichier de langue
     */
    public static function charge_lang() {
        if (!isset($_SESSION['langue']) || empty($_SESSION['langue'])) {
            $langue = Http::request('lang', 'get');

            if (empty($langue)) {
                $_SESSION['langue'] = LANG_DEFAULT;
            } else {
                $_SESSION['langue'] = $langue;
            }
        }
        Header::load_lang($_SESSION['langue']);
    }

    /**
     * Contrôle la conformité d'une adresse email
     * 	@return true|false
     */
    public static function verifMail($email) {
        if ((preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#', $email)) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retourne le temps actuel en microsecondes
     * 	@return float
     */
    public static function getmicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * Détermine l'existence d'une variable et si elle contient une valeur non nulle :
     * 		0 ,
     * 		"" (chaîne vide),
     * 		"0" (0 en tant que chaîne de caractères),
     * 		null,
     * 		false,
     * 		array() (tableau vide),
     * 		var $var (déclaration sans valeur)
     * 	@param string $string Chaîne de caractère à traiter
     * 	@return string
     */
    public static function check_variable($string) {
        if ((isset($string) && !empty($string))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Supprime les caractères inutiles d'une chaîne de caractères
     * 	@param string $str Chaîne de caractère à traiter
     * 	@return string
     */
    public static function stripChar($str, $charset = 'UTF-8') {
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
        $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#\&[^;]+\;#', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace(':', '', $str);
        return $str;
    }

    /**
     * Vérifie la validité d'un code entré pour une image de confirmation visuelle
     * 	@param string $code Code de confirmation à vérifier
     * 	@return true|false
     */
    public static function check_captcha($code) {
        $current_code = $_SESSION['image_response'];
        if (!$current_code || strpos($current_code, ':') === FALSE) {
            return (FALSE);
        }
        $current_code = substr($current_code, 2);
        return ((strtolower($code) === strtolower($current_code)) ? TRUE : FALSE);
    }

    /**
     * Met la première lettre de la chaîne en majuscule et le reste en minuscule
     * 	@param string $str Chaîne de caractère
     * 	@return string
     */
    public static function ucfirstLetter($str) {
        return ( ucfirst(self::strtolower($str)) );
    }

    /**
     * Un print_r() directement formaté
     * 	@param array $array Tableau à retourner formaté
     * 	@return string
     */
    public static function printr($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * Convertit une chaîne UTF8 en minuscule
     * 	@param string $str Chaîne de caractère
     * 	@return string
     */
    public static function strtolower($str) {
        return (strtr($str, $GLOBALS['UTF8_UPPER_TO_LOWER']));
    }

    /**
     * Convertit une chaîne UTF8 en majuscule
     * 	@param string $str Chaîne de caractère
     * 	@return string
     */
    public static function strtoupper($str) {
        return (strtr($str, $GLOBALS['UTF8_LOWER_TO_UPPER']));
    }

    /**
     * Substr gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * 	@param string $str Chaîne de caractère
     * 	@param integer $start Offset de départ pour la tronquature
     * 	@param integer $length Longueur du texte à tronquer
     * 	@return string
     */
    public static function substr($str, $start = 0, $length = NULL) {
        if (Header::$lang->lang('charset') != 'UTF-8') {
            $fct = 'substr';
        } elseif (PHP_EXTENSION_MBSTRING) {
            $fct = 'mb_substr';
        } else {
            if ($length) {
                return (self::substr_manual($str, $start, $length));
            }
            return (self::substr_manual($str, $start));
        }

        if ($length) {
            return ($fct($str, $start, $length));
        }
        return ($fct($str, $start));
    }

    /**
     * Substr gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * 	@param string $str Chaîne de caractère
     * 	@param integer $start Offset de départ pour la tronquature
     * 	@param integer $length Longueur du texte à tronquer
     * 	@return string
     */
    public static function substr_manual($str, $start, $length = 0) {
        if ($length === 0) {
            return ('');
        }

        if ($start > 0) {
            $d = self::substr($str, 0, $start);
            $str = substr($str, strlen($d));
        }

        if ($start < 0) {
            $d = self::substr($str, 0, $start);
            $str = substr($str, -strlen($d));
        }

        if (!$length) {
            return ($str);
        }

        if ($length > 0) {
            $str = substr($str, 0, 5 * $length);
            while (($l = self::strlen($str)) > $length) {
                $str = substr($str, 0, $length - $l);
            }
            return ($str);
        }

        if ($length < 0) {
            $fin = substr($str, 5 * $length);
            while (($l = self::strlen($fin)) > -$length) {
                $fin = substr($fin, $length + $l);
                $fin = preg_replace(',^[\x80-\xBF],S', 'x', $fin);
            }
            return (substr($str, -strlen($fin)));
        }
    }

    /**
     * Strlen gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * 	@param string $str Chaîne de caractère
     * 	@return string
     */
    public static function strlen($str) {
        if (Header::$lang->lang('charset') != 'UTF-8') {
            return (strlen($str));
        }

        if (PHP_EXTENSION_MBSTRING) {
            return (mb_strlen($str));
        }
        return (strlen(preg_replace('#[\x80-\xBF]#S', '', $str)));
    }

    /**
     * Vérifie si le caractère est une lettre minuscule
     * -----
     * 	@param char ::	Caractère
     * -----
     * 	retourne vrai ou faux
     * */
    public static function is_alpha_min($char) {
        if ($char >= 'a' && $char <= 'z') {
            return (TRUE);
        }
        return (FALSE);
    }

    /**
     * Vérifie si le caractère est une lettre majuscule
     * -----
     * 	@param char ::	Caractère
     * -----
     * 	retourne vrai ou faux
     * */
    public static function is_alpha_maj($char) {
        if ($char >= 'A' && $char <= 'Z') {
            return (TRUE);
        }
        return (FALSE);
    }

    /**
     * Vérifie si le caractère est un nombre
     * -----
     * 	@param char ::	Caractère
     * -----
     * 	retourne vrai ou faux
     * */
    public static function is_number($char) {
        if ($char >= '0' && $char <= '9') {
            return (TRUE);
        }
        return (FALSE);
    }

    public static function htmlentities_utf8($str) {
        return htmlentities($str, ENT_NOQUOTES, 'UTF-8');
    }

    public static function tronquer($chaine, $debut, $max, $ponct = '...') {
        if (strlen($chaine) >= $max) {
            $chaine = substr($chaine, $debut, $max);
            $espace = strrpos($chaine, " ");
            $chaine = substr($chaine, $debut, $espace) . $ponct;
        }
        return $chaine;
    }

    public static function php_info() {

        function phpinfo_legend_name($match) {
            $title = ($match[2]) ? $match[2] : 'phpinfo';
            return ('<fieldset><legend>' . $title . '</legend><div><table class="tab">' . $match[3] . '</table></div></fieldset>');
        }

        ob_start();
        phpinfo();
        $buffer = ob_get_contents();
        ob_end_clean();

        $buffer = preg_replace('#<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">.*?<body>#si', '', $buffer);
        $buffer = preg_replace('#</body>.*?</html>#', '', $buffer);
        $buffer = preg_replace_callback('#(<h2>(.*?)</h2>)?\s*<table border="0" cellpadding="3" width="600">(.*?)</table>#si', 'phpinfo_legend_name', $buffer);
        $buffer = preg_replace('#<tr>#', '<tr>', $buffer);
        $buffer = preg_replace('#<tr class="h">#si', '<tr>', $buffer);
        $buffer = preg_replace('#<td class="e">(.*?)</td>#si', '<td width="300"><b>\\1</b></td>', $buffer);
        $buffer = preg_replace('#<td class="v">#si', '<td>', $buffer);
        $buffer = preg_replace('#<img #si', '<img class="image_phpinfo" ', $buffer);
        $buffer = preg_replace('#<a #si', '<a class="lien_phpinfo" ', $buffer);
        $buffer = str_replace('<img class="image_phpinfo" border="0" src="' . $_SERVER['PHP_SELF'] . '?=PHPE9568F34-D428-11d2-A769-00AA001ACF42" alt="PHP Logo" />', '<img class="image_phpinfo" border="0" src="' . substr($_SERVER['PHP_SELF'], 0, -9) . 'templates/images/php.png" alt="PHP Logo" />', $buffer);
        $buffer = str_replace('name="module_Zend Optimizer"', 'name="module_Zend_Optimizer"', $buffer);

        return $buffer;
    }

    public static function mkdir($dir) {
        // Chemin d'accès vers la racine
        $ROOT_path = './';

        // Chemin local d'accès vers la racine
        $local_path = './';

        if (is_dir($dir)) {
            return;
        }

        $dirs = explode('/', $dir);
        $path = './';
        foreach ($dirs AS $cur) {
            $path .= $cur . '/';
            if (!file_exists($local_path . $path) && $cur != '.' && $cur != '..') {
                $result = mkdir($ROOT_path . $path);
                chmod($path, 0777);
            }
        }
    }

    public static function write_file($filename, $content) {
        $fd = fopen($filename, 'w');
        fputs($fd, $content);
        fclose($fd);
    }

    public static function selectChamps() {
        $champs = '';

        if ($_SESSION['langue'] !== 'fr') {
            $champs = $_SESSION['langue'] . '_';
        }

        return $champs;
    }

    public static function br2nl($string) {
        $return = eregi_replace('<br[[:space:]]*/?' . '[[:space:]]*>', chr(13) . chr(10), $string);
        return $return;
    }

    public static function str_to_url($chaine) {
        //les accents
        $chaine = trim(utf8_decode($chaine));
        $chaine = strtr($chaine, utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"), "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");

        //les caracètres spéciaux
        $chaine = preg_replace('/([^.a-z0-9]+)/i', '-', $chaine);

        return utf8_encode(strtolower($chaine));
    }

    public static function unhtmlspecialchars($str) {
        return (str_replace(['&lt;', '&gt;', '&amp;', '&quot;'], ['<', '>', '&', '"'], $str));
    }

    //Fonction qui converti une adresse IP en adresse numérique
    public function IP_to_Number($dotted) {
        $dotted = preg_split("/[.]+/", $dotted);
        $ip = (double) ($dotted[0] * 16777216) + ($dotted[1] * 65536) + ($dotted[2] * 256) + ($dotted[3]);
        return $ip;
    }

    //Fonction inverse qui convertie une adresse numérique en adresse IP
    public function Number_to_IP($number) {
        $a = ($number / 16777216) % 256;
        $b = ($number / 65536) % 256;
        $c = ($number / 256) % 256;
        $d = ($number) % 256;
        $dotted = $a . "." . $b . "." . $c . "." . $d;
        return $dotted;
    }

    public static function build_web_price($float) {
        return self::format_number($float) . ' ' . DEVISE;
    }

    public static function format_number($float) {
        return number_format(round($float, 2), 2);
    }

    public static function parsing_price($float) {
        $float = number_format($float, 2, '.', '');
        list($unite, $decimal) = explode('.', $float);
        return $unite . '&euro;<sup>' . $decimal . '</sup>';
    }

    public static function get_file_data($filename, $type) {
        $tmp = explode('.', basename($filename));
        switch ($type) {
            case 'extension' :
                return (strtolower($tmp[count($tmp) - 1]));
                break;

            case 'filename' :
                unset($tmp[count($tmp) - 1]);
                return (implode('.', $tmp));
                break;
        }
    }

    public static function check_auth($auth) {
        return ($auth == 1) ? true : false;
    }

    public static function formatHtmlPc($img, $desc, $prix) {
        return '<div style="width: 100%;"><div class="img" style="float: left; text-align: center;">' . $img . '<br /><strong style="font-size: 18pt; color: red;">' . self::parsing_price($prix) . '</strong><br /></div><div class="desc" style="float: left; width: 480px; margin-top: 30px; padding: 5px;">' . $desc . '</div></div>';
    }

    public static function formatHtmlPc_($img, $desc, $prix) {
        return '<div class="desc" style="float: left; width: 480px; margin-top: 30px; padding: 5px;">' . $desc . '</div></div><div style="width: 100%;"><div class="img" style="float: left; text-align: center;">' . $img . '<br /><strong style="font-size: 18pt; color: red;">' . self::parsing_price($prix) . '</strong><br /></div>';
    }

    public static function formatHtmlProj($img, $desc, $prix) {
        return '<div style="width: 100%;"><div class="img" style="float: left; text-align: center;">' . $img . '<br /><strong style="font-size: 18pt; color: red;">' . self::parsing_price($prix) . '</strong></div><div class="desc" style="float: left; width: 480px; margin-top: 15px; padding: 5px;">' . $desc . '</div></div>';
    }

    public static function formatHtmlCiblee($img, $desc, $prix) {
        return '<div style="width: 100%;"><div class="img" style="float: left; width: 345px; text-align: center;">' . $img . '<br /><strong style="font-size: 18pt; color: red;">' . self::parsing_price($prix) . '</strong></div><div class="desc" style="float: left; padding: 5px;">' . $desc . '</div></div>';
    }

    public static function formatHtmlDisplay($img, $desc, $prix) {
        return '<div style="width: 100%;"><div class="img" style="float: left; width: 345px; text-align: center;">' . $img . '<br /><strong style="font-size: 18pt; color: red;">' . self::parsing_price($prix) . '</strong></div><div class="desc" style="float: left; padding: 5px;">' . $desc . '</div></div>';
    }

    public static function placeholders($text, $count = 0, $separator = ",") {
        $result = [];
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }

}
