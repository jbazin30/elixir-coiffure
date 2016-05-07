<?php

/**
 * Class de manipulation de la librairie GD
 */
class Image {

    // Si l'extension GD est chargee
    public $loaded = TRUE;
    public $dir_normal_size;
    public $dir_mini_size;
    public $ratio;
    public $src_image;
    public $tableau_image;
    public $mini_image;
    public $img_temp;
    public $tableau_miniature = [];
    public $open_dir;
    public $file_dir;
    public $nbcol = 2;
    public $nbpics;
    public $buffer;

    /**
     * Constructeur : vérifie si l'extension GD est chargée
     */
    public function __construct($src = null, $dest = null, $rate = 100, $image = null) {
        $this->src_image = $image;
        $this->dir_normal_size = $src;
        $this->dir_mini_size = $dest;
        $this->ratio = $rate;
        $this->tableau_image = @getimagesize($this->dir_normal_size . $this->src_image);
        $this->loaded = (PHP_EXTENSION_GD) ? TRUE : FALSE;
    }

    public function getSize() {
        return $this->tableau_image;
    }

    /**
     * Créé une nouvelle image
     * return binary
     */
    public function createImage($str) {
        $image = imagecreate(200, 50);
        $orange = imagecolorallocate($image, 255, 128, 0);
        $bleu = imagecolorallocate($image, 0, 0, 255);
        $bleuclair = imagecolorallocate($image, 156, 227, 254);
        $noir = imagecolorallocate($image, 0, 0, 0);
        $blanc = imagecolorallocate($image, 255, 255, 255);
        $font = ROOT . 'main/class/captcha/fonts/PRINC___.TTF';

        imagettftext($image, 20, 0, 10, 20, $noir, $font, $str);

        imagepng($image);
    }

    /**
     * Vérifie si il s'agit bien d'une image et supprime le fichier si ce n'est pas le cas
     * return bool
     */
    public function verifImage() {
        if ($this->tableau_image == false) {
            unlink($this->dir_normal_size . $this->src_image);
            return false;
        } elseif ($this->tableau_image[2] == 2 || $this->tableau_image[2] == 3) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Créé des miniatures
     */
    public function create_miniature() {
        if ($this->tableau_image[2] == 2) {
            $this->mini_image = imagecreatefromjpeg($this->dir_normal_size . $this->src_image);

            if ($this->tableau_image[0] > $this->tableau_image[1]) {
                $this->img_temp = imagecreatetruecolor(round(($this->ratio / $this->tableau_image[1]) * $this->tableau_image[0]), $this->ratio);
                imagecopyresampled($this->img_temp, $this->mini_image, 0, 0, 0, 0, round(($this->ratio / $this->tableau_image[1]) * $this->tableau_image[0]), $this->ratio, $this->tableau_image[0], $this->tableau_image[1]);
            } else {
                $this->img_temp = imagecreatetruecolor($this->ratio, round(($this->ratio / $this->tableau_image[0]) * $this->tableau_image[1]), $this->ratio);
                imagecopyresampled($this->img_temp, $this->mini_image, 0, 0, 0, 0, $this->ratio, round(($this->ratio / $this->tableau_image[0]) * $this->tableau_image[1]), $this->tableau_image[0], $this->tableau_image[1]);
            }

            imagejpeg($this->img_temp, $this->dir_mini_size . $this->src_image);
        } elseif ($this->tableau_image[2] == 3) {
            $this->mini_image = imagecreatefrompng($this->dir_normal_size . $this->src_image);

            if ($this->tableau_image[0] > $this->tableau_image[1]) {
                $this->img_temp = imagecreatetruecolor(round(($this->ratio / $this->tableau_image[1]) * $this->tableau_image[0]), $this->ratio);
                imagecopyresampled($this->img_temp, $this->mini_image, 0, 0, 0, 0, round(($this->ratio / $this->tableau_image[1]) * $this->tableau_image[0]), $this->ratio, $this->tableau_image[0], $this->tableau_image[1]);
            } else {
                $this->img_temp = imagecreatetruecolor($this->ratio, round(($this->ratio / $this->tableau_image[0]) * $this->tableau_image[1]), $this->ratio);
                imagecopyresampled($this->img_temp, $this->mini_image, 0, 0, 0, 0, $this->ratio, round(($this->ratio / $this->tableau_image[0]) * $this->tableau_image[1]), $this->tableau_image[0], $this->tableau_image[1]);
            }

            imagepng($this->img_temp, $this->dir_mini_size . $this->src_image);
        }
    }

    /**
      Vérifie si une image est trop grande
      @param <string> $path
      @param <int> $max_width
      @param <int> $max_height
      @return <boolean>
     */
    public static function need_resize($path, $max_width, $max_height) {
        // Taille de l'image actuelle
        $img_size = @getimagesize($path);
        if (!$img_size) {
            return (FALSE);
        }
        $file_height = $img_size[1];
        $file_width = $img_size[0];

        if ($file_width > $max_width || $file_height > $max_height) {
            return (TRUE);
        }
        return (FALSE);
    }

    /**
      Redimensionne une image
      @param <string> $path
      @param <int> $max_width
      @param <int> $max_height
      @return <mixed>
     */
    public static function resize($path, $max_width, $max_height, $dest = NULL) {
        // Taille de l'image actuelle
        $img_size = @getimagesize($path);
        if (!$img_size) {
            return (FALSE);
        }
        $file_height = $img_size[1];
        $file_width = $img_size[0];

        // Extension de l'image
        $ext = Fonction::get_file_data($path, 'extension');

        // Tout d'abord on calcul la nouvelle taille de limage
        $width_handler = $file_width - $max_width;
        $height_handler = $file_height - $max_height;
        $size_handler = ($width_handler > $height_handler) ? $file_width : $file_height;
        $max_handler = ($width_handler > $height_handler) ? $max_width : $max_height;
        $new_width = ($file_width / $size_handler) * $max_handler;
        $new_height = ($file_height / $size_handler) * $max_handler;

        // Type d'image
        switch ($ext) {
            case 'jpg' :
            case 'jpeg' :
                $open = 'imagecreatefromjpeg';
                $write = 'imagejpeg';
                break;

            case 'png' :
                $open = 'imagecreatefrompng';
                $write = 'imagepng';
                break;

            case 'gif' :
                $open = 'imagecreatefromgif';
                $write = 'imagegif';
                break;

            case 'bmp' :
                $open = 'imagecreatefromwbmp';
                $write = 'imagewbmp';
                break;
        }

        // Redimensionnement de l'image
        $src = $open($path);
        $thumb = self::resize_alpha($src, $new_width, $new_height, $file_width, $file_height);

        // Affichage
        ob_start();
        $write($thumb);
        $content = ob_get_contents();
        ob_end_clean();

        if ($dest) {
            file_put_contents($dest, $content);
        } else {
            return $content;
        }
    }

    public static function set_size($path, $max_width, $max_height) {
        $img_size = @getimagesize($path);
        if (!$img_size) {
            return (FALSE);
        }
        $file_height = $img_size[1];
        $file_width = $img_size[0];

        // Tout d'abord on calcul la nouvelle taille de limage
        $width_handler = $file_width - $max_width;
        $height_handler = $file_height - $max_height;
        $size_handler = ($width_handler > $height_handler) ? $file_width : $file_height;
        $max_handler = ($width_handler > $height_handler) ? $max_width : $max_height;
        $new_width = ($file_width / $size_handler) * $max_handler;
        $new_height = ($file_height / $size_handler) * $max_handler;

        return [floor($new_width), floor($new_height)];
    }

    /**
      Redimensionement de l'image, avec gestion de la transparence
      @param <string> $src
      @param <int> $new_width
      @param <int> $new_height
      @param <int> $old_width
      @param <int> $old_height
      @return <Image>
     */
    private static function resize_alpha(&$src, $new_width, $new_height, $old_width, $old_height) {
        $thumb = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($thumb, FALSE);
        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
        imagesavealpha($thumb, TRUE);

        return ($thumb);
    }

    public static function collapse_picture($img_1, $img_2, $name, $type) {
        $ext1 = Image::getExtension($img_1);
        $ext2 = Image::getExtension($img_2);

        switch ($ext1) {
            case 'jpg' :
            case 'jpeg' :
                $source = imagecreatefromjpeg($img_1);
                $type2 = 'jpg';
                break;

            case 'png' :
                $source = imagecreatefrompng($img_1);
                $type2 = 'png';
                break;

            case 'gif' :
                $source = imagecreatefromgif($img_1);
                $type2 = 'gif';
                break;
        }

        switch ($ext2) {
            case 'jpg' :
            case 'jpeg' :
                $destination = imagecreatefromjpeg($img_2);
                break;

            case 'png' :
                $destination = imagecreatefrompng($img_2);
                break;

            case 'gif' :
                $destination = imagecreatefromgif($img_2);
                break;
        }

        // Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
        $largeur_source = imagesx($source);
        $hauteur_source = imagesy($source);
        $largeur_destination = imagesx($destination);
        $hauteur_destination = imagesy($destination);

        // On veut placer le logo en bas à droite, on calcule les coordonnées où on doit placer le logo sur la photo
        $destination_x = $largeur_destination - $largeur_source - 2;
        $destination_y = $hauteur_destination - $hauteur_source - 2;

        // On met le logo (source) dans l'image de destination (la photo)
        imagecopymerge($destination, $source, $destination_x, $destination_y, 0, 0, $largeur_source, $hauteur_source, 100);

        // On affiche l'image de destination qui a été fusionnée avec le logo
        $type($destination, DIR_TEMP . $name . '.jpg');

        return '<img src="' . DIR_TEMP . $name . '.jpg" alt="' . $name . '" class="border" onmouseout="killlink();" onmouseover="poplink(\'' . ROOT . DIR_PIC . $name . '.jpg\');" />';
    }

    public static function getExtension($file) {
        return strtolower(substr($file, strrpos($file, '.') + 1)); // On récupère les extensions des fichiers
    }

}
