<?php

/**
 * Classe de benchmark
 */
class bench {

    protected $bench = []; // Contient tous les marqueurs de bench

    public function __construct($name) {
        $this->init($name); // Initialisation du premier marqueur (obligatoire) à l'instanciation de classe.
    }

    public function init($name) {
        $this->bench[$name] = new mark;
    }

    public function __get($name) { // Méthode magique
        if (isset($this->bench[$name])) {
            return $this->bench[$name];
        } else {
            throw new Exception($name . ' n\'existe pas en tant que marque de bench !');
        }
    }

    public function allResult() {
        foreach ($this->bench as $key => $value) {
            $array[$key] = $value->getResult();
        }
        return Fonction::printr($array);
    }

}

class mark {

    protected $start; // Début du compteur
    protected $stop; // Fin de compteur

    final public function start() {
        $this->start = microtime(true);
    }

    final public function stop() {
        $this->stop = microtime(true);
    }

    final public function getResult() {
        return ($this->stop - $this->start);
    }

}
