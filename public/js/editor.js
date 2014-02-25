/**
 * editor.js
 * Scripts pour la zone d'édition de code
 */

// Les onglets de l'éditeur
var ongletsEditeur = new Array();

var canon = require('pilot/canon');

// Le texte affiché dans l'éditeur lorsque aucun onglet n'est ouvert
var texteParDefaut = '';

// Raccourcis clavier
canon.addCommand({
    name: "save",
    bindKey: {
        win: "Ctrl-S",
        mac: "Command-S",
        sender: "editor"
    },
    exec: function() {
        var idxOnglet = getMaxSelectIndexOnglet();
        if(idxOnglet >= 0) {
            ongletsEditeur[idxOnglet].contenu = getEditorContenu();
            
            // On peut sauvegarder l'onglet
            log("Sauvegarde de '"+ongletsEditeur[idxOnglet].path+"'...");
            
            $.post("fileReadWrite.php", {
                n: ongletsEditeur[idxOnglet].path, 
                contenu: ongletsEditeur[idxOnglet].contenu
                }, function(reponse){
                    // Fin de l'appel ajax
                    var erreurs = '';
                    for (var i in reponse.erreur) {
                        erreurs += '' + i + ' : ' + reponse.erreur[i]+"\n";                        
                    }
                    
                    if(erreurs=='') {
                        log("Sauvegarde effectuée.");
                    } else {
                        alert(erreurs);
                    }
                },
                "json"
            );
        }
    }
});

/**
 * Gère le redimensionnement de la page
 */
function resizeEditor() {
    $('#editor').css("width", (document.body.clientWidth - 250)+"px");
    $('#editor').css("height", ($(window).height()-40)+"px");
    $('.fileList').css("height", ($(window).height()-240)+"px");
    window.editor.resize();        
}

/**
 * Initialise l'éditeur
 */
function initEditor() {
    $('#editorContainer').html(''); // Suppression ancien editeur
    $('#editorContainer').append('<div id="editor"></div>')
    
    window.editor = ace.edit("editor");
    
    // Pour le redimensionnement
    $(window).resize(function(){
        resizeEditor();
    });

    // Config thème et mode
    window.editor.setTheme("ace/theme/eclipse");
    var mode = require("ace/mode/php").Mode;
    window.editor.getSession().setMode(new mode());        
    setDefautText();
    resizeEditor();
}

/**
 * Change le contenu affiché par l'éditeur
 */
function setEditorContenu(contenu) {
    window.editor.getSession().setValue(contenu);
}

/**
 * Retourne le contenu affiché par l'éditeur
 */
function getEditorContenu() {
    return window.editor.getSession().getValue();
}

/**
 * Affiche le texte par défaut dans l'éditeur
 */
function setDefautText() {
    setEditorContenu(texteParDefaut);
}

/**
 * Ouvre un fichier dans l'éditeur
 */
function openFileInEditor(filepath) {
    // On vérifie s'il n'est pas déjà ouvert, si oui on récupère son id et le selectIndex Max.
    var ongletIdx = -1;
    for(var i in ongletsEditeur) {
        if(ongletsEditeur[i].path == filepath) {
            ongletIdx = i;    
        }
    }
    
    if(ongletIdx > -1) {
        // On ne continue pas le chargement
        log("Le fichier '"+filepath+"' est déjà ouvert.");
        // ...mais on passe le fichier devant
        selectOnglet(ongletIdx);
        
    } else {
        // Création de l'onglet
        var nomFichier = filepath.replace(/\\/g,'/').replace( /.*\//, '' );
        
        // On récupère le contenu du fichier
        $.getJSON("fileReadWrite.php", {n:filepath}, function(reponse){
            log("Chargement de '"+filepath+"'...");
            
            var erreurs = '';
            for (var i in reponse.erreur) {
                erreurs += '' + i + ' : ' + reponse.erreur[i]+"\n";          
            }

            if(erreurs=='') {
                ongletsEditeur.push({
                    path: filepath,
                    titre: nomFichier,
                    selectIndex: 0,
                    contenu: reponse.contenu
                });
				
                selectOnglet(ongletsEditeur.length - 1);
                log("Fichier '"+nomFichier+"' chargé.");
                
            } else {
                alert(erreurs);
            }
        });
    }
}


/**
 * Change l'onglet selectionné (=affiché dans l'éditeur)
 */
function selectOnglet(idx) {
    // On récupère l'onglet déjà selectionné
	if(ongletsEditeur.length>0) {
		var maxSelectIndexOnglet = getMaxSelectIndexOnglet();
		if(maxSelectIndexOnglet != idx) {
			maxSelectIndexOnglet = ongletsEditeur[maxSelectIndexOnglet];
			maxSelectIndexOnglet.cursorLine = window.editor.renderer.getScrollTopRow();
			maxSelectIndexOnglet.contenu = getEditorContenu();
		}
	}
    			        
    ongletsEditeur[idx].selectIndex = getMaxSelectIndex() + 1;
					        
    setEditorContenu(ongletsEditeur[idx].contenu);
    var numLine = 1;
    if(undefined!==ongletsEditeur[idx].cursorLine) numLine += ongletsEditeur[idx].cursorLine;
    window.editor.renderer.scrollToLine(numLine);
								    
    refreshBarreOnglets();
}

/**
 * Ferme un onglet
 */
function fermerOnglet(idx) {
    // On supprime l'onglet de la liste
    ongletsEditeur.splice(idx, 1);
    var ongletIdx = getMaxSelectIndexOnglet();
    
    if(ongletIdx>=0) selectOnglet(ongletIdx);
    
    refreshBarreOnglets();
    if(ongletsEditeur.length == 0) {
        setDefautText();
    }
}

/**
 * Rafraichit la barre d'onglets de l'éditeur
 */
function refreshBarreOnglets() {
    var html = "";
    
    // On trouve le maxSelectIndex
    var maxSelectIndex = getMaxSelectIndex();
    
    for(var i in ongletsEditeur) {
        var span = "<span class='ongletEditeur%Sel%' onclick='%OnClick%' title='%Alt%'>%Nom%<span class='redClose' onclick='%OnClickClose%'>%DerniereLettre%</span></span>";
        var sel = '';
        if(maxSelectIndex == ongletsEditeur[i].selectIndex) {
            sel = ' sel';
        }
        
        var nom = ongletsEditeur[i].titre.substr(0, ongletsEditeur[i].titre.length-2);
        var derniereLettre = ongletsEditeur[i].titre.substr(nom.length, 2);
        
        var onClick = 'selectOnglet('+i+')';
        var onClickClose = 'fermerOnglet('+i+')';
        var alt = ongletsEditeur[i].path;
        
        span = span.replace("%Sel%", sel)
            .replace("%Alt%", alt)
            .replace("%Nom%", nom)
            .replace("%selIdx%", ongletsEditeur[i].selectIndex)
            .replace("%OnClickClose%", onClickClose)
            .replace("%OnClick%", onClick)
            .replace("%DerniereLettre%", derniereLettre);
        
        html += span;
    }
    
    $('#editorOnglets').html(html);
}


/**
 * Retourne l'index de l'onglet selectionné
 */
function getMaxSelectIndex() {
    var maxSelectIndex = 0;
    for(var i in ongletsEditeur) {
        if(maxSelectIndex < ongletsEditeur[i].selectIndex) {
            maxSelectIndex = ongletsEditeur[i].selectIndex;
        }
    }
    
    return maxSelectIndex;
}


function getMaxSelectIndexOnglet() {
    var maxSelectIndex = -1;
    var r = -1;
    for(var i in ongletsEditeur) {
        if(maxSelectIndex < ongletsEditeur[i].selectIndex) {
            maxSelectIndex = ongletsEditeur[i].selectIndex;
            r = i;
        }
    }
    
    return r;
}


