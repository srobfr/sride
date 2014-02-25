<?php
/**
 * Gère la base de données SQLite
 *
 */
class Db {    
    private static $instance = null;
    
    /**
     * Le handle de la bd utilisée
     */
    private $dbHandle = null;
    private $fichier;
    private $logger;    
    private $nbRequetes = 0;
    private $tempsSql = 0;
    
    /**
     * Initialise le schéma de la base de données
     * à la première utilisation
     */
    public function initSchema() {        
        // Les comptes d'utilisateur
        $this->query("");
    }
    
    /**
     * Retourne l'instance du singleton
     * @return Db
     */
    public static function getInstance($fichier=null) {
        if(is_null(self::$instance)) {
            if(!class_exists('SQLiteDatabase')) throw new Exception("php5-sqlite n'est pas installé");
            self::$instance = new Db();
            self::$instance->logger = Logger::getLogger("srobgtd.sqlite");
            self::$instance->fichier = $fichier;
            self::$instance->open($fichier);
        }

        return self::$instance;
    }

    /**
     * Ouvre un fichier sqlite
     * @param <type> $fichier
     */
    private function open($fichier) {
        $aCreer = !(is_file($fichier) && filesize($fichier) > 0);
        $this->logger->debug("Ouverture du ".($aCreer?'NOUVEAU ':' ')."fichier sqlite '$fichier'");
        $this->dbHandle = sqlite_open($fichier, 0775);
        if($aCreer) {
            Callbacks::run('initialisation_bd_sqlite', $this->dbHandle);
        }
    }

    /**
     * Ferme le fichier courant
     */
    public function close() {
        sqlite_close($this->dbHandle);
        $this->dbHandle = null;

        foreach(self::$instances as $k => $v) {
            if($v===$this) {
                unset(self::$instances[$k]);
                break;
            }
        }
    }

    /**
     * Construit les requêtes
     * @param string $query
     * @param array $params
     * @return string
     */
    private static function genSql($query, $params) {
        $ps = array();
        if(!is_array($params)) $params = array($params);
        foreach ($params as $p) {
            if(is_null($p)) $ps[] = 'NULL';
            elseif(is_int($p)) $ps[] = "$p";
            else $ps[] = "'".str_replace('"', '\"', str_replace('\\', '\\\\', sqlite_escape_string($p)))."'";
        }

        $eval = '$r = sprintf($query, "'.implode('","', $ps).'");';
        eval($eval);
        return $r;
    }

    /**
     * Retourne 1 valeur
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function queryOne($sql, $params=array()) {
        $s = self::genSql($sql, $params);
        $this->logger->debug($this->fichier.':'.$s);
        $t = microtime(true);
        $res = sqlite_single_query($this->dbHandle, $s);
        $this->tempsSql += microtime(true) - $t;
        $this->nbRequetes++;
        if(is_array($res)) $res = (count($res)>0?$res[0]:null);
        $this->logger->debug($res);
        return $res;
    }

    /**
     * Retourne 1 colonne de résultat
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function queryCol($sql, $params=array()) {
        $s = self::genSql($sql, $params);
        $this->logger->debug($this->fichier.':'.$s);
        $t = microtime(true);
        $res = sqlite_single_query($this->dbHandle, $s);
        $this->tempsSql += microtime(true) - $t;
        $this->nbRequetes++;
        if(is_null($res)) $res = array();
        elseif(!is_array($res)) $res = array($res);
        $this->logger->debug($res);
        return $res;
    }

    /**
     * Retourne 1 ligne de résultat
     * @param string $sql
     * @return array Tableau associatif
     */
    public function queryRow($sql, $params=array()) {
        $s = self::genSql($sql, $params);
        $this->logger->debug($s);
        $t = microtime(true);
        $res = sqlite_array_query($this->dbHandle, $s);
        $this->tempsSql += microtime(true) - $t;
        $this->nbRequetes++;
        if(count($res)>0) $res = current($res);
        $r = array();
        if(is_array($res)) foreach ($res as $k=>$v) {
            if(!is_integer($k)) $r[$k] = $v;
        }
        $this->logger->debug($r);
        return $r;
    }

    /**
     * Retourne 1 set complet
     * @param string $sql
     * @return array(array)
     */
    public function queryArray($sql, $params=array()) {
        $s = self::genSql($sql, $params);
        $this->logger->debug($this->fichier.':'.$s);
        $t = microtime(true);
        $res = sqlite_array_query($this->dbHandle, $s);        
        $this->tempsSql += microtime(true) - $t;
        $this->nbRequetes++;
        $this->logger->debug($res);
        return $res;
    }

    /**
     * Retourne le nombre de lignes affectées
     * @param string $sql
     * @return int
     */
    public function query($sql, $params=array()) {
        $s = self::genSql($sql, $params); 
        $this->logger->debug($this->fichier.':'.$s);
        $t = microtime(true);
        sqlite_exec($this->dbHandle, $s);
        $this->tempsSql += microtime(true) - $t;
        $this->nbRequetes++;
        $res = sqlite_changes($this->dbHandle);
        $this->logger->debug($res);
        return $res;
    }

    /**
     * @return int Le dernier ID généré
     */
    public function getLastId() {
        return sqlite_last_insert_rowid($this->dbHandle);
    }

    /**
     * Effectue une insertion d'après le nom de table et les paramètres passés
     * @param string $table
     * @param array($key=>$value) $champs
     * @return int
     */
    public function insert($table, $champs) {
        $champsK = implode(',', array_keys($champs));
        $champsP = array();
        foreach($champs as $champ) {
            $champsP[] = '%s';
        }
        $champsP = implode(', ', $champsP);        
        $s = "INSERT INTO $table ($champsK) VALUES ($champsP)";
        return $this->query($s, array_values($champs));
    }

    /**
     * Met à jour une ligne
     * @param type $table
     * @param type $champs
     * @param type $conditionWhere
     * @return type 
     */
    public function update($table, $champs, $conditionWhere) {
        $sets = array(); $vals = array();
        foreach($champs as $k=>$v) {
            $sets[] = "$k=%s";
            $vals[] = $v;
        }
        $s = "UPDATE $table SET ".implode(', ', $sets);
        if(!empty($conditionWhere)) {
            $s.=" WHERE $conditionWhere";
        }
        return $this->query($s, $vals);
    }
    
    public function getNbRequetes() {
        return $this->nbRequetes;
    }
    
    public function getTempsSql() {
        return $this->tempsSql;
    }
}