// GET CONSOLES
terminal = document.getElementById('terminal');
console_choose = $("#console_choose");
var current_server = null;

function nl2br (str, is_xhtml) {

    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function updateConsole(server) {
    $.ajax({
        url:"ajax/getConsole",
        dataType:"json",
        method:"POST",
        data:{"server":server},
        success: function(response){
            terminal.innerHTML = nl2br(response)
            if(current_server !== server) {
                current_server = server;
                terminal.scrollTop = terminal.scrollHeight;
            }
        }
    })
}


console_choose.on('change', function() {
    updateConsole(this.value)
});

var i = setInterval(function (){
    updateConsole(console_choose[0].value)
}, 2000);

// SEND CMD
function sendCmd(server, cmd) {
    $.ajax({
        url:"ajax/sendServerCmd",
        dataType: "json",
        method:"POST",
        data:{"server":server,"cmd":cmd},
        success: function(response){
            let cmd_error = document.getElementById('cmd_error');
            let msg = "";
            if(response.state) msg += "<span class='green'><i class='fas fa-check-circle'></i> "
            else msg+="<span class='red'><i class='fas fa-times-circle'></i> "
            msg += response.msg+"</span>"
            cmd_error.innerHTML = msg;
        }
    })
}

$("#cmd_send").click(function (e){
    e.preventDefault();
    let cmd_value = $("#cmd_value");
    cmd = cmd_value[0].value;
    cmd_value[0].value = "";
    if(cmd)sendCmd(console_choose[0].value,cmd);
})

