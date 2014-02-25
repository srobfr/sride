<?php require_once dirname(__FILE__) . '/../config.php';

// $r est le tableau envoyé au format JSON en réponse à l'appel de cette page.
// Attention, selon la configuration PHP, des messages d'erreurs (warning...) peuvent apparaître
// dans la réponse HTTP et empêcher l'interprétation par le code coté client
$r = array(
    'erreur' => array()
);

if(!isset($_REQUEST['n'])) {
    // Pas de nom de fichier fourni
    $r['erreur'][1] = "Pas de fichier fourni";
    
} else {
    // Ici on a un nom de fichier
    $path = Config::getInstance()->get('Editeur.baseDir').$_REQUEST['n'];

    if(strpos(realpath(dirname($path)), realpath(Config::getInstance()->get('Editeur.baseDir'))) !== 0) {
        // Problème. on tente d'accéder à un chemin plus haut que la config ne le permet.
        $r['erreur'][12] = "Chemin invalide.";

    } elseif(array_key_exists('contenu', $_REQUEST)) {

        $contenu = $_REQUEST['contenu'];
        
        // On veut écrire le fichier
        if(!is_dir(dirname($path))) {
            $r['erreur'][4] = "Le dossier n'existe pas";
        } elseif(!is_writable(dirname($path))) {
            $r['erreur'][5] = "Impossible d'écrire dans le dossier '".dirname($path)."' (permissions ?)";
            
        } elseif(file_exists($path) && !is_writable($path)) {
            $r['erreur'][7] = "Impossible d'écrire dans le fichier '$path' (permissions ?)";
            
        } else {
            // Tout ok
            if(false===@file_put_contents($path, $contenu)) {
                $r['erreur'][6] = "Erreur d'écriture.";
            }
        }
    
    } elseif(array_key_exists('suppr', $_REQUEST)) {
        // On veut supprimer le fichier / dossier
        if(!is_dir(dirname($path))) {
            $r['erreur'][4] = "Le dossier n'existe pas";
        } elseif(!is_writable(dirname($path))) {
            $r['erreur'][5] = "Impossible d'écrire dans le dossier '".dirname($path)."' (permissions ?)";
            
        } elseif(file_exists($path) && !is_writable($path)) {
            $r['erreur'][7] = "Impossible de supprimer le fichier '$path' (permissions ?)";
            
        } else {
            // Tout ok
            if(is_dir($path)) {
                Utils::delTree($path);
            } else {
                if(false===@unlink($path)) {
                    $r['erreur'][6] = "Erreur lors de la suppression.";
                }
            }
        }
    
    } elseif(array_key_exists('creer', $_REQUEST)) {
        // On veut créer le fichier / dossier
        if(!is_dir(dirname($path))) {
            $r['erreur'][4] = "Le dossier n'existe pas";
        } elseif(!is_writable(dirname($path))) {
            $r['erreur'][5] = "Impossible d'écrire dans le dossier '".dirname($path)."' (permissions ?)";
            
        } elseif(is_file($path)) {
            $r['erreur'][8] = "Le fichier '$path' existe déjà.";
            
        } elseif(is_dir($path)) {
            $r['erreur'][9] = "Le dossier '$path' existe déjà.";
            
        } else {
            // Tout ok
            
            if($_REQUEST['creer']=='dossier') {
                if(false===@mkdir($path)) {
                    $r['erreur'][10] = "Erreur lors de la création du dossier.";
                }
            } else {
                if(false===@file_put_contents($path, '')) {
                    $r['erreur'][11] = "Erreur lors de la création du fichier.";
                }
            }
        }
    
    } else {
        // On veut lire le fichier
        if(!is_file($path)) {
            $r['erreur'][2] = "Fichier introuvable";

        } elseif(!is_readable ($path)) {
            $r['erreur'][3] = "Impossible de lire le fichier (permissions ?)";

        } else {
            $r['contenu'] = file_get_contents($path);
        }
    }
}

echo json_encode($r);

