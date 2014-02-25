<?php // Fichier de configuration de SrIde

define('APPLI_NOM', "SrIde 1.0");
define('ROOT_PATH', dirname(__FILE__));

// Configuration de l'appli
require_once ROOT_PATH . '/class/Config.php';
Config::getInstance()->set(array(
    // Dossier des templates de l'appli
    "templates.dir" => ROOT_PATH . '/templates',

    // Emulateur de terminal
    "SrPhpTerm.shell" => "/bin/bash -i",
    "SrPhpTerm.baseDir" => "~",
    "SrPhpTerm.tmpDir" => "/tmp",
    "SrPhpTerm.freqInputSync" => 0.2,
    "SrPhpTerm.env" => array(),

    // Options de l'éditeur
    "Editeur.baseDir" => ROOT_PATH . '/..', // Dossier contenant les fichiers à éditer
    "Editeur.defaultText" => "<<<<<<<<<<<<<<< [Aucun fichier ouvert]>>>>>>>>>>>>>\n\n"
        . @file_get_contents(ROOT_PATH . '/README'),
    "Editeur.excludes" => array( // Regexp d'exclusion des dossiers et fichiers
        '~^\.svn$~',
    )
));

// Inclusions utiles
require_once ROOT_PATH . '/class/Utils.php';
require_once ROOT_PATH . '/class/Css.php';
require_once ROOT_PATH . '/class/Js.php';
