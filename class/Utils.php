<?php
class Utils {
    
    /**
     * Appelle un template
     * @param string $templateFile Le fichier template
     * @param array $data La variable qui sera dispo dans le code du template
     */
    public static function tpl($templateFile, $data = array()) {
        extract($data);
        ob_start();
        include(Config::getInstance()->get("templates.dir").'/'.$templateFile);
        return ob_get_clean();
    }
    
    /**
     * Supprime un dossier et son contenu
     */
    public static function delTree($dir) { 
        $files = glob( $dir . '*', GLOB_MARK ); 
        foreach( $files as $file ){ 
            if( substr( $file, -1 ) == '/' ) 
                self::delTree( $file ); 
            else 
                @unlink( $file ); 
        } 
        
        if (is_dir($dir)) @rmdir( $dir );
    } 
}