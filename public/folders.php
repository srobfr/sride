<?php require_once dirname(__FILE__) . '/../config.php';
// Récupère la liste des fichiers sur le serveur

require_once ROOT_PATH.'/lib/jqueryFileTree.php';

echo getFileTree(Config::getInstance()->get('Editeur.baseDir'), 
        $_POST['dir'],
        Config::getInstance()->get('Editeur.excludes'));