<?php require_once dirname(__FILE__).'/../config.php';

require_once ROOT_PATH.'/class/Terminal.php';

if(isset($_REQUEST['name'])) {
    $term = Terminal::getInstance($_REQUEST['name']);
    
    // Démarrage du shell
    if(isset($_REQUEST['run'])) {
        echo $term->runShell();
    }
    
    if(isset($_REQUEST['in'])) {
        $request_body = $_REQUEST['in'];

        if(strlen($_REQUEST['in']) > 0) {
            $term->input($_REQUEST['in']);
        }
        sleep(1);
    }
    
    echo $term->output();
}