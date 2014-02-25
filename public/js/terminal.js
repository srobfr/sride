/* 
 * Code JS utilisé par SrPhpTerm
 */

var Terminal = function(name) {
    this.name = name;
    
    // Buffer de données en entrée du shell
    this.inputBuffer = '';

    /**
     * Démarre le shell coté serveur
     */
    this.run = function() {
        $.get('termRunner.php?run=1&name=' + name);        
    }
    
    /**
     * Ecrit les données et récupère l'affichage du shell
     */
    this.sync = function() {
        var ibNow = this.inputBuffer;
        this.inputBuffer = "";
        
        $.ajax({
            type: 'POST',
            url: 'termRunner.php?name=' + name,
            data: {'in': ibNow},
            success: function(result) {
                var st = $('#taTerm').scrollTop();
                $('#taTerm').val($('#taTerm').val()+result);
                if(""!=result) st+=99999;
                $('#taTerm').scrollTop(st);
            }
        });
        
        $('#dbg').html(this.inputBuffer);
    }
    
    /**
     * Gère les entrées du clavier dans la page
     */
    this.handleKbdEvent = function(event) {        
        var keynum;
        if(window.event) { // IE
            keynum = event.keyCode
        } else if(event.which) { // Netscape/Firefox/Opera        
            keynum = event.which
        }

        this.inputBuffer += String.fromCharCode(keynum);
        
        $('#dbg').html(this.inputBuffer);
        //this.sync();
    }
}


