<?php

/**
 * Classe de configuration
 */
class Config {
    private static $instance = null;
    
    private $data = array();
    
    /**
     * Donne l'instance
     * @return Config 
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Fournit une valeur
     * @param type $cle
     * @return type 
     */
    public function get($cle) {
        if(array_key_exists($cle, $this->data)) {
            return $this->data[$cle];
        }
        
        return null;
    }
    
    
    /**
     * Fournit une valeur
     * @param type $cle
     * @return type 
     */
    public function __get($cle) {
        return $this->get($cle);
    }
    
    /**
     * Affecte la config
     * @param array $config
     */
    public function set($config) {
        $this->data = array_merge($this->data, $config);
    }
}
