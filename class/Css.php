<?php
/**
 * Helper pour l'insertion des scripts CSS dans les pages
 *
 * @author srob
 */
class Css {
    private static $css = array();
    
    public static function add($urlCss) {
        if(!in_array($urlCss, self::$css)) self::$css[] = $urlCss;
    }
    
    public static function getHtmlMeta() {
        $r = "";
        foreach(self::$css as $css) {
            $r .= "<link rel='stylesheet' media='all' href='$css' />\n";
        }
        return $r;
    }
}
