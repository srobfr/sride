<?php

/**
 * Fournit des méthodes de gestion du stockage
 *
 */
class Folder {
        
    /**
     * Le chemin du dossier, sur le disque local 
     */ 
    private $path;
    
    /**
     * Constucteur
     */
    public function __construct($path) {
        $this->path = $path;
    }
    
    /**
     * Tente de créer un fichier dans le dossier courant
     */
    public function creerFichier($nom, $contenu='') {
        
    }
    
    /**
     * Tente de supprimer le dossier courant
     */
    public function supprimer() {
        
    }
    
    /**
     * Tente de supprimer un fichier dans le dossier courant
     */
    public function supprimerFichier($nomFichier) {
        
    }
    
    /**
     * Retourne les fichiers présents dans le dossier courant
     */
    public function getFiles() {
        
    }
    
    /**
     * Cherche tous les fichiers et dossiers dont le nom correspond
     * à la regexp
     */
    public function rechercheNom($regExp) {
        
    }

}

