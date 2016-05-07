<?php

/**
 * Class de variable global static
 */
class Globale {

    /**
     * Objet Base de donnée
     * @access public
     * @static
     */
    public static $db;

    /**
     * Objet Template
     * @access public
     * @static array
     */
    public static $Tpl;

    /**
     * Objet Ajax
     * @access public
     * @static array
     */
    public static $ajax;

    /**
     * Objet Langue
     * @access public
     * @static array
     */
    public static $langue;

    /**
     * Objet Footer
     * @access public
     * @static array
     */
    public static $footer;

    /**
     * Objet Header
     * @access public
     * @static array
     */
    public static $header;

    /**
     * Objet Panier
     * @access public
     * @static array
     */
    public static $panier;

    /**
     * Objet Password
     * @access public
     * @static array
     */
    public static $Pwd;

    /**
     * Objet Debut
     * @access public
     * @static array
     */
    public static $debut;

    /**
     * Ressource MySQL
     * @access public
     * @static array
     */
    public static $requete;

    /**
     * Tableau contenant les résultats de la ressource MySQL
     * @access public
     * @static array
     */
    public static $resultat;

    /**
     * Chaîne de caractère contenant les Meta-Tag HTML
     * @access public
     * @static array
     */
    public static $meta = '';

    /**
     * Détermine si on fait une redirection
     * @access public
     * @static array
     */
    public static $redirect = false;

    /**
     * Objet Log
     * @access public
     * @static array
     */
    public static $log;

    /**
     * Objet Date
     * @access public
     * @static array
     */
    public static $date;

    /**
     * Objet Highlight_sql
     * @access public
     * @static array
     */
    public static $highlight_sql;

    /**
     * Sexe
     * @access public
     * @static array
     */
    public static $sexe = [2 => 'Femme', 1 => 'Homme'];
    public static $genre = [1 => 'Adulte', 2 => 'Enfant'];

    /**
     * Liste des langues disponibles
     * @access public
     * @static array
     */
    public static $list_lang = ['fr' => 'Français', 'en' => 'English'];

}
