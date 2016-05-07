<?php

/**
 * Class de gestion des langues
 */
class Lang {

    public $lg = [];

    /**
     * Charge un fichier de langue
     * @param string lg_lang Nom du fichier de langue
     * return array
     */
    public function __construct($lg_name) {
        if (file_exists(ROOT . DIR_LANG . $lg_name . '/' . $lg_name . '.' . PHPEXT)) {
            $this->lg += include(ROOT . DIR_LANG . $lg_name . '/' . $lg_name . '.' . PHPEXT);

            return $this->lg;
        }
    }

    /**
     * Retourne la valeur d'une clef de langue
     * 	@param string key Clé de langue
     * return string
     */
    public function lang($key) {
        return ((isset($this->lg[$key])) ? $this->lg[$key] : NULL);
    }

}
