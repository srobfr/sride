<?php

/**
 * SrPhpTerm
 * Emulateur de terminal Web
 * @author Srob
 */
class Terminal {
    private static $instances = array();
    
    private $name;
    
    private $infoFile;
    
    /**
     * Constructeur
     */
    private function __construct($name) {
        $this->name = $name;
        $this->infoFile = Config::getInstance()->get("SrPhpTerm.tmpDir")."/$name";
    }
    
    /**
     * Permet de récupérer une instance d'un terminal
     * @param string $name 
     * @return Terminal
     */
    public static function getInstance($name) {
        if(!array_key_exists($name, self::$instances)) {
            self::$instances[$name] = new self($name);
        }
        
        return self::$instances[$name];
    }
    
    /**
     * Appelle le shell sur le serveur.
     * Attention, cette fonction ne retourne que lorsque le shell se termine.
     */
    public function runShell() {        
        // Initialisation
        set_time_limit(0);
        $fIn = $this->infoFile . '.in';
        $fOut = $this->infoFile . '.out';
        
        $descriptorspec = array(
           0 => array("pipe", 'r'),
           1 => array("pipe", 'w'),
           2 => array("pipe", 'w'),
        );
        
        // C'est parti
        $process = proc_open('bash -i', $descriptorspec, $pipes, 
                Config::getInstance()->get('SrPhpTerm.baseDir'), 
                Config::getInstance()->get('SrPhpTerm.env'));
        
        if (is_resource($process)) {
            
            posix_mkfifo($fIn, 0777);
            posix_mkfifo($fOut, 0777);
            $fInHandle = fopen($fIn, 'r+');
            $fOutHandle = fopen($fOut, 'r+');
            
            file_put_contents("/tmp/log", "handles ouverts\n", FILE_APPEND);
            
            // Les streams dans lesquels on va lire des données
            //stream_set_blocking($fInHandle, 0);
            
            while(true) {
                // Ensuite, on attend des données à traiter
                $readStreams = array($fInHandle, $pipes[1], $pipes[2]);
                
                stream_select($readStreams, $writeStreams = null, $except = null, 30);

                if(in_array($fInHandle, $readStreams)) {
                    // On passe des données au shell
                    file_put_contents("/tmp/log", "Input\n", FILE_APPEND);
                    $data = fread($fInHandle, 1);
                    file_put_contents("/tmp/log", "$data\n", FILE_APPEND);
                    fwrite($pipes[0], $data);
                }

                if(in_array($pipes[1], $readStreams)) {
                    // Le shell a affiché sur son STDOUT
                    $data = fread($pipes[1], 1);	
                    file_put_contents("/tmp/log", "$data", FILE_APPEND);
                    fwrite($fOutHandle, $data);
                }

                if(in_array($pipes[2], $readStreams)) {
                    // Le shell a affiché sur son STDERR
                    $data = fread($pipes[2], 1);	
                    file_put_contents("/tmp/log", "$data", FILE_APPEND);
                    fwrite($fOutHandle, $data);
                }
            }
        }
    }
    
    /**
     * Envoie des données au shell
     * @param string $data 
     */
    public function input($data) {
        file_put_contents("/tmp/log", "writing '$data'\n", FILE_APPEND);
        $fh = fopen($this->infoFile.'.in', 'r+');        
        file_put_contents("/tmp/log", "writing '$data' : open ok\n", FILE_APPEND);
        fwrite($fh, $data);
        file_put_contents("/tmp/log", "writing '$data' : written ok\n", FILE_APPEND);
        fclose($fh);
        file_put_contents("/tmp/log", "writing '$data' : closed ok\n", FILE_APPEND);
    }
    
    /**
     * Récupère l'affichage du shell
     * @return string
     */
    public function output() {
        $f = $this->infoFile.'.out';
        file_put_contents("/tmp/log", "reading $f\n", FILE_APPEND);
        $fh = fopen($this->infoFile.'.out', 'r');
        stream_set_blocking($fh, 0);
        file_put_contents("/tmp/log", "reading : open ok\n", FILE_APPEND);
        $data = fread($fh, 256);        
        file_put_contents("/tmp/log", "reading : read ".strlen($data)."o\n", FILE_APPEND);
        fclose($fh);
        file_put_contents("/tmp/log", "reading : closed ok\n", FILE_APPEND);
        return $data;
    }
    
    /**
     * Arrête l'instance du shell sur le serveur
     */
    public function logout() {
        $this->input("#EndOfTerminal{$this->name}#");
    }
}

?>
