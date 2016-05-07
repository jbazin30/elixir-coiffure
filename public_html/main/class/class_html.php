<?php

/**
 * Ensemble de méthode retournant du code HTML généré
 */
class Html {

    /**
     * Créé un champ caché
     * @param string $name Nom du champ caché
     * @param string $valeur Valeur du champ caché
     */
    public static function hidden($name, $value = '') {
        if (is_array($name)) {
            $return = '';
            foreach ($name AS $k => $v) {
                $return .= '<input type="hidden" name="' . $k . '" value="' . $v . '" />';
            }
            return ($return);
        } else {
            return ('<input type="hidden" name="' . $name . '" value="' . $value . '" />');
        }
    }

    /**
     * Gestion d'une pagination
     * @param integer $cur Valeur de la page courante
     * @param integer $total Nombre de page
     * @param string $url URL de redirection de la pagination, sans sid()
     * @param string $page_info Ajoute des informations à la pagination : page suivante, précédente, première page, etc ...
     */
    public static function pagination($cur, $total, $url, $page_info = PAGINATION_ALL, $simple_style = FALSE, $suffixe = '') {
        $style_pagination = [
            'url_global' => '<div class="pagination_block">%1$s</div>',
            'url' => '<a href="%s">%s</a>',
            'url_cur' => '<span class="pagination_block_cur">%s</span>',
            'url_separator' => ' ',
            'topic_global' => '<span class="small">(%s)</span>',
            'topic' => '<a href="%s">%s</a>',
            'topic_cur' => '<span style="font-weight: bold">%s</span>',
            'topic_separator' => ','
        ];

        // Style de la pagination
        $default_style = ($simple_style) ? 'topic' : 'url';
        $s = $style_pagination[$default_style . '_separator'];

        // Initialisation des variables
        $total = ceil($total);
        $str = '';
        $begin = ($cur < 3) ? 1 : $cur - 2;
        $end = ($cur > ($total - 2)) ? $total : $cur + 2;

        // Création de la pagination
        if ($cur) {
            for ($i = $begin; $i <= $end; $i++) {
                $str .= ( ($i == $begin) ? '' : $s) . (($i == $cur) ? sprintf($style_pagination[$default_style . '_cur'], $i) : sprintf($style_pagination[$default_style], $url . '-' . $i . $suffixe . '.html', $i));
            }
        } else {
            $str .= 'Page : ';
            if ($total <= 4) {
                for ($i = 1; $i <= $total; $i++) {
                    $str .= ( ($i == 1) ? '' : $s) . sprintf($style_pagination[$default_style], $url . $i, $i);
                }
            } else {
                $str .= sprintf($style_pagination[$default_style], $url . '-' . 1 . $suffixe . '.html', 1);
                $str .= $s . sprintf($style_pagination[$default_style], $url . '-' . 2 . $suffixe . '.html', 2);
                $str .= $s . ' ...';
                $str .= $s . sprintf($style_pagination[$default_style], $url . '-' . ($total - 1) . $suffixe . '.html', ($total - 1));
                $str .= $s . sprintf($style_pagination[$default_style], $url . '-' . $total . $suffixe . '.html', $total);
            }
        }

        // Liens suivants, précédents, première page et dernière page
        if ($page_info & PAGINATION_PREV) {
            $str = (($cur > 1) ? sprintf($style_pagination[$default_style], $url . '-' . ($cur - 1) . $suffixe . '.html', '&#171;') : sprintf($style_pagination[$default_style . '_cur'], '&#171;')) . $s . $str;
        }

        if ($page_info & PAGINATION_FIRST) {
            $str = sprintf($style_pagination[$default_style], $url . '-' . 1 . $suffixe . '.html', Header::$lang->lang('t_list_premiere')) . $s . $str;
        }

        if ($page_info & PAGINATION_NEXT) {
            $str .= $s . (($cur < $total) ? sprintf($style_pagination[$default_style], $url . '-' . ($cur + 1) . $suffixe . '.html', '&#187;') : sprintf($style_pagination[$default_style . '_cur'], '&#187;'));
        }

        if ($page_info & PAGINATION_LAST) {
            $last_str = ($simple_style) ? Header::$lang->lang('t_list_premiere') : sprintf(Header::$lang->lang('t_list_d_sprintf'), $total);
            $str .= $s . sprintf($style_pagination[$default_style], $url . '-' . $total . $suffixe . '.html', $last_str);
        }

        return (sprintf($style_pagination[$default_style . '_global'], $str, $url));
    }

    /**
     * La jumpbox est une liste déroulante permettant d'accéder rapidement à n'importe quelle rubrique.
     * @param redirect ::	Si on est en mode redirection
     */
    public static function jumpbox($redirect = FALSE) {
        if ($redirect) {
            $value = Http::request('jumpbox', 'post');

            Http::redirect($value);
        } else {
            return (Html::list_catalogue('jumpbox'));
        }
    }

    /**
     * Créé une liste HTML
     * @param name			::	Nom de la liste
     * @param value		::	Valeur par défaut de la liste
     * @param ary			::	Tableau contenant en clef les valeurs des options et
     * 							en valeur les éléments.
     * @param multilist	::	S'il s'agit d'une multiliste on précise le code ici, $value devra
     * 							être un tableau
     * @param code			::	Code HTML (ou javascript) à rajouter à la liste
     */
    public static function create_list($name, $value, $ary, $multilist = '', $code = '') {
        $list = '<select name="' . $name . ((!$multilist) ? '' : '[]') . '" id="' . $name . '" ' . $multilist . ' ' . $code . '>';
        foreach ($ary AS $k => $v) {
            $list .= '<option value="' . $k . '" ' . (((!$multilist && $k == $value) || ($multilist && is_array($value) && in_array($k, $value))) ? 'selected="selected"' : '') . '>' . $v . '</option>';
        }
        $list .= '</select>';
        return ($list);
    }

    /**
     * Créé une liste HTML en fonction des éléments dans un dossier
     * @param name			::	Nom de la liste
     * @param value		::	Valeur par défaut de la liste
     * @param dir			::	Chemin du répertoire à lister
     * @param allowed_ext	::	Contient les extensions autorisées.
     * 							Laisser vide pour autoriser tous les fichiers.
     * @param only_dir		::	Autorise uniquement les dossiers si TRUE
     * @param first		::	Rajouter un élément en début de liste
     * @param code			::	Pour rajouter des attributs ou du code javascript dans le <select>
     */
    public static function list_dir($name, $value, $dir, $allowed_ext = [], $only_dir = FALSE, $first = '', $code = '') {
        $count = count($allowed_ext);
        if (!$fd = @opendir($dir)) {
            trigger_error(Header::$lang->lang('t_not_open_dir') . $dir, C_ERROR);
        }

        $list = '<select name="' . $name . '" ' . $code . '>' . $first;
        while ($file = readdir($fd)) {
            $ary = explode('.', $file);
            $ext = $ary[count($ary) - 1];
            if ($file[0] != '.' && (!$count || ($count && in_array($ext, $allowed_ext)))) {
                if (!$only_dir || ($only_dir && is_dir($dir . '/' . $file))) {
                    $list .= '<option value="' . $file . '" ' . (($file == $value) ? 'selected="selected" style="font-weight: bold;"' : '') . '>' . str_replace('_', ' ', $file) . '</option>';
                }
            }
        }
        closedir($fd);
        $list .= '</select>';
        return ($list);
    }

    /*
     * Crée une liste de checkbox html pour des filtres
     */

    public static function make_checkbox_list($array) {
        $output = '';
        foreach ($array as $key => $value) {
            $output .= '<input type="checkbox" value="' . $key . '" />&nbsp;' . Fonction::ucfirstLetter($value) . '<br />';
        }
        return $output;
    }

}
