var colors;

function implementGraph(name, infos){

    let ctx;
    let data;
    let options;
    let config;

    switch (name)
    {
        // ADMIN STATS GRAPH
        case 'admin_stats':

            let values = infos['values'];

            ctx = document.getElementById('graph1').getContext('2d')

            data = {
                labels: ['Avant-avant-hier', 'Avant-hier', 'Hier', 'Aujourd\'hui'],
                datasets: [
                    {
                        label: 'Visites',
                        backgroundColor: '#00C292',
                        data: values['day_visits'].reverse(),
                    },

                    {
                        label: 'Inscriptions',
                        backgroundColor: '#FF7E62',
                        data: values['day_members'].reverse(),
                    },

                    {
                        label: 'Achats',
                        backgroundColor: '#7DD1F0',
                        data: values['day_purchases'].reverse(),
                    },

                    {
                        label: 'Topics',
                        backgroundColor: '#F8BE52',
                        data: values['day_topics'].reverse(),
                    },
                ]
            }

            options = {
                legend: {
                    display: false
                },
            }

            config = {
                type: 'line',
                data: data,
                options: options
            }
            let graph1 = new Chart(ctx, config)

            $(".checkbox").click(function() {

                data.datasets = [];

                if(document.getElementById('visits_checkbox').checked) {
                    data.datasets.push({
                        label: 'Visites',
                        backgroundColor: '#00C292',
                        data: values['day_visits'].reverse(),
                })
                }

                if(document.getElementById('members_checkbox').checked) {
                    data.datasets.push({
                        label: 'Inscriptions',
                        backgroundColor: '#FF7E62',
                        data: ['day_members'].reverse(),
                })
                }

                if(document.getElementById('purchases_checkbox').checked) {
                    data.datasets.push({
                        label: 'Achats',
                        backgroundColor: '#7DD1F0',
                        data: values['day_purchases'].reverse(),
                })
                }

                if(document.getElementById('topics_checkbox').checked) {
                    data.datasets.push({
                        label: 'Topics',
                        backgroundColor: '#F8BE52',
                        data: values['day_topics'].reverse(),
                })
                }

                graph1.update()
            })

            break;

        // STATS PLAYER
        case 'player_stats':

            function getRandomColor() {
                var letters = '0123456789ABCDEF';
                var color = '#';
                for (var i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }

            if(colors === undefined) {
                colors = {};
                for (let game in infos['game_stats']) {
                    colors[game] = getRandomColor();
                }
            }

            ctx = document.getElementById('graph_'+infos['type']).getContext('2d')

            data = {
                datasets: [{
                    data: [],
                    backgroundColor: [],
                }],

                labels: []
            }

            for(let key in infos['game_stats']){
                if(infos['game_stats'][key][infos['type']] !== undefined) {
                    data.datasets[0].data.push(infos['game_stats'][key][infos['type']]);
                    data.datasets[0].backgroundColor.push(colors[key])
                    data.labels.push(key);
                }else {
                    data.datasets[0].data.push(0);
                    data.labels.push(key);
                }
            }

            options = {
                responsive: true,
                legend: {
                    position: 'top'
                },
                tooltips: {
                    callbacks: {

                        label: function(tooltipItem, data) {
                            let index = parseInt(tooltipItem['index']);

                            var label = data['labels'][index];
                            if (label) {
                                label += ': ';
                            }

                            let value = data['datasets'][0]['data'][index];

                            switch (infos['type']){

                                case 'time':
                                    var time=new Date();
                                    time.setTime(value*1000);
                                    label += (time.getHours()-1)+"h "+time.getMinutes()+"m "+time.getSeconds()+"s";
                                    break;

                                default:
                                    label += value+" "+infos['type'];
                                    break;


                            }

                            return label;

                        }
                    }
                },
                plugins: {
                    datalabels: {
                        color: '#fff',
                        anchor: 'end',
                        align: 'start',
                        borderWidth: 2,
                        borderColor: '#fff',
                        backgroundColor: (context) => {
                            return context.dataset.backgroundColor;
                        },
                        font: {
                            weight: 'bold',
                            size: '10',
                        },
                        formatter: (value) => {

                            let label;

                            switch (infos['type']){

                                case 'time':
                                    var time=new Date();
                                    time.setTime(value*1000);
                                    label = (time.getHours()-1)+"h "+time.getMinutes()+"m "+time.getSeconds()+"s";
                                    break;

                                default:
                                    label = value;
                                    break;


                            }

                            return label;
                        }
                    }
                }
            }

            config = {
                type: 'pie',
                data: data,
                options: options
            }
            let graph_ilycoins = new Chart(ctx, config)

            break;

    }

}