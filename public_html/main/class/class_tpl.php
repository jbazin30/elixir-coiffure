<?php

/**
 * Class de templates
 */
define('PREG_LIMIT', 1);
define('ECHO_CODE', 1);
define('GET_CODE', 2);

class Tpl {

    public $ROOT;
    public $cache_dir;
    public $use_cache;
    public $data = [];
    public $last_mask;
    public $stack = [];
    public $curent_stack = 0;
    public $switch = [];

    public function Tpl($ROOT, $cache_dir = '') {
        $this->ROOT = $ROOT;
        if (!empty($cache_dir)) {
            $this->use_cache = TRUE;
            $this->cache_dir = $cache_dir;
        } else {
            $this->use_cache = FALSE;
        }

        $file = '';

        $this->data['parent'] = [
            'file' => '',
            'cache_file' => $this->cache_dir . '/' . $file . '.php',
            'vars' => [],
            'blocks' => []
        ];
        return (TRUE);
    }

    public function set_filenames($array) {
        list($mask, $file) = each($array);
        if (file_exists($this->ROOT . $file)) {
            $this->last_mask = $mask;
            $this->curent_stack++;
            $this->stack[$this->curent_stack] = $mask;
            $this->data[$mask] = [
                'file' => $this->ROOT . $file,
                'cache_file' => $this->cache_dir . '/' . $file . '.php',
                'vars' => [],
                'blocks' => [],
            ];
        } else {
            die("Tpl->load_file() Le fichier $this->ROOT$file n'existe pas.");
        }
        return (TRUE);
    }

    public function assign_vars($vars = [], $mask = 'parent') {
        $this->data[$mask]['vars'] = array_merge($this->data[$mask]['vars'], $vars);
        return (TRUE);
    }

    public function assign_block_vars($block, $vars, $mask = '') {
        if (empty($mask)) {
            $mask = $this->stack[$this->curent_stack];
        }
        $get_blocks = $this->get_blocks($block, $mask);
        eval('$this->data[\'' . $mask . '\'][\'blocks\']' . $get_blocks . '[] = $vars;');
        return (TRUE);
    }

    public function create_block($block) {
        $this->switch[$block] = TRUE;
    }

    public function get_blocks($block, $mask) {
        $blocks_str = '';
        if (preg_match('/\./', $block)) {
            $blocks_array = explode('.', $block);
            $count_blocks = count($blocks_array);
            for ($i = 0; $i < $count_blocks - 1; $i++) {
                $blocks_str .= '[\'' . $blocks_array[$i] . '\']';
                eval('$count = count($this->data[\'' . $mask . '\'][\'blocks\']' . $blocks_str . ');');
                $blocks_str .= '[' . ($count - 1) . ']';
            }
            $blocks_str .= '[\'' . $blocks_array[$count_blocks - 1] . '\']';
        } else {
            $blocks_str = '[\'' . $block . '\']';
        }

        return ($blocks_str);
    }

    public function get_current_blocks($blocks_array, $iteration) {
        $blocks_str = '';
        $count_array = count($blocks_array);
        for ($i = $count_array - 2; $i >= 0; $i--) {
            $blocks_str = '[\'' . $blocks_array[$i] . '\'][$i_' . ($iteration - ($count_array - $i) + 1) . ']' . $blocks_str;
        }
        $blocks_str .= '[\'' . $blocks_array[$count_array - 1] . '\']';
        return ($blocks_str);
    }

    public function get_vars_blocks($blocks) {
        $blocks_array = explode('.', $blocks);
        array_pop($blocks_array);
        $count = count($blocks_array) - 1;
        return ($this->get_current_blocks($blocks_array, $count) . '[$i_' . $count . ']');
    }

    public function parse_code($str, $mask, $type) {
        $assign = ($type == ECHO_CODE) ? 'echo ' : '$_result_tpl .= ';

        $total_inc_tpl = 0;
        $f = 0;
        do {
            preg_match_all('/<!-- INCLUDE_TPL ([a-zA-Z0-9\/._-]+) -->/si', $str, $inc_tpl);
            $count_inc_tpl = count($inc_tpl[1]);
            $total_inc_tpl += $count_inc_tpl;
            for ($v = 0; $v < $count_inc_tpl; $v++) {
                if (file_exists($this->ROOT . $inc_tpl[1][$v])) {
                    $str = str_replace($inc_tpl[0][$v], implode("", file($this->ROOT . $inc_tpl[1][$v])), $str);
                } else {
                    die("Tpl->get_code() Le fichier " . $inc_tpl[1][$v] . " n'existe pas.");
                }
            }
            unset($inc_tpl);
            $f++;
        } while ($f < $total_inc_tpl + 1);

        $str = str_replace('\'', '\\\'', str_replace('\\', '\\\\', $str));

        preg_match_all('/<!-- BEGIN_PHP -->(.*?)<!-- END_PHP -->/si', $str, $php_code);
        $count_php_code = count($php_code);

        for ($v = 0; $v < $count_php_code; $v++) {
            if (isset($php_code[0][$v]) && isset($php_code[1][$v])) {
                $str = str_replace($php_code[0][$v], "';\n" . str_replace('\\\'', '\'', str_replace('\\\\', '\\', $php_code[1][$v])) . "\n$assign '", $str);
            }
        }

        $str = $this->parse_iteration_var($str);

        $str = preg_replace('/\{([A-Z0-9\-_]+)\}/s', '\' . ((isset($this->data[\'' . $mask . '\'][\'vars\'][\'\\1\'])) ? $this->data[\'' . $mask . '\'][\'vars\'][\'\\1\'] : $this->data[\'parent\'][\'vars\'][\'\\1\'])  . \'', $str);

        preg_match_all('/\{(([a-z0-9\-_]+?\.)+?)([A-Z0-9\-_]+?)\}/s', $str, $vars_b);
        $count_vars_b = count($vars_b[1]);
        for ($v = 0; $v < $count_vars_b; $v++) {
            $str = str_replace($vars_b[0][$v], '\' . $this->data[\'' . $mask . '\'][\'blocks\']' . $this->get_vars_blocks($vars_b[1][$v]) . '[\'' . $vars_b[3][$v] . '\'] . \'', $str);
        }

        $str = $this->parse_cond_blocks($str, $assign, $mask);

        $line = explode("\n", $str);
        if ($count_line = count($line)) {
            $block_array = [];
            $b = 0;
            $code = '';
            for ($i = 0; $i < $count_line; $i++) {
                if (preg_match('/<!-- BEGIN ([a-z0-9\-_]+) -->/si', $line[$i], $matches)) {
                    $block_array[$b] = $matches[1];
                    $code .= preg_replace('/<!-- BEGIN ([a-z0-9\-_]+) -->/si', "';\n" . '$count_i_' . $b . ' =  count($this->data[\'' . $mask . '\'][\'blocks\']' . $this->get_current_blocks($block_array, $b) . ')' . ";\n" . 'for($i_' . $b . ' = 0; $i_' . $b . ' < $count_i_' . $b . '; $i_' . $b . '++)' . "\n" . '{' . "\n$assign '", $line[$i], PREG_LIMIT);
                    $b++;
                } elseif (preg_match('/<!-- END ([a-z0-9\-_]+) -->/', $line[$i])) {
                    array_pop($block_array);
                    $code .= preg_replace('/<!-- END ([a-z0-9\-_]+ )?-->/si', "';\n}\n$assign '", $line[$i], PREG_LIMIT);
                    $b--;
                } else {
                    $code .= $line[$i] . "\n";
                }
                unset($matches);
            }
        } else {
            die("Tpl->get_code() Le fichier $this->data[$mask]['file'] est vide.");
        }
        return ($assign . '\'' . $code . '\';');
    }

    public function parse_cond_blocks($str, $assign, $mask) {
        $str = preg_replace('/\[([A-Z0-9\-_]+)\]/s', '(isset($this->data[\'' . $mask . '\'][\'vars\'][\'\\1\']) ? $this->data[\'' . $mask . '\'][\'vars\'][\'\\1\'] : $this->data[\'parent\'][\'vars\'][\'\\1\'])', $str);
        preg_match_all('/\[(([a-z0-9\-_]+?\.)+?)([A-Z0-9\-_]+?)\]/s', $str, $vars_b);
        $count_vars_b = count($vars_b[1]);
        for ($v = 0; $v < $count_vars_b; $v++) {
            $str = str_replace($vars_b[0][$v], '$this->data[\'' . $mask . '\'][\'blocks\']' . $this->get_vars_blocks($vars_b[1][$v]) . '[\'' . $vars_b[3][$v] . '\']', $str);
        }

        preg_match_all('/<!-- (ELSE)?IF (.+?) -->/si', $str, $if);
        $count_if = count($if);

        for ($v = 0; $v < $count_if; $v++) {
            if (isset($if[0][$v]) && isset($if[1][$v]) && isset($if[2][$v])) {
                $str = str_replace($if[0][$v], "';\n" . (($if[1][$v] == 'ELSE') ? "}\nelse " : '') . "if (" . $if[2][$v] . ")\n{\n$assign '", $str);
            }
        }

        $str = preg_replace('/<!-- IFEXIST ([a-zA-Z0-9\-_]+?) -->/si', "';\n" . 'if ( isset ($this->switch[\'\\1\']))' . "\n{\n$assign '", $str);

        $str = str_replace('<!-- ELSE -->', "';\n}\nelse\n{\n$assign '", $str);
        $str = str_replace('<!-- ENDIF -->', "';\n}\n$assign '", $str);

        return ($str);
    }

    public function parse_iteration_var($str) {
        preg_match_all('/(([a-z0-9\-_]+?\.)+?)ITERATION/si', $str, $it);
        $count = count($it[0]);
        for ($i = 0; $i < $count; $i++) {
            $count_elmt = substr_count($it[1][$i], '.');
            $str = str_replace('{' . $it[0][$i] . '}', '\' . $i_' . ($count_elmt - 1) . ' . \'', $str);
            $str = str_replace('[' . $it[0][$i] . ']', '$i_' . ($count_elmt - 1), $str);
        }

        preg_match_all('/(([a-z0-9\-_]+?\.)+?)COUNT_ITERATION/si', $str, $it);
        $count = count($it[0]);
        for ($i = 0; $i < $count; $i++) {
            $count_elmt = substr_count($it[1][$i], '.');
            $str = str_replace('{' . $it[0][$i] . '}', '\' . $count_i_' . ($count_elmt - 1) . ' . \'', $str);
            $str = str_replace('[' . $it[0][$i] . ']', '$count_i_' . ($count_elmt - 1), $str);
        }
        return ($str);
    }

    public function pparse($mask, $type = ECHO_CODE, $interfer = FALSE) {
        $this->curent_stack--;
        array_pop($this->stack);

        unset($_result_tpl);
        $_result_tpl = '';
        if (!$interfer && $this->use_cache && file_exists($this->data[$mask]['cache_file']) && filemtime($this->data[$mask]['file']) == filemtime($this->data[$mask]['cache_file'])) {
            include($this->data[$mask]['cache_file']);
        } elseif (file_exists($this->data[$mask]['file'])) {
            $str = implode("", file($this->data[$mask]['file']));
            $code = $this->parse_code($str, $mask, GET_CODE);

            if (!$interfer && $this->use_cache) {
                $this->write_cache_tpl($this->data[$mask]['cache_file'], "<?php\n$code\n?>", filemtime($this->data[$mask]['file']));
            }
            eval($code);
            unset($code);
        } else {
            die("Tpl->load_file() Le fichier $this->data[$mask]['file'] n'existe pas.");
        }

        if ($type == ECHO_CODE) {
            echo $_result_tpl;
        }
        return ($_result_tpl);
    }

    public function write_cache_tpl($file, $code, $time) {
        $fd = @fopen($file, 'w+');
        @flock($fd, LOCK_EX);
        $result = @fwrite($fd, $code);
        @flock($fd, LOCK_UN);
        @fclose($fd);
        @touch($file, $time);
        @umask(0);
        @chmod($file, 0666);
        return ($result);
    }

}
