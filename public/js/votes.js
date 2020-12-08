let loader = $('.votes_loader_block');

loader.hide();

function send_form(values) {
    var form = document.createElement('form')
    form.setAttribute('method', 'post');
    for(var key in values) {
        var hiden_input = document.createElement('input');
        hiden_input.setAttribute('type', 'hidden');
        hiden_input.setAttribute('name', key);
        hiden_input.setAttribute('value', values[key]);
        form.appendChild(hiden_input);
    }
    document.body.appendChild(form);
    form.submit();
}

let auto_stop_timer = 0;

let verify_vote = null;

function parseMillisecondsIntoReadableTime(milliseconds){
    //Get hours from milliseconds
    var hours = milliseconds / (1000*60*60);
    var absoluteHours = Math.floor(hours);
    var h = absoluteHours > 9 ? absoluteHours : '0' + absoluteHours;

    //Get remainder from hours and convert to minutes
    var minutes = (hours - absoluteHours) * 60;
    var absoluteMinutes = Math.floor(minutes);
    var m = absoluteMinutes > 9 ? absoluteMinutes : '0' +  absoluteMinutes;

    //Get remainder from minutes and convert to seconds
    var seconds = (minutes - absoluteMinutes) * 60;
    var absoluteSeconds = Math.floor(seconds);
    var s = absoluteSeconds > 9 ? absoluteSeconds : '0' + absoluteSeconds;


    return h + ' Heure(s) ' + m + ' Minute(s) et ' + s + ' Seconde(s)';
}

var hasVoted = [];
let time_limit = 5; //in minutes

function verify_votes(id) {

    $.getJSON("https://api.ipify.org?format=json",
        function (ip_data) {

            let ip_adress = ip_data.ip;
            let site_id;

            switch (id) {

                // SERVEUR-PRIVE.NET
                case 0:

                    site_id = '7yZ1HW83RqSoB0D'

                    if(hasVoted[id] === undefined) {
                        $.getJSON(
                            'https://serveur-prive.net/api/vote/json/' + site_id + '/' + ip_adress,
                            function (data) {
                                if(parseInt(data.status) === 1) hasVoted[id] = true;
                                else hasVoted[id] = false;
                            })
                    }else {
                        $.getJSON(
                            'https://serveur-prive.net/api/vote/json/' + site_id + '/' + ip_adress,
                            function (data) {
                                if (parseInt(data.status) === 1){
                                    if (hasVoted[id] === false) {
                                        stop_verify(1);
                                    } else return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(data.nextvote * 1000));
                                }
                            })
                    }
                    break;


                // SERVEUR-MINECRAFT.ORG
                case 1:

                    site_id = 59858;

                    if(hasVoted[id] === undefined) {
                        $.get(
                            'https://www.serveurs-minecraft.org/api/is_valid_vote.php',
                            {id: site_id, format: 'ajax', ip: ip_adress, duration: 7200},
                            function (data) {
                                let date = Date.parse(data.lastVoteDate);
                                let interval = Date.now() - date;
                                let day = 8.64e+7;
                                if(interval < day) {
                                    hasVoted[id] = true;
                                    return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(day-interval));
                                }else hasVoted[id] = false;
                            })
                    }else if(hasVoted[id] === false) {
                        $.get(
                            'https://www.serveurs-minecraft.org/api/is_valid_vote.php',
                            {id: site_id, format: 'ajax', ip: '93.26.170.126', duration: time_limit},
                            function (data) {
                                if (data.votes && data.votes > 0)
                                    stop_verify(1);
                                else if (data.error)
                                    stop_verify(0, data.error);
                            })
                    }
                    break;

                // TOP-SERVEURS.FR
                case 2:

                    site_id = 'LX7LTW1E5E';

                    if(hasVoted[id] === undefined) {
                        $.getJSON(
                            'https://api.top-serveurs.net/v1/votes/check-ip?server_token=' + site_id + '&ip=' + ip_adress)
                            .error(hasVoted[id] = false)
                            .success(hasVoted[id] = true)
                    }else {
                        $.getJSON(
                            'https://api.top-serveurs.net/v1/votes/check-ip?server_token=' + site_id + '&ip=' + ip_adress)
                            .success(function(data) {
                                if(hasVoted[id] === false) {
                                    stop_verify(1);
                                }else return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(data.duration * 60000));
                            })
                    }
                    break;

            }
        })

}

function start_verify_votes(id) {

    $.getJSON("https://api.ipify.org?format=json",
        function (ip_data) {

            let ip_adress = ip_data.ip;

            verify_vote = setInterval(

                function () {

                    let site_id;

                    auto_stop_timer += 5;
                    if(auto_stop_timer >= (time_limit*60)) {
                        stop_verify(0, 'Temps de vote dépassé (> à ' + time_limit + ' minutes)')
                    }

                    switch (id) {

                        // SERVEUR-PRIVE.NET
                        case 0:

                            site_id = '7yZ1HW83RqSoB0D'

                            if(hasVoted[id] === undefined) {
                                $.getJSON(
                                    'https://serveur-prive.net/api/vote/json/' + site_id + '/' + ip_adress,
                                    function (data) {
                                        if(parseInt(data.status) === 1) hasVoted[id] = true;
                                        else hasVoted[id] = false;
                                    })
                            }else {
                                $.getJSON(
                                    'https://serveur-prive.net/api/vote/json/' + site_id + '/' + ip_adress,
                                    function (data) {
                                        if (parseInt(data.status) === 1){
                                            if (hasVoted[id] === false) {
                                                stop_verify(1);
                                            } else return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(data.nextvote * 1000));
                                        }
                                    })
                            }
                            break;


                        // SERVEUR-MINECRAFT.ORG
                        case 1:

                            site_id = 59858;

                            if(hasVoted[id] === undefined) {
                                $.get(
                                    'https://www.serveurs-minecraft.org/api/is_valid_vote.php',
                                    {id: site_id, format: 'ajax', ip: ip_adress, duration: 7200},
                                    function (data) {
                                        let date = Date.parse(data.lastVoteDate);
                                        let interval = Date.now() - date;
                                        let day = 8.64e+7;
                                        if(interval < day) {
                                            hasVoted[id] = true;
                                            return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(day-interval));
                                        }else hasVoted[id] = false;
                                    })
                            }else if(hasVoted[id] === false) {
                                $.get(
                                    'https://www.serveurs-minecraft.org/api/is_valid_vote.php',
                                    {id: site_id, format: 'ajax', ip: '93.26.170.126', duration: time_limit},
                                    function (data) {
                                        if (data.votes && data.votes > 0)
                                            stop_verify(1);
                                        else if (data.error)
                                            stop_verify(0, data.error);
                                    })
                            }
                            break;

                        // TOP-SERVEURS.FR
                        case 2:

                            site_id = 'LX7LTW1E5E';

                            if(hasVoted[id] === undefined) {
                                $.getJSON(
                                    'https://api.top-serveurs.net/v1/votes/check-ip?server_token=' + site_id + '&ip=' + ip_adress)
                                    .error(hasVoted[id] = false)
                                    .success(hasVoted[id] = true)
                            }else {
                                $.getJSON(
                                    'https://api.top-serveurs.net/v1/votes/check-ip?server_token=' + site_id + '&ip=' + ip_adress)
                                    .success(function(data) {
                                        if(hasVoted[id] === false) {
                                            stop_verify(1);
                                        }else return stop_verify(0, "Vous pourrez voter à nouveau sur ce site dans " + parseMillisecondsIntoReadableTime(data.duration * 60000));
                                    })
                            }
                            break;

                    }

                }

                , 5000);
    })
}

$('.vote-btn').click(function () {
    loader.show();
    verify_votes(parseInt(this.id));
    auto_stop_timer = 0;
    clearInterval(verify_vote);
    start_verify_votes(parseInt(this.id));
})

function stop_verify(state = 0, error = '') {

    loader.hide();
    clearInterval(verify_vote);
    send_form({ "_state" : state, '_error' : error });
}