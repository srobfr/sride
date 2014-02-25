/**
 * log.js
 * Gère l'encart affichant les lignes de logs de l'application
 */

/**
 * Ajoute une ligne de log
 */
function log(log) {
    $('#log').append(log+"<br/>");
    $('#log').scrollTop(999999);
}
