<?php

/**
 * Class de manipulation des en-têtes PHP
 */
class Http {

    /**
     * Envoie un header HTTP
     * @param string $key Clef à envoyer
     * @param string $value Valeur
     * @param string $replace Ecraser les précédentes valeurs
     */
    public static function header($key, $value, $replace = NULL) {
        if ($replace === NULL) {
            header($key . ': ' . $value);
        } else {
            header($key . ': ' . $value, $replace);
        }
    }

    /**
     * Récupère une variable transmise à la page via les super globales
     * @param string $key Clef à envoyer
     * @param string $mode Liste de valeurs super globales dans laquelle on va chercher si la clef existe
     * @return mixed|false
     */
    public static function request($key, $mode = 'get|post') {
        $split = explode('|', $mode);

        foreach ($split as $gl) {
            $gl = '_' . strtoupper($gl);

            if (isset($GLOBALS[$gl][$key])) {
                return ($GLOBALS[$gl][$key]);
            }
        }
        return (NULL);
    }

    /**
     * Redirige automatiquement la page.
     * @param string $url URL de destination
     * @param integer $time Durée avant la redirection, si inférieur à 0 on ne redirige pas, si vaut 0 on redirige instantanément via un header, sinon on redirige via une balise META refresh.
     */
    public static function redirect($url, $time = 0) {
        if ($time < 0) {
            return;
        } elseif ($time == 0) {
            self::header('location', str_replace('&amp;', '&', $url));
            exit;
        } else {
            $tag = self::add_meta('meta', [
                        'http-equiv' => 'refresh',
                        'content' => $time . ';url=' . $url,
            ]);

            return $tag;
        }
    }

    /**
     * Redirige à partir d'une information précise
     * @param string $redirect Information pour la redirection
     */
    public static function redirect_to($redirect) {
        if ($redirect) {
            // Redirection pages
            if (file_exists(ROOT . 'main/pages/pages_' . $redirect . '.' . PHPEXT)) {
                $url = 'index.' . PHPEXT . '?p=' . urlencode($redirect);
                foreach ($_GET AS $key => $value) {
                    if ($key != 'p' && $key != 'redirect' && $key != 'sid') {
                        $url .= '&' . $key . '=' . urlencode($value);
                    }
                }
            }
            // Redirection locale au site web
            else {
                if (preg_match('#^\s*[a-zA-Z0-9]+://#i', $redirect)) {
                    // URL externe interdites pour des raisons de sécurité
                    Http::redirect('index.php');
                }
                Http::redirect($redirect);
            }
            Http::redirect($url);
        } else {
            Http::redirect(ROOT . 'index.' . PHPEXT);
        }
    }

    /**
     * Ajoute un tag META HTML sur la page.
     * @param string $name Nom de la balise
     * @param array $attr Attributs de la balise META
     */
    public static function add_meta($name, $attr) {
        $meta = '<' . $name . ' ';

        foreach ($attr AS $key => $value) {
            $meta .= $key . '="' . $value . '" ';
        }

        $meta .= ' />';

        return $meta;
    }

    /**
     * Les pages ne doivent pas êtres mises en cache
     */
    public static function no_cache() {
        self::header('Cache-Control', 'post-check=0, pre-check=0', FALSE);
        self::header('Expires', '0');
        self::header('Pragma', 'no-cache');
    }

    /**
     * Lance le téléchargement d'un fichier
     * @param string $filename Nom du fichier
     * @param string $type Type mime du fichier
     */
    public static function download($filename, $type = null, $path) {
        switch ($type) {
            case 'gz':
                $type = 'application/x-gzip';
                break;
            case 'tgz':
                $type = 'application/x-gzip';
                break;
            case 'zip':
                $type = 'application/zip';
                break;
            case 'pdf':
                $type = 'application/pdf';
                break;
            case 'png':
                $type = 'image/png';
                break;
            case 'gif':
                $type = 'image/gif';
                break;
            case 'jpg':
                $type = 'image/jpeg';
                break;
            case 'txt':
            case 'sql':
                $type = 'text/plain';
                break;
            case 'htm':
                $type = 'text/html';
                break;
            case 'html':
                $type = 'text/html';
                break;
            default:
                $type = 'application/octet-stream';
                break;
        }

        self::header('Content-disposition', 'attachment; filename=' . $filename);
        self::header('Content-Type', 'application/' . $type);
        self::header('Content-Transfer-Encoding', $type . "\n");
        self::header('Content-Length', filesize($path . $filename));
        self::header('Pragma', 'no-cache');
        self::header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0, public');
        self::header('Expires', '0');
        readfile($path . $filename);

        exit;
    }

    /**
     * Envoie un cookie au client
     * @param string $name Nom du cookie
     * @param mix $value Valeur du cookie
     * @param int $time Temps d'expiration
     */
    public static function cookie($name, $value, $time) {
        setcookie('cookie_' . $name, $value, $time, '/', '', 0);
    }

    /**
     * Renvoie la valeur d'un cookie du forum
     * @param string $name Nom du cookie
     */
    public static function getcookie($name) {
        $cookie_name = 'cookie_' . $name;
        return (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : NULL);
    }

}
