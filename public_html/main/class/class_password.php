<?php

/**
 * Class de gestion des chaines de caractères
 */
class Password {

    // Contient les données graduées du mot de passe
    public $grade_data = [];

    // Pour la génération de mot de passes
    const LOWCASE = 1;
    const UPPCASE = 2;
    const NUMERIC = 4;
    const SPECIAL = 8;
    const ALL = 255;

    /**
      Hash un mot de passe à partir des paramètres passés
      -----
      @param password		::	Mot de passe
      @param algorithm	::	Algorithme utilisé (md5 | sha1)
      @param use_salt		::	Si on concatène un grain au mot de passe
      -----
      retourne un hash du mot de passe
     */
    public static function hash($password, $algorithm, $use_salt) {
        if ($algorithm != 'md5' && $algorithm != 'sha1') {
            $algorithm = 'md5';
        }

        if ($use_salt) {
            $password .= substr(md5(rand(0, CURRENT_TIME) . rand(0, CURRENT_TIME)), 0, 10);
        }

        return ($algorithm($password));
    }

    /**
      Génération d'un mot de passe aléatoire
      -----
      @param length	::	Longueur du mot de passe
      @param type		::	Types de caractères utilisés
      -----
      retourne le mot de passe généré
     */
    public static function generate($length = 8, $type = self::ALL) {
        $chars = '';
        $list = [
            self::LOWCASE => 'abcdefghijklmnopqrstuvwxyz',
            self::UPPCASE => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            self::NUMERIC => '0123456789',
            self::SPECIAL => '&#@{}[]()-+=_!?;:$%*',
        ];

        foreach ($list AS $key => $value) {
            if ($type & $key) {
                $chars .= $value;
            }
        }

        $password = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i <= $length; $i++) {
            $password .= $chars[mt_rand(0, $max)];
        }

        return ($password);
    }

    /**
      Test la robustesse d'un mot de passe
      -----
      @param password_str ::	Mot de passe à graduer
      -----
      renvoie 1, 2, 3 ou 4 en fonction de cette robustesse.
      1 étant un mot de passe faible et 3 / 4 un mot de passe robuste.
     */
    public function grade($password_str) {
        $this->grade_data = ['len' => 1, 'char_type' => 1, 'average' => 0];

        // Première graduation, la longueur du mot de passe
        $len = strlen($password_str);
        $len_step = [6, 8, 11, 15];
        $this->grade_data['len'] = 0;
        foreach ($len_step AS $k => $v) {
            if ($len >= $v) {
                $this->grade_data['len'] = $k + 1;
            }
        }

        // Seconde graduation, on vérifie le type de caractère du mot de passe, lettres, chiffres, caractères spéciaux
        $char_type = ['alpha_min' => 0, 'alpha_maj' => 0, 'number' => 0, 'other' => 0];
        for ($i = 0; $i < $len; $i++) {
            if ($this->is_alpha_min($password_str[$i])) {
                $char_type['alpha_min'] ++;
            } elseif ($this->is_alpha_maj($password_str[$i])) {
                $char_type['alpha_maj'] ++;
            } elseif ($this->is_number($password_str[$i])) {
                $char_type['number'] ++;
            } else {
                $char_type['other'] ++;
            }
        }

        $number_type = 0;
        foreach ($char_type AS $type) {
            if ($type > 0) {
                $number_type++;
            }
        }
        $this->grade_data['char_type'] = $number_type;

        // Troisième graduation, on vérifie le pourcentage de ce type de caractère dans le mot
        $average = ceil($len / 4 / 1.5);
        foreach ($char_type AS $type) {
            if ($type >= $average) {
                $this->grade_data['average'] ++;
            }
        }
        return (round(($this->grade_data['len'] + $this->grade_data['char_type'] + $this->grade_data['average']) / 3));
    }

    /**
      Vérifie si le caractère est une lettre minuscule
      -----
      @param char ::	Caractère
      -----
      retourne vrai ou faux
     */
    private function is_alpha_min($char) {
        if ($char >= 'a' && $char <= 'z') {
            return (TRUE);
        }
        return (FALSE);
    }

    /**
      Vérifie si le caractère est une lettre majuscule
      -----
      @param char ::	Caractère
      -----
      retourne vrai ou faux
     */
    private function is_alpha_maj($char) {
        if ($char >= 'A' && $char <= 'Z') {
            return (TRUE);
        }
        return (FALSE);
    }

    /**
      Vérifie si le caractère est un nombre
      -----
      @param char ::	Caractère
      -----
      retourne vrai ou faux
     */
    private function is_number($char) {
        if ($char >= '0' && $char <= '9') {
            return (TRUE);
        }
        return (FALSE);
    }

}
