<?php

/**
 * Permet de créer un backup des fichiers
 *
 * @author INFO5
 * @copyright TFE
 * @version 1.0.0
 * @license GPL v2.0
 *
 * @package TFE
 */
class Backup_file {

    /**
     * Retourne une instance de la classe backup
     */
    public static function factory() {
        $compress = new Compress('cache/file_backup/backup_file_' . date('d_m_y_H_i', CURRENT_TIME) . '.tar.gz');
        self::list_file('..');

        foreach (Globale::$tree as $k => $v) {
            $compress->add_file(str_replace('../', '', $v));
        }
        $compress->write();
        return (TRUE);
    }

    protected function list_file($Folder) {
        $dir = opendir($Folder);

        while (false !== ($Current = readdir($dir))) {
            if ($Current != '.' && $Current != '..') {
                if (is_dir($Folder . '/' . $Current)) {
                    self::list_file($Folder . '/' . $Current);
                } else {
                    $ext = strtolower(substr(strrchr($Current, '.'), 1));
                    if ($ext != 'gz') {
                        Globale::$tree[] = $Folder . '/' . $Current;
                    }
                }
            }
        }
        closedir($dir);
    }

}

/* EOF */