/**
 * Scripts JS pour l'affichage et la gestion des fichiers
 *
 */
 
/**
 * Coche / décoche toutes les cases visibles
 */
function clickCocherDecocherTout(chkbox) {
    if($(chkbox).attr('checked')) {
        $('input[class="chkboxFileTree"]').attr('checked', 'checked');
    } else {
        $('input[class="chkboxFileTree"]').removeAttr('checked');
    }
}
 
/**
 * Récupère
 */
function getCoches(unSeulement) {
    var r = new Array();
    $('input:checked[class="chkboxFileTree"]').each(function(){
        r.push($(this).attr('path'));
    });
    
    if(unSeulement === true) {
        if(r.length>0) r = r[0];
        else r = null;
    }
    
    return r;
}

/**
 * Affiche le message 'Voulez-vous supprimer le fichier ?'
 */
function clickSupprFichier() {
    // On récupère tous les fichiers selectionnés
    var paths = getCoches();
    var nbFichiers = paths.length;
    if(nbFichiers == 0) {
        // Rien à supprimer
        log("Aucun fichier à supprimer");
        
    } else {    
        var supprLien = "doSupprFichiers('"+paths+"');";        
        var msg = "Voulez-vous réellement <a href=\"javascript:"+supprLien+"\">supprimer</a> ces "+nbFichiers+" fichiers ?";        
        log(msg);
    }
}

/**
 * Supprime réellement le fichier
 */
function doSupprFichiers(paths) {
    paths = paths.split(/,/g);
    
    function callAjaxSuppr() {
        if(paths.length==0) {        
            $('#fileListContainer').html('');
            affFileTree();
            return;
        }
        
        path = paths.pop();
        log("Suppression de " + path + "...");
        $.getJSON("fileReadWrite.php", {n:path, suppr:1}, function(reponse){
            var erreurs = '';
            for (var i in reponse.erreur) {
                erreurs += '' + i + ' : ' + reponse.erreur[i] + "\n";
            }

            if (erreurs == '') {
                log(path + " supprimé !!");
                callAjaxSuppr();

            } else {
                alert(erreurs);
            }
        });
    }
    
    callAjaxSuppr();
}

/**
 * Affiche la boite pour créer un fichier/dossier
 */
function clickCreer() {
    // on récupère le chemin du dossier coché
    var path = getCoches(true);
    if (path == null) path = '';
    
    var nom = prompt("Saisissez le nom du fichier à créer dans '"+path+"'\n(préfixer de '*' pour créer un dossier, par exemple '*sousDossier')");
    if(nom[0]=='*') {
        nom = nom.substr(1);
        log("Création du dossier '"+path+'/'+nom+"'...");
        $.getJSON("fileReadWrite.php", {n:path+'/'+nom, creer:'dossier'}, function(reponse){
            var erreurs = '';
            for (var i in reponse.erreur) {
                erreurs += '' + i + ' : ' + reponse.erreur[i] + "\n";
            }

            if (erreurs == '') {
                $('#fileListContainer').html('');
                affFileTree();
                log("Dossier " + path + '/' + nom + " créé.");

            } else {
                alert(erreurs);
            }
        });
        
    } else {
        log("Création du fichier '"+path+'/'+nom+"'...");
        $.getJSON("fileReadWrite.php", {n:path+'/'+nom, creer:'fichier'}, function(reponse){
            var erreurs = '';
            for (var i in reponse.erreur) {
                erreurs += '' + i + ' : ' + reponse.erreur[i] + "\n";
            }

            if (erreurs == '') {
                $('#fileListContainer').html('');
                affFileTree();

                var lienFichier = "openFileInEditor(\"" + path + '/' + nom + "\")";
                log("Fichier <a href='javascript:" + lienFichier + "'>" + nom + "</a> créé.");

            } else {
                alert(erreurs);
            }
        });
    }    
}

/**
 * Affiche la liste des dossiers & fichiers
 */
function affFileTree() {
    $('#fileListContainer').fileTree({
        root: '/',
        filtre: $('#filtreFichiers').val(),
        script: 'folders.php',
        expandSpeed: 0,
        collapseSpeed: 0,
        multiFolder: true
    }, function(file) {
        openFileInEditor(file);
    });
}