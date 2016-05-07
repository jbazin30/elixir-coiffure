<?php

/**
 * Class de manipulation XML
 */
class Xml {

    private $content;
    public $document;
    private $stack = [];
    public $titi = 'Salut';

    public function __construct() {
        $this->document = new Xml_element();
    }

    public function load_file($filename, $use_cache = TRUE) {
        if (!file_exists($filename)) {
            trigger_error('Le fichier XML ' . $filename . ' n\'existe pas', C_ERROR);
        }

        if ($use_cache) {
            $hash = md5($filename);
            $cache = Cache::factory('xml');
            if ($cache->exists($hash) && $cache->get_time($hash) == filemtime($filename)) {
                $this->document = $cache->get($hash);
                return;
            }
        }

        $this->content = file_get_contents($filename);
        $this->parse();

        if ($use_cache) {
            $cache->put($hash, $this->document, $filename, filemtime($filename));
        }
    }

    public function load_content($content) {
        $this->document = new Xml_element();
        $this->content = $content;
        $this->parse();
    }

    public function parse() {
        $xml = new Xml_regexp_parser();
        $xml->obj = & $this;
        $xml->open_handler = 'open_tag';
        $xml->value_handler = 'value_tag';
        $xml->close_handler = 'close_tag';
        $result = $xml->parse($this->content);
        if (!$result) {
            trigger_error($xml->errstr, C_ERROR);
        }
    }

    public function open_tag($tag, $attr) {
        $ref = &$this->document;
        foreach ($this->stack AS $i => $item) {
            if ($i > 0) {
                $ref = &$ref->$item;
                $ref = &$ref[count($ref) - 1];
            }
        }

        if (count($this->stack)) {
            $new = $ref->createElement($tag);
            foreach ($attr AS $k => $v) {
                $new->setAttribute($k, $v);
            }
            $new->__data['depth'] = count($this->stack);
            $ref->appendChild($new);
        } else {
            $ref->setTagName($tag);
        }

        array_push($this->stack, $tag);
    }

    public function close_tag($tag) {
        array_pop($this->stack);
    }

    public function value_tag($text) {
        $ref = &$this->document;
        foreach ($this->stack AS $i => $item) {
            if ($i > 0) {
                $ref = &$ref->$item;
                $ref = &$ref[count($ref) - 1];
            }
        }

        $ref->setData($text);
    }

}

class Xml_element {

    public $__data = [];

    public function __construct() {
        $this->__data['name'] = 'newElement';
        $this->__data['value'] = NULL;
        $this->__data['attr'] = [];
        $this->__data['depth'] = 0;
    }

    public function createElement($name = 'newElement') {
        $new = new Xml_element();
        $new->setTagName($name);
        $new->__data['depth'] = $this->__data['depth'] + 1;

        return ($new);
    }

    public function attribute() {
        return ($this->__data['attr']);
    }

    public function setAttribute($name, $value) {
        $this->__data['attr'][$name] = $value;
    }

    public function attributeExists($name) {
        return ((isset($this->__data['attr'][$name])) ? TRUE : FALSE);
    }

    public function getAttribute($name) {
        return (($this->attributeExists($name)) ? $this->__data['attr'][$name] : NULL);
    }

    public function deleteAttribute($name) {
        unset($this->__data['attr'][$name]);
    }

    public function setTagName($name) {
        $this->__data['name'] = $name;
    }

    public function getTagName() {
        return ($this->__data['name']);
    }

    public function setData($value, $htmlspecialchars = TRUE) {
        if ($htmlspecialchars) {
            $value = htmlspecialchars($value);
        }
        $this->__data['value'] = $value;
    }

    public function getData() {
        return (String::unhtmlspecialchars($this->__data['value']));
    }

    public function appendChild($node, $pos = 0) {
        $name = $node->getTagName();
        if (!isset($this->$name)) {
            $this->$name = [];
        }
        $ref = &$this->$name;

        if ($pos == 0) {
            $ref[] = $node;
        } else {
            $ref = array_merge(array_slice($ref, 0, $pos), [$node], array_slice($ref, $pos));
        }
    }

    public function AppendXmlChild($string, $pos = 0) {
        $xml = new Xml;
        $xml->load_content($string);
        $this->appendChild($xml->document, $pos);
    }

    public function children() {
        $children = [];
        foreach ($this AS $property_name => $property_value) {
            if ($property_name != '__data') {
                $children[] = &$this->$property_name;
            }
        }

        return ($children);
    }

    public function listChildren() {
        $children = [];
        foreach ($this AS $property_name => $property_value) {
            if ($property_name != '__data') {
                foreach ($this->$property_name AS $child) {
                    $children[] = &$child;
                }
            }
        }

        return ($children);
    }

    public function childExists($name) {
        return ((isset($this->$name)) ? TRUE : FALSE);
    }

    public function hasChildren() {
        return ((count($this->children())) ? TRUE : FALSE);
    }

    public function deleteChildren($name) {
        if ($this->childExists($name)) {
            unset($this->$name);
        }
    }

    public function deleteChild($name, $pos = 0) {
        if (!$this->childExists($name)) {
            return (NULL);
        }

        $new = [];
        foreach ($this->$name AS $index => $child) {
            if ($index != $pos) {
                $new[] = $child;
            }
        }
        $this->$name = $new;
    }

    public function moveChild($name, $pos, $move = 0) {
        if ($move == 0 || !$this->childExists($name)) {
            return;
        }

        $ref = &$this->$name;
        if (isset($ref[$pos + $move])) {
            $tmp = $ref[$pos];
            $ref[$pos] = $ref[$pos + $move];
            $ref[$pos + $move] = $tmp;
        }
    }

    public function &lastChild($name) {
        if (!$this->childExists($name)) {
            return (NULL);
        }
        $ref = &$this->$name;
        $ref = &$ref[count($ref) - 1];
        return ($ref);
    }

    public function &getElementByPath($path) {
        $split = explode('/', trim($path, '/'));
        $ref = &$this;
        foreach ($split AS $item) {
            if (!$ref->childExists($item)) {
                $null = NULL;
                $ref = &$null;
                return ($ref);
            }
            $ref = &$ref->$item;
            $ref = & $ref[0];
        }

        return ($ref);
    }

    public function asXML() {
        $xml = str_repeat("\t", $this->__data['depth']) . '<' . $this->getTagName();
        foreach ($this->attribute() AS $key => $value) {
            $xml .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        $xml .= '>';

        if ($this->hasChildren()) {
            foreach ($this->children() AS $childs) {
                foreach ($childs AS $child) {
                    $xml .= "\n" . $child->asXML();
                }
            }
            $xml .= "\n" . str_repeat("\t", $this->__data['depth']) . '</' . $this->getTagName() . '>';
        } else {
            $xml .= '<![CDATA[' . $this->getData() . ']]></' . $this->getTagName() . '>';
        }

        return ($xml);
    }

    public function asValidXML($charset = 'UTF-8') {
        $xml = '<?xml version="1.0" encoding="' . $charset . '" standalone="no"?>' . "\n";
        $xml .= $this->asXml();
        return ($xml);
    }

}

class Xml_regexp_parser {

    public $obj = NULL;
    public $open_handler = NULL;
    public $close_handler = NULL;
    public $value_handler = NULL;
    public $errstr = NULL;

    public function parse($str) {
        if (preg_match('#^\s*<\?xml.*?\?>#si', $str, $m)) {
            $str = preg_replace('#^\s*<\?xml.*?\?>#si', '', $str);
        }

        $stack = [];
        $in_cdata = FALSE;
        $last_offset = 0;
        $value = '';

        preg_match_all('#<(/)?\s*([a-zA-Z0-9_\-]*?)(\s+(.*?))?\s*(/?)>\s*(<\!\[CDATA\[)?#si', $str, $m, PREG_OFFSET_CAPTURE);
        $count = count($m[0]);
        for ($i = 0; $i < $count; $i++) {
            $length = strlen($m[0][$i][0]);
            $current_offset = $m[0][$i][1] + $length;

            $tag = $m[2][$i][0];

            if (!$m[1][$i][0] || $m[5][$i][0]) {
                if ($in_cdata) {
                    continue;
                }

                array_push($stack, $tag);

                if ($this->open_handler) {
                    preg_match_all('#([a-zA-Z0-9_\-]*?)="(.*?)"#si', $m[3][$i][0], $a, PREG_SET_ORDER);
                    $attrs = [];
                    foreach ($a AS $attr) {
                        $attrs[$attr[1]] = $attr[2];
                    }

                    if ($this->obj) {
                        $this->obj->{$this->open_handler}($tag, $attrs);
                    } else {
                        call_user_func($this->open_handler, $tag, $attrs);
                    }
                }

                if ($m[6][$i] && $m[6][$i][0]) {
                    $in_cdata = TRUE;
                }
            }

            if ($m[1][$i][0] || $m[5][$i][0]) {
                $value = substr($str, $last_offset, $m[0][$i][1] - $last_offset);

                if (substr($value, -2) == ']>') {
                    $value = substr($value, 0, -3);
                    $in_cdata = FALSE;
                }

                if ($in_cdata) {
                    continue;
                }

                $check = array_pop($stack);
                if ($check != $tag) {
                    $this->errstr = 'XML error : tag &lt;' . $check . '&gt; is different of &lt;' . $tag . '&gt;';
                    return (FALSE);
                }

                if ($this->obj && $this->value_handler) {
                    $this->obj->{$this->value_handler}($value);
                } elseif ($this->value_handler) {
                    call_user_func($this->value_handler, $value);
                }

                if ($this->obj && $this->close_handler) {
                    $this->obj->{$this->close_handler}($tag);
                } elseif ($this->close_handler) {
                    call_user_func($this->close_handler, $tag);
                }
            }

            $last_offset = $current_offset;
        }
        return (TRUE);
    }

}
