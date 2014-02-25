<?php
/**
 * Helper pour l'insertion des scripts JS dans les pages
 
 * @author srob
 */
class Js {
    private static $js = array();
    
    public static function add($urlJs) {
        if(!in_array($urlJs, self::$js)) self::$js[] = $urlJs;
    }
    
    public static function getHtmlMeta() {
        $r = "";
        foreach(self::$js as $js) {
            $r .= "<script type='text/javascript' src='$js'></script>\n";
        }
        return $r;
    }
}

