<?php

/**
 * Classe permettant de réaliser des backups de diverses données (mysql, pgsql, etc ...).
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
abstract class Backup {

    // Multi insertion ?
    public $multi_insert = FALSE;
    // Méthode pour le dump
    protected $dump_method;

    const OUTPUT = 1;
    const DOWNLOAD = 2;
    const FTP = 3;
    const GET = 4;
    const STRUCT = 1;
    const DATA = 2;
    const ALL = 255;

    abstract public function open($filename);

    abstract public function write($str);

    abstract public function close();

    /**
     * Retourne une instance de la classe Backup, étendue par la classe gérant le buffer de sortie, et contenant
     * une propriété pointant sur la méthode avec laquelle on souhaite faire un backup.
     */
    public static function factory($sgbd, $output) {
        // Gestion du buffer
        switch ($output) {
            case self::OUTPUT :
                $obj = new Backup_print();
                break;

            case self::DOWNLOAD :
                $obj = new Backup_download();
                break;

            case self::FTP :
                $obj = new Backup_ftp();
                break;

            case self::GET :
            default :
                $obj = new Backup_get();
                break;
        }

        if (!method_exists($obj, 'dump_' . $sgbd)) {
            trigger_error('Base de donnée incorecte dans la classe Backup() : ' . $sgbd, C_ERROR);
        }
        $obj->dump_method = $sgbd;

        return ($obj);
    }

    /**
     * Lance le backup
     */
    public function save($type, $tables) {
        $filename = $this->generate_filename();

        $this->open($filename);
        $this->create_header();
        $this->{'dump_' . $this->dump_method}($type, $tables);
        return ($this->close());
    }

    /**
     * Créé un header pour les backups
     * -----
     * $data_type ::	Type de données à sauver (mysql, etc ..)
     */
    private function create_header() {
        $header = sprintf("#\n# TFE version %s :: `%s` dump\n# Créé le %s\n#\n\n", '1.0.0', $this->dump_method, date("d/m/y H:i", CURRENT_TIME));

        return ($header);
    }

    /**
     * Génère un nom pour le backup
     * -----
     * $data_type ::	Type de données à sauver (mysql, etc ..)
     */
    private function generate_filename() {
        return ('backup_' . $this->dump_method . '_' . date('d_m_y_H_i', CURRENT_TIME) . '.sql');
    }

    /**
     * Effectue un dump des tables MySQL du forum
     * -----
     * $type ::			Le type de données qu'on veut sauvegarder (structure, contenu ou les deux)
     * $save_table ::	Les tables a sauvegarder
     * $comment ::		Ajoute un commentaire en début de table
     */
    public function dump_mysql($type, $save_table, $comment = TRUE) {
        if (is_array($save_table)) {
            $sql = "SHOW TABLES";
            $result = Globale::$db->requete($sql);
            $content = '';
            while ($table = Globale::$db->tableau($result, 'row')) {
                if (in_array($table[0], $save_table)) {
                    $struct = '';
                    $data = '';
                    if ($type & self::STRUCT) {
                        if ($comment) {
                            $this->write("#\n# Structure de la table MySQL `${table[0]}`\n#\n");
                        }

                        $sql = 'SHOW CREATE TABLE `' . $table[0] . '`';
                        $create_result = Globale::$db->requete($sql);
                        while ($create = Globale::$db->tableau($create_result, 'row')) {
                            $this->write($create[1] . ";\n");
                        }
                        $this->write("\n");
                    }

                    if ($type & self::DATA) {
                        $this->dump_database($table[0], "MySQL", $comment, $this->multi_insert);
                    }
                }
            }
        } else {
            trigger_error('La variable $save_table doit être un tableau dans la classe backup() : ' . $save_table, C_ERROR);
        }
        return ($content);
    }

    /**
     *  Créé les requètes d'insertion, commun pour chaque des bases de donnée.
     * -----
     * $tablename ::	Nom de la table
     * $dbms_name ::	Nom de la SGBD
     * $comment ::		Ajoute un commentaire en début de table
     * $multi_insert ::	Gérer les requètes sous forme de multi insertion
     * $exept ::		Contient la liste des champs à ne pas prendre en compte
     */
    public function dump_database($tablename, $sgbd_name, $comment = TRUE, $multi_insert = FALSE, $exept = []) {
        // Si la SGBD ne supporte pas les multi insertions on force le paramètre à FALSE
        if (!Globale::$db->can_use_multi_insert) {
            $multi_insert = FALSE;
        }

        $get_fields = FALSE;
        $fields_type = [];
        $content = '';
        if ($comment) {
            $this->write("\n#\n# Contenu de la table $sgbd_name `$tablename`\n#\n");
        }

        // Données de la table
        $sql = "SELECT *
				FROM `$tablename`";
        $result = Globale::$db->requete($sql);
        $multi_values = '';
        $k = 0;
        while ($tableau = Globale::$db->tableau($result, 'assoc')) {
            $values = '';
            foreach ($tableau AS $field => $value) {
                // Si on ne prend pas en compte le champ
                if ($exept && in_array($field, $exept)) {
                    continue;
                }

                if (!$get_fields) {
                    // On récupère les champs si cela n'a pas déjà été fait
                    $fields_type[$field] = Globale::$db->get_field_type($result, $field, $tablename);
                }

                // On récupère les valeurs de la ligne courante
                $values .= (($values) ? ', ' : '') . (($fields_type[$field] == 'string') ? '\'' . Globale::$db->escape($value) . '\'' : $value);
            }

            if (!$get_fields) {
                $fields = implode(', ', array_keys($fields_type));
            }

            if (!$multi_insert || !$get_fields) {
                $this->write("INSERT INTO `$tablename` ($fields) VALUES ");
            }

            if ($multi_insert && $get_fields) {
                $this->write(",\n");
            }

            $this->write("($values)");

            if (!$multi_insert) {
                $this->write(";\n");
            }

            $get_fields = TRUE;
        }

        if ($multi_insert) {
            $this->write(";\n");
        }
    }

}

/* EOF */