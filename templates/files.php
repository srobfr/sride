<?php // Affiche la liste des fichiers du projet 
Css::add("css/jqueryFileTree.css");
Js::add("js/jquery-1.6.4.js");
Js::add("js/jqueryFileTree.js");
Js::add("js/filesList.js");
?>

<div class="divFuncFileList">
    <a href="javascript:clickCreer()">Nouveau</a> |
    <a href="javascript:clickSupprFichier()">Supprimer</a> | 
    <input type="checkbox" onchange="clickCocherDecocherTout(this)">
</div>

<div class="fileList" id="fileListContainer"></div>

<script>
    $(document).ready( function() {
        affFileTree();
    });
</script>