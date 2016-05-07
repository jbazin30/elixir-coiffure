<?php

/**
 * Class de gestion des évènements AJAX
 */
class Ajax {

    /**
     * Constante indiquant qu'on envoie des données text/plain au navigateur
     * @access private
     * @var integer
     */
    const TXT = 1;

    /**
     * Constante indiquant qu'on envoie des données text/xml au navigateur
     * @access private
     * @var integer
     */
    const XML = 2;
    const JSON = 3;

    /**
     * Liste des évènements
     * @access protected
     * @var array
     */
    protected $events = [];

    /**
     * Ajoute un évènement
     * Le nombre d'arguments de cette fonction est variable, cependant les trois premiers arguments sont indispensables :
     * 	@param integer $type (@link TXT) constante Ajax::TXT ou Ajax::XML
     * 	@param string $name nom de l'évènement
     * 	@param string $callback fonction de callback appellée pour l'évènement
     * 	@param mixed $arguments arguments additionels pour la fonction de callback
     */
    public function add_event($type, $name, $callback) {
        $count = func_num_args();
        if ($count < 3) {
            trigger_error('Au moins 3 paramètres doivent être passés à la méthode Ajax::add_event()', FSB_ERROR);
        }

        // On récupère les arguments de la fonction
        $argv = [];
        for ($i = 3; $i < $count; $i++) {
            $argv[] = func_get_arg($i);
        }

        $this->events[$name] = [
            'type' => $type,
            'callback' => $callback,
            'argv' => $argv,
        ];
    }

    /**
     * Supprime un évènement
     * 	@param string $name Nom de l'évènement
     */
    public function drop_event($name) {
        if (isset($this->events[$name])) {
            unset($this->events[$name]);
        }
    }

    /**
     * Déclenche un évènement
     * 	@param string $name Nom de l'évènement
     * */
    public function trigger($name) {
        if (isset($this->events[$name])) {
            // Appel du callback pour l'évènement
            if (function_exists($this->events[$name]['callback'])) {
                $return = call_user_func_array($this->events[$name]['callback'], $this->events[$name]['argv']);
                if ($return !== NULL) {
                    // Génération du Content-type
                    switch ($this->events[$name]['type']) {
                        case self::XML :
                            Http::header('Content-type', 'text/xml');
                            break;

                        case self::JSON :
                            Http::header('Content-type', 'application/json');
                            break;

                        case self::TXT :
                        default :
                            Http::header('Content-type', 'text/html');
                            break;
                    }

                    echo $return;
                }
            }
        }
        exit;
    }

}
