<?php // Editeur
Css::add("css/editor.css");
Js::add("js/jquery-1.6.4.js");
Js::add("js/ace/ace.js");

// Themes
Js::add("js/ace/theme-eclipse.js");

// Modes
Js::add("js/ace/mode-php.js");

Js::add("js/editor.js");
?>
<div id="editorOnglets"></div>
<div id="editorContainer"></div>

<script>
    // Initialisation de l'éditeur
    var texteParDefaut;
    $(function(){
        texteParDefaut = <?php echo json_encode(Config::getInstance()->get("Editeur.defaultText")); ?>;
        initEditor();
    });
</script>