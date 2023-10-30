<?php
    $bucs_tds = file_get_contents('brady-bucs-tds.json');
    $pats_tds = file_get_contents('brady-pats-tds.json');
    $bucs_json = json_decode($bucs_tds, true);
    $pats_json = json_decode($pats_tds, true);
    $json = array_merge($bucs_json, $pats_json);
?>
<!doctype html>
<html>
    <head>
        <script defer data-domain="tbtds.com" src="https://plausible.io/js/script.js"></script>
        <meta charset="UTF-8" />
        <link rel="canonical" href="https://tombradytds.com/" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Every Tom Brady TD Pass</title>
        <link rel="shortcut icon" type="image/jpg" href="tbtds-favicon.png"/>
        <!-- Tailwind CSS and Alpine JS -->
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
        <!-- JQuery JS -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <!-- Highcharts JS -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <!-- Magnific Popup CSS & JS -->
        <link rel="stylesheet" href="magnific/magnific-popup.css">
        <script src="magnific/jquery.magnific-popup.min.js"></script>
        <!-- Custom Modal Video JS -->
        <script>
            function gifModal(gif) {
                
                    $.magnificPopup.open({
                        items: {
                            src: 'https://muse.ai/embed/' + gif + '?data-autoplay=1'
                        },
                        disableOn: 700,
                        type: 'iframe',
                        mainClass: 'mfp-fade',
                        removalDelay: 160,
                        preloader: false,
                        fixedContentPos: false
                    });
            }
        </script>
        <style>
            a:hover {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TNJBS47"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <nav class="border-b-2 border-gray-200 p-4 mb-10">
            <div class="container items-center justify-between lg:flex lg:flex-row md:flex-col mx-auto px-6 py-2">
                <div>
                    <a class="text-4xl" href="/">
                    Every Tom Brady TD Pass
                    </a>
                </div>
                <div class="">
                    <ul class="inline-flex flex-wrap">
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="#opponent">Opponent</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="#season">Season</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="#week">Week</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="#distance">Distance</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="#airyards">Air Yards</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/viz.php">Data Viz</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/search.php">Search</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="py-10 flex flex-wrap p-10">
            <p>Tom Brady has thrown <?php print count($json); ?> touchdown passes in his NFL career, more than anyone in the history of the game. Below you will find every single one of them, organized in different ways. Clicking on a list item will show you all the TD passes for that criteria, and clicking on each specific TD link will show the actual play.</p>
        </div>

        <div class="py-10" id="td-scatter"></div>
        <?php
        
            $q_times = [
                '1' => 45,
                '2' => 30,
                '3' => 15,
                '4' => 0,
                '5' => -15
            ];
            $pats_scatter = [];
            foreach ($pats_json as $td) {
                if (($td['seconds'])) {
                    $mins = $q_times[$td['quarter']];
                    $mins += $td['minutes'];
                    $secs = $td['seconds'];
                    $time = ($mins*60) + $secs;
                    if ($td['quarter'] < 5) {
                        $quarter = ordinal($td['quarter']) . ' quarter';
                    } 
                    else {
                        $quarter = 'overtime';
                    }
                    $pats_scatter[] = [
                        'time' => $time,
                        'distance' => $td['yards_gained'],
                        'quarter' => $quarter,
                        'vid' => $td['muse_id'],
                        't_string' => $td['minutes'] . ':' . str_pad($td['seconds'], 2, '0', STR_PAD_LEFT),
                        'title' => $td['title'],
                        'desc' => $td['season'] . ' vs ' . $td['opponent']
                    ];
                }
            }
            $bucs_scatter = [];
            foreach ($bucs_json as $td) {
                if (is_numeric($td['minutes'])) {
                    $mins = $q_times[$td['quarter']];
                    $mins += $td['minutes'];
                    $secs = $td['seconds'] ?? 0;
                    $time = ($mins*60) + $seconds;
                    if ($td['quarter'] < 5) {
                        $quarter = ordinal($td['quarter']) . ' quarter';
                    } 
                    else {
                        $quarter = 'overtime';
                    }
                    $bucs_scatter[] = [
                        'time' => $time,
                        'distance' => $td['yards_gained'],
                        'quarter' => $quarter,
                        'vid' => $td['muse_id'],
                        't_string' => $td['minutes'] . ':' . $td['seconds'],
                        'title' => $td['title']
                    ];
                }
            }
        ?>
        <script>
            Highcharts.chart('td-scatter', {
                chart: {
                    type: 'scatter',
                    zoomType: 'xy'
                },
                title: {
                    text: 'Every Brady TD by time vs distance'
                },
                xAxis: {
                    title: {
                        enabled: true,
                        text: 'Game Time'
                    },
                    labels: {
                        enabled: false
                    },
                    alignTicks: false,
                    tickLength: 0,
                    reversed: true,
                    plotLines: [{
                        color: '#FF0000',
                        width: 2,
                        value: 1800
                    },
                    {
                        color: '#FF0000',
                        width: 2,
                        value: 0
                    }],
                    plotBands: [{
                        color: 'rgb(15 41 82 / 8%)',
                        from: 3600,
                        to: 2700,
                        label: {
                            text: '1st Quarter'
                        }
                    },
                    {
                        color: 'rgb(0 0 0 / 0%)',
                        from: 2700,
                        to: 1800,
                        label: {
                            text: '2nd Quarter'
                        }
                    },
                    {
                        color: 'rgb(15 41 82 / 8%)',
                        from: 1800,
                        to: 900,
                        label: {
                            text: '3rd Quarter'
                        }
                    },
                    {
                        color: 'rgb(0 0 0 / 0%)',
                        from: 900,
                        to: 0,
                        label: {
                            text: '4th Quarter'
                        }
                    },
                    {
                        color: 'rgb(15 41 82 / 8%)',
                        from: 0,
                        to: -900,
                        label: {
                            text: 'Overtime'
                        }
                    },
                ]
                },
                yAxis: {
                    title: {
                        text: 'Distance (yards)'
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    verticalAlign: 'top',
                    x: 100,
                    y: 70,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
                    borderWidth: 1
                },
                plotOptions: {
                    scatter: {
                        marker: {
                            radius: 5,
                            states: {
                                hover: {
                                    enabled: true,
                                    lineColor: 'rgb(100,100,100)'
                                }
                            }
                        },
                        states: {
                            hover: {
                                marker: {
                                    enabled: false
                                }
                            }
                        },
                        tooltip: {
                            headerFormat: '<b>{series.name}</b><br>',
                            pointFormat: '{point.y} yard TD with {point.custom.time} left in {point.custom.q}<br><em>{point.custom.title}</em><br>{point.custom.desc}'
                        }
                    }
                },
                series: [{
                    name: 'Bucs TDs',
                    color: 'rgba(223, 83, 83, .5)',
                    point: {
                        events: {
                            click: function() {

                                gifModal(this.custom.link);
                            }
                        }
                    },
                    data: [<?php
                        foreach ($bucs_scatter as $td) {
                            print '{x:' . $td['time'] . ', y:'. $td['distance'].', custom: {q: "'.$td['quarter'].'", link: "'.$td['vid'].'", time:"'.$td['t_string'].'", title:"'.$td['title'].'", desc:"'.$td['desc'].'"}},';
                        }
                    ?>]

                }, {
                    name: 'Patriots TDs',
                    color: 'rgba(119, 152, 191, .5)',
                    point: {
                        events: {
                            click: function() {
                                gifModal(this.custom.link);
                            }
                        }
                    },
                    data: [<?php
                        foreach ($pats_scatter as $td) {
                            print '{x:' . $td['time'] . ', y:'. $td['distance'].', custom: {q: "'.$td['quarter'].'", link: "'.$td['vid'].'", time:"'.$td['t_string'].'", title:"'.$td['title'].'", desc:"'.$td['desc'].'"}},';
                        }
                    ?>]
                }]
            });

        </script>

        
        <div class="py-10" id="opponent">
            <h1 class="font-light text-3xl text-center">TDs by Opponent</h1>
            <div class="flex flex-wrap p-10">
                <div class="w-screen md:w-3/4">
                    <?php
                        $pats_tds_by_opp = [];
                        $bucs_tds_by_opp = [];
                        foreach($json as $td) {
                            $tds_by_opp[$td['opponent']][] = $td;
                            if ($td['bucs'] !== TRUE) {
                                $pats_tds_by_opp[$td['opponent']][] = $td;
                            } else {
                                $bucs_tds_by_opp[$td['opponent']][] = $td;
                            }
                        } 
                        
                        array_multisort(array_map('count', $tds_by_opp), SORT_DESC, $tds_by_opp);
                    ?>
                    <div id="td-by-opp"></div>
                    <script>
                        Highcharts.chart('td-by-opp', {
                            chart: {
                                type: 'column'
                            },
                            legend: {
                                enabled: false
                            },
                            colors: ['#002244', '#b91c1c'],
                            title: {
                                text: ''
                            },
                            xAxis: {
                                categories: [
                                    <?php foreach($tds_by_opp as $team => $opp_tds): ?>
                                        '<?php print $team; ?>',
                                    <?php endforeach; ?>
                                ],
                                title: {
                                    text: 'Opponent'
                                }
                            },
                            yAxis: {
                                title: 'Brady TDs',
                                stackLabels: {
                                    enabled: true,
                                }
                            },
                            tooltip: {
                                valueSuffix: ' TDs'
                            },
                            plotOptions: {
                                column: {
                                    stacking: 'normal',
                                    dataLabels: {
                                        enabled: false
                                    }
                                }
                            },
                            series: [
                                { 
                                name: 'Brady TDs (Patriots)',
                                data: [
                                <?php foreach($tds_by_opp as $team => $opp_tds): ?>
                                    <?php print count($pats_tds_by_opp[$team]); ?>,
                                <?php endforeach; ?>
                                ]},
                                { 
                                name: 'Brady TDs (Bucs)',
                                data: [
                                <?php foreach($tds_by_opp as $team => $opp_tds): ?>
                                    <?php if (isset($bucs_tds_by_opp[$team])) { 
                                        print count($bucs_tds_by_opp[$team]); 
                                    } else {
                                        print '0';
                                    }?>,
                                <?php endforeach; ?>
                                ]} 
                            ]
                            });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_opp as $team => $opp_tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3"><?php print $team; ?> (<?php print count($tds_by_opp[$team]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($opp_tds as $opp_td): ?>
                                    <li><a onclick="gifModal('<?php print $opp_td['muse_id']; ?>')"><?php print $opp_td['title']; ?> (<?php print $opp_td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>


        <div class="py-10 bg-gray-200" id="season">
            <h1 class="font-light text-3xl text-center">TDs by Season</h1>
            <div class="flex flex-wrap p-10">
                <div class="w-screen md:w-3/4">
                    <?php
                        $tds_by_season = [];
                        foreach($json as $td) {
                            $tds_by_season[$td['season']][] = $td;
                        }
                        ksort($tds_by_season);
                        $season_avg = count($json) / count($tds_by_season);
                    ?>
                    <div id="td-by-season"></div>
                    <script>
                        Highcharts.chart('td-by-season', {
                            chart: {
                                type: 'line',
                                backgroundColor: '#e5e7eb'
                            },
                            legend: {
                                enabled: false
                            },
                            title: {
                                text: ''
                            },
                            xAxis: {
                                categories: [
                                    <?php foreach($tds_by_season as $season => $season_td): ?>
                                        '<?php print $season; ?>',
                                    <?php endforeach; ?>
                                ],
                                title: {
                                    text: 'Season'
                                }
                            },
                            yAxis: {
                                title: 'Brady TDs',
                                plotLines: [{
                                    color: 'gray',
                                    value: <?php print $season_avg; ?>, 
                                    width: '1',
                                    zIndex: 4,
                                    dashStyle: 'Dot',
                                    label: { 
                                    text: 'Average: <?php print $season_avg; ?>', 
                                    align: 'left', 
                                }
                                }]
                            },
                            tooltip: {
                                valueSuffix: ' TDs'
                            },
                            plotOptions: {
                                column: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            },
                            series: [
                                { 
                                    name: 'Brady TDs',
                                    marker: {
                                        symbol: 'circle'
                                    },
                                    data: [
                                        <?php foreach ($tds_by_season as $season => $season_td) {
                                                print count($tds_by_season[$season]) . ','; 
                                        }?>
                                    ],
                                    zoneAxis: 'x',
                                    zones: [
                                        {
                                            value: 18,
                                            color: '#002244'
                                        },
                                        {
                                            color: '#b91c1c'
                                        }                                        
                                    ]
                                }
                            ]
                            });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_season as $season => $season_tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3"><?php print $season; ?> (<?php print count($tds_by_season[$season]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($season_tds as $season_td): ?>
                                    <li><a onclick="gifModal('<?php print $season_td['muse_id']; ?>')"><?php print $season_td['title']; ?> (<?php print $season_td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>



        <div class="py-10" id="week">
            <h1 class="font-light text-3xl text-center">TDs by Week</h1>
            <?php
                $tds_by_week = [];
                $playoff_weeks =  [
                    19 => 'Wildcard',
                    20 => 'Divisional',
                    21 => 'Conference',
                    22 => 'Super Bowl'
                ];
                foreach($json as $td) {
                    $tds_by_week[$td['week']][] = $td;
                }
                ksort($tds_by_week);
                $playoff_tds = 0;
                foreach ($playoff_weeks as $w => $name) {
                    $playoff_tds += count($tds_by_week[$w]);
                }
            ?>
            <div class="flex flex-wrap p-10">
                <p class="pb-10">
                    Tom Brady has thrown <?php print $playoff_tds;?> playoff TDs, 
                    the most in NFL history. For comparison, that's more TDs than 
                    Josh Freeman (81), Joey Harrington (79) or Jay Fiedler (72) 
                    threw in their entire career.
                </p>
                <div class="w-screen md:w-3/4">
                    
                    <div id="td-by-week"></div>
                    <script>
                        Highcharts.chart('td-by-week', {
                            chart: {
                                type: 'area'
                            },
                            legend: {
                                enabled: false
                            },
                            title: {
                                text: ''
                            },
                            xAxis: {
                                categories: [
                                    <?php foreach($tds_by_week as $week => $week_td): ?>
                                        <?php if ($week < 19): ?>
                                            'Week <?php print $week; ?>',
                                        <?php else: ?>
                                            '<?php print $playoff_weeks[$week]; ?>',
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                ],
                                title: {
                                    text: 'Week'
                                }
                            },
                            yAxis: {
                                title: 'Brady TDs'
                            },
                            tooltip: {
                                valueSuffix: ' TDs'
                            },
                            plotOptions: {
                                column: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            },
                            series: [
                                { 
                                    name: 'Brady TDs',
                                    marker: {
                                        symbol: 'circle'
                                    },
                                    data: [
                                        <?php foreach ($tds_by_week as $week => $week_td) {
                                                print count($tds_by_week[$week]) . ','; 
                                        }?>
                                    ],
                                    zoneAxis: 'x',
                                    zones: [
                                        {
                                            value: 18,
                                            color: '#002244'
                                        },
                                        {
                                            color: '#b91c1c'
                                        }                                        
                                    ]
                                }
                            ]
                            });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_week as $week => $week_tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3">
                                        <?php if ($week < 19): ?>
                                            Week <?php print $week; ?>
                                        <?php else: ?>
                                            <?php print $playoff_weeks[$week]; ?>
                                        <?php endif; ?>
                                        (<?php print count($tds_by_week[$week]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($week_tds as $week_td): ?>
                                    <li><a onclick="gifModal('<?php print $week_td['muse_id']; ?>')"><?php print $week_td['title']; ?> (<?php print $week_td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>    

        <div class="py-10 bg-gray-200" id="player">
            <h1 class="font-light text-3xl text-center">TDs by Receiver</h1>
            <div class="flex flex-wrap p-10">
            <?php
                        $pats_tds_by_player = [];
                        $bucs_tds_by_player = [];
                        foreach($json as $td) {
                            $tds_by_player[$td['players_involved']][] = $td;
                            if ($td['bucs'] !== TRUE) {
                                $pats_tds_by_player[$td['players_involved']][] = $td;
                            }else {
                                $bucs_tds_by_player[$td['players_involved']][] = $td;
                            }
                        } 
                        array_multisort(array_map('count', $tds_by_player), SORT_DESC, $tds_by_player);
                    ?>
            <p class="pb-10">
                Tom Brady has connected with Rob Gronkowski for <?php print count($tds_by_player['Rob Gronkowski']); ?> touchdowns, 
                second only behind Peyton Manning and Marvin Harrison (114 TDs). 
                And then he's also thrown TDs to <?php print count($tds_by_player) - 1; ?> other players.
            </p>
                <div class="w-screen md:w-3/4">
                    
                    <div id="td-by-player"></div>
                    <script>
                        Highcharts.chart('td-by-player', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie',
                            backgroundColor: '#e5e7eb'
                        },
                        title: {
                            text: ''
                        },
                        tooltip: {
                            pointFormat: '<b>{point.y} TDs</b> ({point.percentage:.1f} %)'
                        },
                        plotOptions: {
                            pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y} TDs ({point.percentage:.1f} %)'
                            }
                            }
                        },
                        series: [{
                            name: 'Players',
                            colorByPoint: true,
                            data: [
                                <?php $ones = 0; $twos = 0; $threes = 0; $fours = 0; $fives = 0;
                                    foreach ($tds_by_player as $player => $tds) {
                                        if (count($tds) > 5) { 
                                        print '{';
                                        print 'name: "' . $player . '",';
                                        print 'y: ' . count($tds) . ',';
                                        print '},';
                                        } else {
                                            switch (count($tds)) {
                                                case 1:
                                                    $ones++;
                                                    break;
                                                case 2:
                                                    $twos++;
                                                    break;
                                                case 3:
                                                    $threes++;
                                                    break;
                                                case 4:
                                                    $fours++;
                                                    break;
                                                case 5:
                                                    $fives++;
                                                    break;
                                        }
                                    }
                                }?>
                                {
                                    name: "<?php print $fives; ?> Players with 5 TDs",
                                    y: <?php print $fives * 5; ?>,
                                },
                                {
                                    name: "<?php print $fours; ?> Players with 4 TDs",
                                    y: <?php print $fours * 4; ?>,
                                },
                                {
                                    name: "<?php print $threes; ?> Players with 3 TDs",
                                    y: <?php print $threes * 3; ?>,
                                },
                                {
                                    name: "<?php print $twos; ?> Players with 2 TDs",
                                    y: <?php print $twos * 2; ?>,
                                },
                                {
                                    name: "<?php print $ones; ?> Players with 1 TD",
                                    y: <?php print $ones; ?>,
                                },
                                ]
                            }]
                        });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_player as $player => $tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3"><?php print $player; ?> (<?php print count($tds_by_player[$player]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($tds as $td): ?>
                                    <li><a onclick="gifModal('<?php print $td['muse_id']; ?>')"><?php print $td['title']; ?> (<?php print $td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="py-10" id="distance">
            <h1 class="font-light text-3xl text-center">TDs by Distance</h1>
            <?php
                        $pats_tds_by_dist = [];
                        $bucs_tds_by_dist = [];
                        $total_distance = 0;
                        foreach($json as $td) {
                            $tds_by_dist[$td['yards_gained']][] = $td;
                            $total_distance += $td['yards_gained'];
                            if ($td['bucs'] !== TRUE) {
                                $pats_tds_by_dist[$td['yards_gained']][] = $td;
                            }else {
                                $bucs_tds_by_dist[$td['yards_gained']][] = $td;
                            }
                        } 
                        
                        ksort($tds_by_dist);
                    ?>
            <div class="flex flex-wrap p-10">
            <p class="pb-10">
                If you only count yards gained on passes that went for TDs, Tom
                Brady has accumulated <?php print number_format($total_distance); ?> yards.
                That's more passing yards than Rex Grossman (11,015) threw in his 
                entire career. 
            </p>
                <div class="w-screen md:w-3/4">
                    
                    <div id="td-by-dist"></div>
                    <script>
                        Highcharts.chart('td-by-dist', {
                            chart: {
                                type: 'column',
                                backgroundColor: '#fff'
                            },
                            legend: {
                                enabled: false
                            },
                            colors: ['#002244', '#b91c1c'],
                            title: {
                                text: ''
                            },
                            xAxis: {
                                categories: [
                                    <?php foreach($tds_by_dist as $distance => $tds): ?>
                                        '<?php print $distance; ?>',
                                    <?php endforeach; ?>
                                ],
                                title: {
                                    text: 'Distance'
                                }
                            },
                            yAxis: {
                                title: 'Brady TDs',
                                stackLabels: {
                                    enabled: true,
                                }
                            },
                            tooltip: {
                                valueSuffix: ' TDs'
                            },
                            plotOptions: {
                                column: {
                                    stacking: 'normal',
                                    dataLabels: {
                                        enabled: false
                                    }
                                }
                            },
                            series: [
                                { 
                                name: 'Brady TDs (Patriots)',
                                data: [
                                <?php foreach($tds_by_dist as $distance => $tds): ?>
                                    <?php print count($pats_tds_by_dist[$distance]); ?>,
                                <?php endforeach; ?>
                                ]},
                                { 
                                name: 'Brady TDs (Bucs)',
                                data: [
                                <?php foreach($tds_by_dist as $distance => $tds): ?>
                                    <?php if (isset($bucs_tds_by_dist[$distance])) { 
                                        print count($bucs_tds_by_dist[$distance]); 
                                    } else {
                                        print '0';
                                    }?>,
                                <?php endforeach; ?>
                                ]} 
                            ]
                            });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_dist as $distance => $tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3"><?php print $distance; ?> yards (<?php print count($tds_by_dist[$distance]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($tds as $td): ?>
                                    <li><a onclick="gifModal('<?php print $td['muse_id']; ?>')"><?php print $td['title']; ?> (<?php print $td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>


        <div class="py-10 bg-gray-200" id="airyards">
            <h1 class="font-light text-3xl text-center">TDs by Air Yards</h1>
            <div class="flex flex-wrap p-10">
            <p class="pb-10">
                Air yards are determined by how far the ball traveled in the air 
                relative to the line of scrimmage, so negative air yards are passes 
                behind the line of scrimmage (screen plays, for example).
            </p>
                <div class="w-screen md:w-3/4">
                    <?php
                        $pats_tds_by_air_yds = [];
                        $bucs_tds_by_air_yds = [];
                        foreach($json as $td) {
                            $tds_by_air_yds[$td['air_yards']][] = $td;
                            if ($td['bucs'] !== TRUE) {
                                $pats_tds_by_air_yds[$td['air_yards']][] = $td;
                            }else {
                                $bucs_tds_by_air_yds[$td['air_yards']][] = $td;
                            }
                        } 
                        
                        ksort($tds_by_air_yds);
                    ?>
                    <div id="td-by-air-yds"></div>
                    <script>
                        Highcharts.chart('td-by-air-yds', {
                            chart: {
                                type: 'column',
                                backgroundColor: '#e5e7eb'
                            },
                            legend: {
                                enabled: false
                            },
                            colors: ['#002244', '#b91c1c'],
                            title: {
                                text: ''
                            },
                            xAxis: {
                                categories: [
                                    <?php foreach($tds_by_air_yds as $distance => $tds): ?>
                                        '<?php print $distance; ?>',
                                    <?php endforeach; ?>
                                ],
                                title: {
                                    text: 'Air Yards'
                                }
                            },
                            yAxis: {
                                title: 'Brady TDs',
                                stackLabels: {
                                    enabled: true,
                                }
                            },
                            tooltip: {
                                valueSuffix: ' TDs'
                            },
                            plotOptions: {
                                column: {
                                    stacking: 'normal',
                                    dataLabels: {
                                        enabled: false
                                    }
                                }
                            },
                            series: [
                                { 
                                name: 'Brady TDs (Patriots)',
                                data: [
                                <?php foreach($tds_by_air_yds as $distance => $tds): ?>
                                    <?php print count($pats_tds_by_air_yds[$distance]); ?>,
                                <?php endforeach; ?>
                                ]},
                                { 
                                name: 'Brady TDs (Bucs)',
                                data: [
                                <?php foreach($tds_by_air_yds as $distance => $tds): ?>
                                    <?php if (isset($bucs_tds_by_air_yds[$distance])) { 
                                        print count($bucs_tds_by_air_yds[$distance]); 
                                    } else {
                                        print '0';
                                    }?>,
                                <?php endforeach; ?>
                                ]} 
                            ]
                            });
                    </script>
                </div>
                <div class="w-screen md:w-1/4">
                    <ul class="block w-11/12 my-4 mx-auto h-96 overflow-scroll" x-data="{selected:null}">
                        <?php $c = 0; ?>
                        <?php foreach($tds_by_air_yds as $distance => $tds): ?>
                            <li class="flex align-center flex-col my-0.5">
                                <h4 @click="selected !== <?php print $c; ?> ? selected = <?php print $c; ?> : selected = null"
                                    class="cursor-pointer px-5 py-3 bg-gray-700 text-white text-center inline-block hover:opacity-75 hover:shadow hover:-mb-3"><?php print $distance; ?> yards (<?php print count($tds_by_air_yds[$distance]); ?>)</h4>
                                <ol x-show="selected == <?php print $c; ?>" class="border py-4 pl-10 list-decimal">
                                    <?php foreach($tds as $td): ?>
                                    <li><a onclick="gifModal('<?php print $td['muse_id']; ?>')"><?php print $td['title']; ?> (<?php print $td['season']; ?>)</a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </li>
                            <?php $c++; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>

<?php function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}
?>