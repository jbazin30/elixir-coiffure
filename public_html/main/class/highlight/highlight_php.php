<?php

/**
 * Coloration syntaxique du PHP
 */
class Highlight_php extends Highlight {

    private static $php_special_functions = [];
    public static $php_internal_functions = [];
    private static $php_special_vars = [];
    private static $lib_php = [];
    private static $init = FALSE;

    public function __construct() {
        if (self::$init) {
            return;
        }
        self::$init = TRUE;

        // Fichier de configuration
        $file_content = file_get_contents(ROOT . 'main/class/highlight/keywords/highlight_php.txt');
        self::$php_special_functions = $this->get_conf($file_content, 'FUNCTIONS');
        self::$php_special_vars = $this->get_conf($file_content, 'VARS');

        // built-in php
        $defined_functions = get_defined_functions();
        self::$php_internal_functions = $defined_functions['internal'];
        unset($defined_functions);

        // On charge la librairie des fonctions PHP
        if (file_exists(ROOT . 'main/class/highlight/keywords/lib_php_prototype.txt')) {
            $lib_php = (array) @explode("\n", gzuncompress(file_get_contents(ROOT . 'main/class/highlight/keywords/lib_php_prototype.txt')));
            self::$lib_php = [];
            foreach ($lib_php AS $line) {
                $split = explode("\t", $line);
                if (count($split) == 2) {
                    list($name, $prototype) = $split;
                    self::$lib_php[trim($name)] = trim($prototype);
                }
            }
        }
    }

    /**
     * Parse une chaîne de caractère PHP
     */
    protected function _parse($str) {
        $len = strlen($str);

        $result = '';
        $word_open = FALSE;
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];

            if (($c == '\'' || $c == '"' || $c == '`') && !Fonction::is_escaped($i, $str)) {
                // Gestion des quotes ' " et `
                $result .= $this->_quote_string($str, $i, $len, $c, 'sc_php_text');
            } elseif ($c == '/') {
                // Gestion des commentaires
                if ($str[$i + 1] == '/') {
                    $result .= $this->open_style('sc_php_comment');
                    while ($i < $len && $str[$i] != "\n" && $str[$i] != "\0") {
                        $result .= $this->escape_special_char($str[$i]);
                        $i++;
                    }
                    $result .= $this->close_style();
                    $i--;
                } elseif ($str[$i + 1] == '*') {
                    $result .= $this->open_style('sc_php_comment');
                    while ($i < $len) {
                        $result .= $this->escape_special_char($str[$i]);
                        if ($str[$i] == "*" && $str[$i + 1] == '/') {
                            $result .= '/';
                            $i++;
                            break;
                        }
                        $i++;
                    }
                    $result .= $this->close_style();
                } else {
                    $result .= $this->escape_special_char($c);
                }
            } elseif ($c == '$' && !Fonction::is_escaped($i, $str)) {
                // Gestion des variables
                $aco_open = FALSE;
                $i++;
                $tmp = '';
                while ($i < $len) {
                    $c = $str[$i];
                    if ($c == '{' && !$aco_open) {
                        $aco_open = TRUE;
                    } elseif (($c == '}' && $aco_open) || (!$aco_open && !preg_match('#[a-zA-Z0-9_]#i', $c))) {
                        $begin = (in_array($tmp, self::$php_special_vars)) ? $this->open_style('sc_php_special_var') : $this->open_style('sc_php_var');
                        $result .= $begin . '$' . $tmp;
                        if ($aco_open) {
                            $result .= '}';
                            $i++;
                        }
                        $result .= $this->close_style();
                        $aco_open = FALSE;
                        break;
                    }
                    $tmp .= $this->escape_special_char($c);
                    $i++;
                }
                $i--;
            }
            // Fonctions ?
            elseif (preg_match('#[a-zA-Z0-9_]#i', $c)) {
                $tmp = '';
                while ($i < $len) {
                    $c = $str[$i];
                    if (!preg_match('#[a-zA-Z0-9_]#i', $c)) {
                        if (in_array(strtolower($tmp), self::$php_special_functions)) {
                            $result .= $this->open_style('sc_php_keyword') . $tmp . $this->close_style();
                        } elseif (in_array(strtolower($tmp), self::$php_internal_functions)) {
                            $helper = (isset(self::$lib_php[$tmp])) ? self::$lib_php[$tmp] : '';
                            $result .= '<a href="http://www.php.net/manual/function.' . str_replace('_', '-', $tmp) . '.php" class="sc_php_function" target="_blank" title="' . $helper . '">' . $tmp . '</a>';
                        } else {
                            $result .= $this->open_style('sc_php_normal') . $tmp . $this->close_style();
                        }
                        break;
                    }
                    $tmp .= $this->escape_special_char($c);
                    $i++;
                }
                $i--;
            }
            // Autre
            else {
                $result .= $this->escape_special_char($c);
            }
        }

        return ($result);
    }

}
