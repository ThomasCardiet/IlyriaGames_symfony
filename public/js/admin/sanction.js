table = document.getElementById("players");
function updateOnline(search = "") {
    $.ajax({
        url:"ajax/getOnlinePlayers",
        dataType:"json",
        method:"POST",
        data:{"search":search},
        success: function(response){
            table.innerHTML = "<tr class='table_head'>" +
                "                <td>Visuel</td>" +
                "                <td>Pseudo</td>" +
                "                <td>Groupe</td>" +
                "                <td></td>" +
                "            </tr>"
            response.forEach(player => {
                table.innerHTML+="<tr class=\"table_content\">" +
                    "                    <td><img src='https://minotar.net/helm/"+player.pseudo+"/42.png' alt=''></td>" +
                    "                    <td>"+player.pseudo+"</td>" +
                    "                    <td>"+player.groupe+"</td>" +
                    "                    <td><a class='btn' href='sanctions/"+player.id+"'>Sanctionner</a></td>" +
                    "                </tr>"
            })
        }
    })
}

var i = setInterval(function (){
    updateOnline();
}, 2000)

$("#buttonSearch").click(function (e){
    e.preventDefault();
    let value = $("#textSearch")[0].value;
    clearInterval(i);
    updateOnline(value);
})