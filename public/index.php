<?php require_once dirname(__FILE__) . '/../config.php';

$dossierProjet = Config::getInstance()->get('Editeur.baseDir');

echo Utils::tpl("main.php", array(
        'title' => APPLI_NOM,
        'entete' => APPLI_NOM,
        'terminal' => Utils::tpl("terminal.php", array(
            'name' => 'term1')),
        'listeFichiers' => Utils::tpl("files.php", array("dossier" => $dossierProjet)),
        'editeur' => Utils::tpl("editor.php", array()),
    )
);