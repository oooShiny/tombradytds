<html>
    <head>
        <link href="viz.css" rel="stylesheet">
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
        <!-- JQuery JS -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <!-- Magnific Popup CSS & JS -->
        <link rel="stylesheet" href="magnific/magnific-popup.css">
        <script src="magnific/jquery.magnific-popup.min.js"></script>

    </head>
    <body>
    <nav class="border-b-2 border-gray-200 p-4 mb-10">
            <div class="container items-center justify-between lg:flex lg:flex-row md:flex-col mx-auto px-6 py-2">
                <div>
                    <a class="text-4xl" href="/">
                    Every Tom Brady TD Pass
                    </a>
                </div>
                <div class="">
                    <ul class="inline-flex flex-wrap">
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/index.php#opponent">Opponent</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/index.php#season">Season</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/index.php#week">Week</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/index.php#distance">Distance</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/index.php#airyards">Air Yards</a></li>
                        <li><a class="p-4 hover:bg-red-700 hover:text-white" href="/viz.php">Data Viz</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="viz">
            <div class="flex justify-evenly my-4">

                <div class="my-1">
                <h3 class="uppercase font-light text-xl">Quarter</h3>
                <button onclick="filterTDs('q-1')" class="p-4 bg-white hover:bg-red-200 text-red-pats text-center">1Q</button>
                <button onclick="filterTDs('q-2')" class="p-4 bg-white hover:bg-red-200 text-red-pats text-center">2Q</button>
                <button onclick="filterTDs('q-3')" class="p-4 bg-white hover:bg-red-200 text-red-pats text-center">3Q</button>
                <button onclick="filterTDs('q-4')" class="p-4 bg-white hover:bg-red-200 text-red-pats text-center">4Q</button>
                <button onclick="filterTDs('q-5')" class="p-4 bg-white hover:bg-red-200 text-red-pats text-center">OT</button>
                </div>

                <div class="my-1">
                <h3 class="uppercase font-light text-xl">Reg/Post Season</h3>
                <button onclick="filterTDs('reg-season')" class="p-4 bg-blue-pats hover:bg-blue-800 text-white text-center">Reg Season</button>
                <button onclick="filterTDs('playoffs')" class="p-4 bg-red-pats hover:bg-red-800 text-white text-center">Playoffs</button>
                <button onclick="filterTDs('super-bowl')" class="p-4 bg-yellow-500 hover:bg-yellow-200 text-black text-center">Super Bowl</button>
                </div>

                <div class="my-1">
                <h3 class="uppercase font-light text-xl">Opponent</h3>
                    <select id="opp_filter">
                    <option value="play">-- Select a Team --</option>
                    <option value='49ers'>49ers</option>
                    <option value='bears'>Bears</option>
                    <option value='bengals'>Bengals</option>
                    <option value='bills'>Bills</option>
                    <option value='broncos'>Broncos</option>
                    <option value='browns'>Browns</option>
                    <option value='buccaneers'>Buccaneers</option>
                    <option value='cardinals'>Cardinals</option>
                    <option value='chargers'>Chargers</option>
                    <option value='chiefs'>Chiefs</option>
                    <option value='colts'>Colts</option>
                    <option value='cowboys'>Cowboys</option>
                    <option value='dolphins'>Dolphins</option>
                    <option value='eagles'>Eagles</option>
                    <option value='falcons'>Falcons</option>
                    <option value='giants'>Giants</option>
                    <option value='jaguars'>Jaguars</option>
                    <option value='jets'>Jets</option>
                    <option value='lions'>Lions</option>
                    <option value='packers'>Packers</option>
                    <option value='panthers'>Panthers</option>
                    <option value='raiders'>Raiders</option>
                    <option value='rams'>Rams</option>
                    <option value='ravens'>Ravens</option>
                    <option value='redskins'>Redskins</option>
                    <option value='saints'>Saints</option>
                    <option value='seahawks'>Seahawks</option>
                    <option value='steelers'>Steelers</option>
                    <option value='texans'>Texans</option>
                    <option value='titans'>Titans</option>
                    <option value='vikings'>Vikings</option>
                </select>
                </div>
                <?php
                    $bucs_tds = file_get_contents('brady-bucs-tds.json');
                    $pats_tds = file_get_contents('brady-pats-tds.json');
                    $bucs_json = json_decode($bucs_tds, true);
                    $pats_json = json_decode($pats_tds, true);
                    $all_tds = array_merge($bucs_json, $pats_json);
                    $wrs = [];
                    foreach ($all_tds as $td) {
                        $wr = strtolower(str_replace(' ', '-', $td['players_involved']));
                        $wrs[$wr] = $td['players_involved'];
                    }
                    ksort($wrs);
                ?>
                <div class="my-1">
                <h3 class="uppercase font-light text-xl">Receiver</h3>
                    <select id="wr_filter">
                        <option value="play">-- Select a Player --</option>
                        <?php foreach ($wrs as $opt => $wr): ?>
                            <option value="<?php print $opt; ?>"><?php print $wr; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="my-1">
                    <button onclick="clearFilters()" class=" p-4 bg-red-pats hover:bg-red-800 text-white text-center">Reset Filters</button>
                </div>


            </div>

            <script src="https://d3js.org/d3.v4.min.js"></script>
            <div id="field" ></div>
            <script>
                var width = '100%';
                var height = '100%';
                var widthpx = 3600;
                var heightpx = 1600;
                var yellow = 'yellow';
                var blue = '#002244';
                var red = '#c60c30';    
                
                var data = d3.json('/all-tds.php', function(error, data) {
                // Create SVG element field
                var field = d3.select("div#field")
                    .append("svg")
                    .attr("preserveAspectRatio", "xMinYMin meet")
                    .attr("viewBox", function () {
                    return '0 0 ' + heightpx + ' ' + widthpx;
                    })
                    .attr('class', 'field-svg');

                // Create top end zone
                field.append("rect")
                    .attr("x", 0)
                    .attr("y", 0)
                    .attr("width", width)
                    .attr("height", 300)
                    .attr('fill', 'rgba(255, 255, 255, 0.5)')
                    .attr('stroke', 'white')
                    .attr('stroke-width', '5');

                // Create bottom end zone
                field.append("rect")
                    .attr("x", 0)
                    .attr("y", 3300)
                    .attr("width", width)
                    .attr("height", 300)
                    .attr('fill', 'rgba(255, 255, 255, 0.5)')
                    .attr('stroke', 'white')
                    .attr('stroke-width', '5');

                // Create yard lines
                field.each(function(d){
                    var line = d3.select(this)
                    .attr('fill', 'rgba(255, 255, 255, 0.5)')
                    .attr('stroke', 'white')
                    .attr('stroke-width', '2.5');
                    for (var i = 1; i < 100; i++) {
                    line.append('line')
                        .attr('x1', 575)
                        .attr('y1', i*30 + 300)
                        .attr('x2', 625)
                        .attr('y2', i*30 + 300);
                    line.append('line')
                        .attr('x1', 975)
                        .attr('y1', i*30 + 300)
                        .attr('x2', 1025)
                        .attr('y2', i*30 + 300);
                    if (i % 5 === 0) {
                        line.append('line')
                        .attr('x1', 0)
                        .attr('y1', i*30 + 300)
                        .attr('x2', width)
                        .attr('y2', i*30 + 300);
                        if (i % 2 === 0) {
                        line.append("text")
                            .attr("x", 10)
                            .attr("y", i*30 + 300)
                            .attr("transform", function (d) {
                            if (i < 50) {
                                var y = i * 30 + 300;
                                return "rotate(90,90," + y + ")";
                            }
                            else  if (i > 50) {
                                var y = i * 30 + 320;
                                return "rotate(90,70," + y + ")";
                            }
                            else {
                                var y = i * 30 + 320;
                                return "rotate(90,70," + y + ")";
                            }
                            })
                            .attr('font-size', '5em')
                            .text(function(d) {
                            if (i < 50) {
                                return '<' + i;
                            }
                            else if (i > 50) {
                                return 100 - i + '>';
                            }
                            else {
                                return i;
                            }
                            });
                        }
                    }
                    }
                });



                // Add the passing data
                var pass = field.selectAll('g')
                    .data(data)
                    .enter()
                    .append('g');

                // Add metadata classes for filtering
                pass.attr('class', function (d) {
                    var team = d.opponent.replace(' ', '-').replace(' ', '-').toLowerCase();
                    var wr = d.players_involved.replace(' ', '-').replace(' ', '-').toLowerCase();
                    if (d.week == 22) {
                    var gameweek = 'super-bowl';
                    }
                    else if (d.week > 18) {
                    var gameweek = 'playoffs';
                    }
                    else {
                    var gameweek = 'reg-season'
                    }
                    var classes = 'play q-' + d.quarter + ' ' + d.season + ' ' + gameweek + ' ' + team + ' ' + wr;
                    return classes;
                });
                pass.attr('id', function (d) {
                        return d.id;
                    })

                pass.append('a')
                    .attr('onclick', function (d) {
                    return 'gifModal(\'' + d.muse_id + '\')';
                    })
                    .append('line')
                    .attr("x2", function(d, i){
                    if (d.pass_location === "Left Sideline") {
                        return 100;
                    }
                    else if (d.pass_location === "Left Outside Numbers") {
                        return 300;
                    }
                    else if (d.pass_location === "Left Numbers") {
                        return 400;
                    }
                    else if (d.pass_location === "Left") {
                        return 500;
                    }
                    else if (d.pass_location === "Middle") {
                        return 800;
                    }
                    else if (d.pass_location === "Right") {
                        return 1100;
                    }
                    else if (d.pass_location === "Right Numbers") {
                        return 1200;
                    }
                    else if (d.pass_location === "Right Outside Numbers") {
                        return 1300;
                    }
                    else {
                        return 1500;
                    }
                    })
                    .attr("y2", function(d, i){
                        var yards = d.yards_gained - d.air_yards
                        return (yards * 30) + 300;
                    })
                    .attr("x1", function(d, i){
                    if (d.pass_thrown_from === "Left Sideline") {
                        return 400;
                    }
                    else if (d.pass_thrown_from === "Left Hash") {
                        return 600;
                    }
                    else if (d.pass_thrown_from === "Between Hashes (left)") {
                        return 725;
                    }
                    else if (d.pass_thrown_from === "Between Hashes") {
                        return 800;
                    }
                    else if (d.pass_thrown_from === "Between Hashes (right)") {
                        return 925;
                    }
                    else if (d.pass_thrown_from === "Right Hash") {
                        return 1000;
                    }
                    else if (d.pass_thrown_from === "Right Sideline") {
                        return 1200;
                    }
                    })
                    .attr("y1", function(d, i){
                    var yards = d.yards_gained
                    return (yards * 30 + 300);
                    })
                    .attr("stroke", function(d, i){
                    if (d.week == 22) {
                        return yellow;
                    }
                    else if (d.week > 18) {
                        return red;
                    }
                    else {
                        return blue;
                    }
                    })
                    .attr('stroke-width', '5')
                    
                });
            </script>
            <script>
                document.getElementById('opp_filter').addEventListener('change', function() {
                    filterTDs(this.value);
                });

                document.getElementById('wr_filter').addEventListener('change', function() {
                    filterTDs(this.value);
                });

                function filterTDs(c) {
                    jQuery(function($) {
                        $('g').hide();
                        $('g.'+c).show();
                    });
                }
                function clearFilters() {
                    jQuery(function($) {
                        $('g').show();
                    });
                }

                function gifModal(gif) {

                    $.magnificPopup.open({
                        items: {
                            src: 'https://muse.ai/embed/' + gif
                        },
                        type: 'iframe',
                        alignTop: true,
                        mainClass: 'mfp-fade',
                        removalDelay: 160,
                        preloader: false,
                        fixedContentPos: false
                    });
                }
            </script>
        </div>
    </body>
</html>
