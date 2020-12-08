sanctions = document.getElementById("Sanctions");
power = sanctions.dataset.power;
power_ban = sanctions.dataset.need;

function canBan(){
    return parseInt(power) >= parseInt(power_ban);
}

function getPlayerId(){
    var url = window.location.href;
    return url.substring(url.lastIndexOf("/")+1);
}

function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }

    return true;
}

function addSanction(type, playerid, array = {}){
    if(type === "ban" && !canBan) return;
    $.ajax({
        url: "../ajax/addSanction",
        dataType: "json",
        method: "POST",
        data: {'type':type,"player_id":playerid,"sanction":array},
        success: function (response) {
            console.log(response)
        }
    })
}

$(".sanction_submit").click(function (e){
    e.preventDefault();
    let values = {}
    values['reason'] = $('#'+this.value+"_reason")[0].value;
    $('#'+this.value+"_reason")[0].value = "";
    if(this.value !== "kick"){
        let time = $('#'+this.value+"_time")[0].value;
        $('#'+this.value+"_time")[0].value = "";
        let type = $('#'+this.value+"_type")[0].value
        $('#'+this.value+"_type")[0].value = "";
        if(time && type !== "Unlimited") values['time'] = time;
        else if(type !== "Unlimited") return;
        values['type'] = type
    }
    addSanction(this.value, getPlayerId(), values);
})

function removeSanction(id, type){
    if(type === "ban" && !canBan) return;
    $.ajax({
        url: "../ajax/removeSanction",
        dataType: "json",
        method: "POST",
        data: {"id":id,"type":type},
        success: function (response) {}
    })
}

function getSanctions(id) {
    $.ajax({
        url:"../ajax/getCurrentSanctions",
        dataType:"json",
        method:"POST",
        data:{"player_id":id},
        success: function(response){
            sanctions.innerHTML = "Sanction:<br>";
            Object.values(response).forEach(val => {

            });
            if(!isEmpty(response)) {
                Object.values(response).forEach(val => {

                    if(val.type === "bannis" && !canBan()) return;

                    let expire;
                    if(parseInt(val.expiredate) === -1) expire = "</span>, expire <span>Jamais</span>";
                    else {
                        let date = new Date(parseInt(val.expiredate));
                        expire = "</span>, expire le <span>"+date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear()+" à "+date.getHours()+"h"+date.getMinutes()+"</span>"
                    }
                        sanctions.innerHTML += "-><span>" +
                            "                                    [" + val.type + "]" +
                            "                                </span> par" +
                            "                                <span>" +
                            "                                    " + val.staff + "" +
                            "                                </span> pour\n" +
                            "                                <span>" +
                            "                                    '" + val.reason + "'" + expire +
                            "                                <button class='sanction_delete_btn' type='submit' value='id:" + val.id + ",type:" + val.type + "' name='delete_sanction_submit'>Blanchir</button>" +
                            "                            <br>";
                });
            }
            else sanctions.innerHTML += "<span>Aucune</span>";

            $('.sanction_delete_btn').click(function (e){
                e.preventDefault();
                let params = this.value;
                params = params.split(",");
                let sanction = {};
                params.forEach(value => {
                    params = value.split(':');
                    sanction[params[0]] = params[1];
                })

                removeSanction(sanction['id'], sanction['type']);
            })
        }
    })
}

var i = setInterval(function (){
    getSanctions(getPlayerId());
}, 2000)