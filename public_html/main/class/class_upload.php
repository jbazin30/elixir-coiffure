<?php

/**
 * Class de gestion des upload
 */
class Upload {

    /**
     * Dossier d'upload
     * 	@access public
     * 	@var string
     */
    public $upload_dir = 'upload';

    /**
     * Taille maximale d'un fichier
     * 	@access public
     * 	@var integer
     */
    public $max_file_size = 33554432;

    /**
     * Extensions autorisées
     * 	@access public
     * 	@var array
     */
    public $extension = ['zip', 'rar', 'gz', 'tar', 'tgz', 'jpg', 'png', 'gif', 'txt', 'html', 'htm'];

    /**
     * Contient la liste des fichiers de la variable $_FILES
     * 	@access public
     * 	@var string
     */
    public $file;

    /**
     * Récupère et vérifie les arguments
     */
    public function __construct($select_groupe = null, $dir_upload = '', $size_max_file = null, $ext = []) {
        if (!empty($dir_upload)) {
            $this->upload_dir = $dir_upload;
        }
        if (!empty($size_max_file)) {
            $this->max_file_size = $size_max_file;
        }
        if (!empty($ext)) {
            $this->extension = $ext;
        }
        if (!empty($select_groupe)) {
            $this->groupe = $select_groupe;
        }
    }

    /**
     * Envoi d'un fichier
     * 	@return string
     */
    public function upload() {
        foreach ($_FILES as $key => $this->file) {
            // Si il y a bien un fichier envoyé
            if ($this->file['error'] != 4) {
                $this->file['ext'] = substr(strchr($this->file['name'], '.'), 1); // On récupère l'extension du fichier
                $this->file['basename'] = preg_replace('/(.*)\.([^.]+)$/', '\\1', $this->file['name']); // On récupère le nom du fichier sans son extension
                // On vérifie la taille du fichier envoyé par rapport au php.ini
                if ($this->file['error'] == 1) {
                    trigger_error('La taille du fichier excède la taille maximale configuré dans le php.ini', C_ERROR);
                }

                // On vérifie que le fichier ne dépasse pas la limite imposée par le webmaster et le php.ini
                if ($this->file['error'] == 2 || $this->file['size'] > $this->max_file_size) {
                    trigger_error('La taille du fichier excède la taille maximale spécifié par le webmaster', C_ERROR);
                }

                // On vérifie que le fichier est du bon type
                if (!@in_array($this->file['ext'], $this->extension)) {
                    trigger_error('Le type de fichier n\'est pas autorisé', C_ERROR);
                }

                // Si l'extension du fichier est jpg, png ou jpeg, on vérifie qu'il s'agit bien d'un fichier image en essayant de prendre ses dimensions.
                if (@in_array($this->file['ext'], ['jpg', 'png', 'jpeg'])) {
                    if (!@getimagesize($this->file['tmp_name'])) {
                        trigger_error('L\'image n\'est pas valide', C_ERROR);
                    }
                }

                // Si l'extension est html ou htm (pour l'ajout de bookmark Firefox par exemple), on modifie l'extension pour éviter tout probléme de sécurité
                if (@in_array($this->file['ext'], ['html', 'htm'])) {
                    $this->file['name'] = $this->file['basename'] . '.' . $this->extension[7];

                    // Si le fichier avec l'extension renommé existe, on renomme son nom
                    if (file_exists($this->upload_dir . '/' . $this->file['name'])) {
                        $this->file['name'] = md5($_SERVER['REQUEST_TIME']) . '.' . $this->extension[7];
                    }
                }

                // On vérifie si le fichier existe déjà sur le serveur
                if (file_exists($this->upload_dir . '/' . $this->file['name'])) {
                    $this->file['name'] = md5($_SERVER['REQUEST_TIME']) . '.' . $this->file['ext'];
                }

                // On vérifie que le fichier est bien uploadé
                if (!@is_uploaded_file($this->file['tmp_name'])) {
                    // On vérifie si le fichier est correctement envoyé
                    if ($this->file['error'] == 3) {
                        trigger_error('Le fichier n\'est que partiellement chargé', C_ERROR);
                    }

                    // On vérifie que le dossier temporaire existe bien
                    if ($this->file['error'] == 6) {
                        trigger_error('Le dossier temporaire est manquant', C_ERROR);
                    }

                    // On vérifie que le fichier est bien écrit sur le disque
                    if ($this->file['error'] == 7) {
                        trigger_error('Echec de l\'écriture du fichier sur le disque', C_ERROR);
                    }

                    trigger_error('Une erreur est survenue lors du chargement du fichier', C_ERROR);
                }

                // Si il n'y a pas d'erreur, on déplace le fichier temporaire vers le dossier de destination
                if ($this->file['error'] == 0) {
                    if (!@move_uploaded_file($this->file['tmp_name'], $this->upload_dir . '/' . $this->file['name'])) {
                        trigger_error('Une erreur est survenue lors du chargement du fichier', C_ERROR);
                    } else {
                        return true;
                    }
                }
            }
        }
    }

}
