<?php

/**
 * Class de DB
 */
class Db {

    private $ctx;
    private $db;
    private $nb_requete;
    private $rq;

    public function __construct($argx) {
        $this->ctx = $argx;
        // On tente de se connecter
        try {
            // Chaine de connexion au serveur (à rendre dynamique par un fichier de conf)
            $db = $this->ctx['db'];
            $host = $this->ctx['host'];
            $user = $this->ctx['user'];
            $pass = $this->ctx['pass'];
            $dsn = "mysql:dbname=$db;host=$host;charset=utf8";

            // Paramètre pour la connexion
            $arrExtraParam = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];

            // On se connecte
            $this->db = new PDO($dsn, $user, $pass, $arrExtraParam);

            // On demande à afficher les rapports d'erreurs et à émettre des exceptions à capturer
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // On retourne l'objet de connexion
            return $this->db;
        } catch (PDOException $e) { // On capture l'erreur de connexion
            $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            die($msg);
        }
    }

    public function requete($sql, $params = null) {
        if ($params == null) {
            $this->rq = $this->db->query($sql); // exécution directe
        } else {
            $this->rq = $this->db->prepare($sql);  // requête préparée
            $this->rq->execute($params);
        }
        $this->nb_requete++;

        return $this->rq;
    }

    public function request($sql, $params = null) {
        if ($params == null) {
            $this->rq = $this->db->query($sql); // exécution directe
        } else {
            $this->rq = $this->db->prepare($sql);  // requête préparée
            $this->rq->execute($params);
        }

        return $this->rq->fetchAll()[0];
    }

    public function tableau($style = PDO::FETCH_ASSOC) {
        try {
            return $this->rq->fetchAll($style);
        } catch (PDOException $e) {
            $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            die($msg);
        }
    }

    public function row($style = PDO::FETCH_ASSOC) {
        try {
            return $this->rq->fetch($style);
        } catch (PDOException $e) {
            $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            die($msg);
        }
    }

    public function last_insert_id() {
        try {
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            die($msg);
        }
    }

    public function get_affected_row_nb() {
        try {
            return $this->db->rowCount();
        } catch (PDOException $e) {
            $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
            die($msg);
        }
    }

    public function nbRequete() {
        return $this->nb_requete;
    }

}
