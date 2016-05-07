<?php

/**
 * Génère une image aléatoire pour le code de confirmation visuel.
 * Ce code est ensuite sauvé dans la session du membre et vérifié lors du traitement du formulaire d'inscription.
 */
// On démarre les sessions
session_start();

// On défini les constantes d'extension de fichier et de la racine du site
define('PHPEXT', substr(strrchr(__FILE__, '.'), 1));
define('ROOT', '../');

// On inclu la page de chargement des class et de la config
include(ROOT . 'main/start.' . PHPEXT);

// On inclu la class_captcha pour la génération de l'image
include_once(ROOT . 'main/class/captcha/captcha.php');

// On défini le jeu de langue à utiliser
Header::$lang = new Lang($_SESSION['langue']);

// Nouvelle image captcha
$captcha = Captcha::factory();

// Créé le code aléatoirement
$captcha->create_str();

// On affiche l'image
$captcha->output();

// On met la réponse en session pour contrôler
$_SESSION['image_response'] = $captcha->store_str;

// On stop le script
exit;
