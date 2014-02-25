<?php

//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//

/**
 * Ajoute du code HTML à chaque fichier ou dossier dans la liste
 */
function addButtons($path, $isFile) {
    if($isFile) {
        // C'est la ligne d'un fichier
        $r = "<input type='checkbox' class='chkboxFileTree' path='$path'>";
            //."<a href='javascript:clickSupprFichier(\"$path\");' class='fileListDelete'>x</a>";
        
    } else {
        // C'est la ligne d'un dossier
        $r = "<input type='checkbox' class='chkboxFileTree' path='$path'>";
            /*."<a href='javascript:clickSupprFichier(\"$path\");' class='fileListDelete'>x</a>"
            ."<a href='javascript:clickCreer(\"$path\");' class='fileListCreate'>+</a>";*/
    }
    
    return $r;
}

function getFileTree($root, $dir, $excludes = null) {    
    $dir = urldecode($dir);
    $r = '';
    
    function isExcluded($path, $excludes) {
        $nom = basename($path);
        foreach($excludes as $re) {
            if(preg_match($re, $nom)) return true;
        }
        return false;
    }
    
    if (file_exists($root . $dir) && is_readable($root . $dir) && !isExcluded($root . $dir, $excludes)) {
        $files = scandir($root . $dir);
        
        natcasesort($files);
        if (count($files) > 2) { /* The 2 accounts for . and .. */
            $r .= "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
            // All dirs
            foreach ($files as $file) {
                if (!isExcluded($root . $dir . $file, $excludes) && file_exists($root . $dir . $file) && $file != '.' && $file != '..' && is_dir($root . $dir . $file)) {
                    $buttons = addButtons($dir . $file, false);
                    $r .= "<li class=\"directory collapsed\">$buttons<a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
                }
            }
            // All files
            foreach ($files as $file) {
                if (!isExcluded($root . $dir . $file, $excludes) && file_exists($root . $dir . $file) && $file != '.' && $file != '..' && !is_dir($root . $dir . $file)) {
                    $ext = preg_replace('/^.*\./', '', $file);
                    $buttons = addButtons($dir . $file, true);
                    $r .= "<li class=\"file ext_$ext\">$buttons<a href=\"#\" rel=\"" . htmlentities($dir . $file) . "\">" . htmlentities($file) . "</a></li>";
                }
            }
            $r .= "</ul>";
        }
    }
    return $r;
}

?>