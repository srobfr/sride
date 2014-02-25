<?php // Terminal Web 
Js::add("js/jquery-1.6.4.js");
Js::add("js/terminal.js");
?>
<div id="<?php echo $name; ?>">
    <!-- TODO terminal -->
    <script>
        var terminal = new Terminal("<?php echo $name; ?>");
        terminal.run();  
        //setInterval(function(){terminal.sync();}, 1000);
    </script>
    <textarea id="taTerm" onkeypress="terminal.handleKbdEvent(event); return false;"></textarea>
    <button onclick="terminal.sync()">Sync</button>
    
    <span style="border:1px solid black" id="dbg">dbg</span>
        
</div>
    