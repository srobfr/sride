<?php 
Css::add("css/main.css");
Js::add("js/main.js");
Js::add("js/log.js");
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">        
        <title><?php echo $title; ?></title>
        
        <?php echo Css::getHtmlMeta(); ?>
        <?php echo Js::getHtmlMeta(); ?>
    </head>
    <body>
        <table class="globalTable">
            <!--<tr>
                <td colspan="2">
                    <div id="entete">
                        <?php /*echo $entete */?>
                    </div>
                </td>
            </tr>-->
            <tr>
                <td>
                    <?php echo $listeFichiers; ?>
                    <div id='log'></div>
                    <script>
                        $(window).load(function(){
                            log("<?php echo APPLI_NOM ?><br><a href='http://scripts.srob.fr/SrIde/Aide' target='_blank'>>>>> Aide <<<<<</a>");
                        });
                    </script>
                </td>
                <td>
                    <?php echo $editeur ?>
                </td>
            </tr>
        </table>
    </body>
</html>