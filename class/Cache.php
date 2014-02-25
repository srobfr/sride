<?php
/**
 * Fournit des méthodes d'accès à la table Cache
 *
 */
class Cache {

    /**
     * Le "cache du cache", présent dans la RAM. Evite d'appeler plusieurs fois la base pour la même requête.
     * @var array();
     */
    private static $cacheCache = array();
    
    /**
     * Initialise le schéma dans la BD
     */
    private static function initSchema() {
        Db::getInstance()->query("CREATE TABLE cache (
            cle TEXT PRIMARY KEY,
            valeur TEXT,
            datePerime INTEGER
            )");
        
        Db::getInstance()->query("CREATE INDEX cache_datePerime_Idx ON cache (datePerime)");
    }

    /**
     * récupère une valeur du cache
     */
    public static function get($key, $default = null) {
        if(array_key_exists($key, self::$cacheCache)) {
            return self::$cacheCache[$key];
        }
        
        $t = time();
        $s = "SELECT valeur FROM cache WHERE cle = '$key' AND datePerime >= $t";
        $r = Db::getInstance()->queryOne($s);
        
        self::$cacheCache[$key] = $r;
        
        if(is_null($r)) $r = $default;
        return $r;
    }

    /**
     * Ajoute une valeur au cache
     * @param string $key la clé
     * @param string $valeur la valeur
     * @param int $lifetime la durée de vie, en secondes
     */
    public static function set($key, $valeur, $lifetime = null) {
        self::$cacheCache[$key] = $valeur;
        $valeur = str_replace("'", "\'", $valeur);
        $lt = time() + $lifetime;
        $s = "INSERT OR REPLACE INTO cache (cle, valeur, datePerime) VALUES ('$key', '$valeur', $lt)";
        Db::getInstance()->query($s);
    }

    /**
     * Nettoie le cache des entrées périmées
     */
    public static function purge() {
        $s = "DELETE FROM cache WHERE datePerime < ".time();
        Db::getInstance()->query($s);
    }
}