<?php

/**
 * Class de gestion des chaines de caractères
 */
class String {

    // Correspond aux offset générés par les fonctions preg_*() avec le flag PREG_OFFSET_CAPTURE
    const CHAR = 0;
    const OFFSET = 1;

    /*
     * Ajoute un espace dans les mots trop long
     * -----
     * $str ::		Chaîne à traiter
     * $length ::	Longueur maximale de chaque mot
     */

    public static function truncate($str, $length) {
        $merge = [];
        foreach (explode(' ', $str) AS $word) {
            if (strlen($str) > $length) {
                $word = wordwrap($word, $length, ' ', 1);
            }
            $merge[] = $word;
        }
        return (implode(' ', $merge));
    }

    /*
     * Convertit une chaîne UTF8 en minuscule
     * -----
     * $str ::		Chaîne à convertir
     */

    public static function strtolower($str) {
        return (strtr($str, $GLOBALS['UTF8_UPPER_TO_LOWER']));
    }

    /*
     * Convertit une chaîne UTF8 en majuscule
     * -----
     * $str ::		Chaîne à convertir
     */

    public static function strtoupper($str) {
        return (strtr($str, $GLOBALS['UTF8_LOWER_TO_UPPER']));
    }

    /*
     * Substr gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * -----
     * $str ::		Chaîne de caractère
     * $start ::	Offset de départ pour la tronquature
     * $length ::	Longueur du texte à tronquer
     */

    public static function substr($str, $start = 0, $length = NULL) {
        if (PHP_EXTENSION_MBSTRING) {
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

    /*
     * Substr gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * -----
     * $str ::		Chaîne de caractère
     * $start ::	Offset de départ pour la tronquature
     * $length ::	Longueur du texte à tronquer
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

    /*
     * Strlen gérant l'UTF-8, fonction reprise de SPIP (http://www.spip.net)
     * -----
     * $str ::		Chaîne de caractère
     */

    public static function strlen($str) {
        if (PHP_EXTENSION_MBSTRING) {
            return (mb_strlen($str));
        }

        return (strlen(preg_replace('#[\x80-\xBF]#S', '', $str)));
    }

    /*
     * Converti en entités HTML des données qui ont été encodées par escape() en javascript.
     * Cette fonction est indispensable dans votre traitement AJAX côté PHP, car les fonctions
     * PHP ne reconnaissent pas les caractères unicode encodées de la forme %uxxxx.
     * -----
     * $str ::		Chaîne de caractère à décoder
     */

    public static function utf8_decode($str) {
        return (preg_replace('#%u([[:alnum:]]{4})#i', '&#x\\1;', $str));
    }

    /*
     * Ajoute des zeros manquants en début de chaine, tant que $str est inférieur à $total
     * -----
     * $str ::		Chaine initiale
     * $total ::	Nombre de caractères totaux que devra comporter la chaine à la fin
     */

    public static function add_zero($str, $total) {
        while (strlen($str) < $total) {
            $str = '0' . $str;
        }
        return ($str);
    }

    /*
     * Inverse d'htmlspecialchars()
     * -----
     * $str ::		Chaîne de caractère
     */

    public static function unhtmlspecialchars($str) {
        return (str_replace(['&lt;', '&gt;', '&amp;', '&quot;'], ['<', '>', '&', '"'], $str));
    }

    /*
     * Encode une chaîne de caractère en caractères héxadécimaux visibles par
     * le navigateur, afin d'offrir une protection contre la lecture de données dans
     * la source de la page (anti spam)
     * ----
     * $str ::		Chaîne de caractère à encoder
     */

    public static function no_spam($str) {
        $new = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $new .= '&#x' . bin2hex($str[$i]) . ';';
        }
        return ($new);
    }

    /*
     * Renvoie TRUE si $word match $pattern
     * -----
     * $pattern ::		Chaîne de caractère acceptant comme caractère spécial
     * $word ::			Chaîne vérifiant le pattern
     */

    public static function is_matching($pattern, $word) {
        return ((preg_match('/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/i', $word)) ? TRUE : FALSE);
    }

    /*
     * Fonction récursive qui vérifie si un caractère est échappé par un \
     * -----
     * $pos ::		Position du caractère dans la chaîne
     * $str ::		Chaîne de caractère
     */

    public static function is_escaped($pos, &$str) {
        if (($pos - 1) >= 0 && $str[$pos - 1] != '\\') {
            return (FALSE);
        } elseif (($pos - 1) >= 0 && ($pos - 2) >= 0 && $str[$pos - 1] == '\\' && $str[$pos - 2] != '\\') {
            return (TRUE);
        } elseif (($pos - 1) >= 0 && ($pos - 2) >= 0 && $str[$pos - 1] == '\\' && $str[$pos - 2] == '\\') {
            return (self::is_escaped($pos - 2, $str));
        }
        return (FALSE);
    }

    /*
     * Découpe une chaîne de caractère en plusieurs sous chaîne dans un tableau, en fonction de délimiteurs.
     * Les chaînes comprises dans des quote ne sont pas découpées.
     * -----
     * $del ::	Délimiteur (ou tableau de délimiteur)
     * $str ::	Chaîne à découper
     */

    public static function split($del, $str) {
        if (!is_array($del)) {
            $del = [$del];
        }

        // Découpage de la chaîne en fonction des délimiteurs et des quotes ' et "
        $del[] = '\'';
        $del[] = '"';
        preg_match_all('/(\\\)*(' . str_replace('/', '\/', implode('|', $del)) . ')/', $str, $m, PREG_OFFSET_CAPTURE);
        $count = count($m[0]);

        $return = [];
        $tmp = '';
        $last_offset = 0;
        $current_quote = '';
        for ($i = 0; $i < $count; $i++) {
            // En cas de quote ou de simple quote, on ne prendra pas en compte les délimiteurs
            if ($m[2][$i][self::CHAR] == '\'' || $m[2][$i][self::CHAR] == '"') {
                if (!self::is_escaped(strlen($m[0][$i][self::CHAR]) - 1, $m[0][$i][self::CHAR])) {
                    $tmp .= substr($str, $last_offset, $m[0][$i][self::OFFSET] - $last_offset);
                    if ($m[2][$i][self::CHAR] == $current_quote) {
                        $tmp .= $current_quote;
                        $current_quote = '';
                    } elseif (!$current_quote) {
                        $current_quote = $m[2][$i][self::CHAR];
                        $tmp .= $current_quote;
                    } else {
                        $tmp .= $m[2][$i][self::CHAR];
                    }
                } else {
                    // Les quote echapés par des \ ne sont pas gardés
                    $tmp .= $m[0][$i][self::CHAR];
                }
            } else {
                // On sauve en mémoire la chaîne entre deux délimiteurs. Si on est dans un quote, on garde le tout
                // sous forme de chaîne unique, sinon on ajoute de nouveaux éléments au tableau
                $tmp .= substr($str, $last_offset, $m[0][$i][self::OFFSET] - $last_offset);
                if ($current_quote) {
                    $tmp .= $m[0][$i][self::CHAR];
                } elseif ($tmp) {
                    $return[] = $tmp;
                    $tmp = '';
                }
            }
            $last_offset = $m[0][$i][self::OFFSET] + strlen($m[0][$i][self::CHAR]);
        }

        $tmp .= substr($str, $last_offset, strlen($str) - $last_offset);
        if (trim($tmp)) {
            $return[] = $tmp;
        }

        return ($return);
    }

}
