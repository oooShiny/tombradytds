<?php
    $bucs_tds = file_get_contents('brady-bucs-tds.json');
    $pats_tds = file_get_contents('brady-pats-tds.json');
    $bucs_json = json_decode($bucs_tds, true);
    $pats_json = json_decode($pats_tds, true);
    $json = array_merge($bucs_json, $pats_json);

    usort($json, function($b, $a) {
        $retval = $a['season'] <=> $b['season'];
        if ($retval == 0) {
            $retval = $a['week'] <=> $b['week'];
        }
        return $retval;
    });

    $players = [];
    foreach($json as $td) {
        $players[$td['players_involved']][] = $td;
        $teams[$td['opponent']] = $td['opponent'];
        $seasons[$td['season']] = $td['season'];
    } 
    array_multisort(array_map('count', $players), SORT_DESC, $players);
    ksort($teams);
    ksort($seasons);
?>
<!doctype html>
<html>
    <head>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TNJBS47');</script>
        <!-- End Google Tag Manager -->
        <meta charset="UTF-8" />
        <link rel="canonical" href="https://tombradytds.com/search.php" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Every Tom Brady TD Pass</title>
        <link rel="shortcut icon" type="image/jpg" href="tbtds-favicon.png"/>
        <!-- Tailwind CSS and Alpine JS -->
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
        <!-- JQuery JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
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
                jQuery(function ($) {
                var modalSrc = '';
                var url = 'https://api.gfycat.com/v1/gfycats/';
                $.get( url+gif, function( data ) {
                    videoSrc = "<video controls muted autoplay preload='metadata' class='responsive-video'>" +
                    "<source src='" + data.gfyItem.mp4Url + "' type='video/mp4; codecs=' avc1.42e01e, mp4a.40.2''>" +
                    "<source src='" + data.gfyItem.webmUrl + "' type='video/webm; codecs=' vp8, vorbis''>" +
                    "</video>";
                    $.magnificPopup.open({
                        items: {
                            src: data.gfyItem.mp4Url
                        },
                        type: 'iframe'
                    });
                }).fail(function() {
                    var url = 'https://api.redgifs.com/v1/gfycats/'
                    $.get( url+gifID, function( data ) {
                        videoSrc = "<video controls muted autoplay preload='metadata' class='responsive-video'>" +
                        "<source src='" + data.gfyItem.mp4Url + "' type='video/mp4; codecs=' avc1.42e01e, mp4a.40.2''>" +
                        "<source src='" + data.gfyItem.webmUrl + "' type='video/webm; codecs=' vp8, vorbis''>" +
                        "</video>";
                        $('.opp-video').html(videoSrc);
                    })
                });
                });
            }
        </script>
        <!-- SortTable JS -->
        <script src="sorttable.js"></script>
        <style>
            a:hover {
                cursor: pointer;
            }
            table.sortable thead {
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
                    Tom Brady TD Search
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
            <form method="get">
                Show all Tom Brady TDs thrown to 
                <select name="player" id="player" class="border-b-2 border-black">
                    <option value="all">[any receiver]</option>
                    <?php 
                    foreach ($players as $player => $plays) {
                        if (isset($_GET['player']) && $_GET['player'] == $player) {
                            print "<option value='".$player."' selected>".$player."</option>";
                        }
                        else {
                            print "<option value='".$player."'>".$player."</option>";
                        }
                    } 
                    ?>
                </select>
                against the
                <select name="team" id="team" class="border-b-2 border-black">
                    <option value="all">[any team]</option>
                    <?php 
                    foreach ($teams as $team) {
                        if (isset($_GET['team']) && $_GET['team'] == $team) {
                            print "<option value='".$team."' selected>".$team."</option>";
                        }
                        else {
                            print "<option value='".$team."'>".$team."</option>";
                        }
                    } 
                    ?>
                </select>
                from 
                <select name="start" id="start" class="border-b-2 border-black">
                    <option value="all">[any season]</option>
                    <?php 
                    foreach ($seasons as $season) {
                        if (isset($_GET['start']) && $_GET['start'] == $season) {
                            print "<option value='".$season."' selected>".$season."</option>";
                        }
                        else {
                            print "<option value='".$season."'>".$season."</option>";
                        }
                    } 
                    ?>
                </select>
                to 
                <select name="end" id="end" class="border-b-2 border-black">
                    <option value="all">[any season]</option>
                    <?php 
                    foreach ($seasons as $season) {
                        if (isset($_GET['end']) && $_GET['end'] == $season) {
                            print "<option value='".$season."' selected>".$season."</option>";
                        }
                        else {
                            print "<option value='".$season."'>".$season."</option>";
                        }
                    } 
                    ?>
                </select>
                <input class="p-4 hover:bg-red-700 hover:text-white" type="submit" value="Search"> 
                <a class="p-4 hover:bg-red-700 hover:text-white" href="/search.php">Reset</a>
            </form>
        </div>
        <?php if (isset($_GET['player'])): ?>
            <table class="m-10 sortable table-auto w-11/12">
                <thead class="text-left">
                    <tr><th></th><th>Season</th><th>Week</th><th>Opponent</th><th>Yards</th><th>Play</th></tr>
                </thead>
                <tbody>
                    <?php
                        $count = 1;
                        foreach ($json as $td) {
                            if ($_GET['player'] == 'all' || $td['players_involved'] == $_GET['player']) {
                                if ($_GET['team'] == 'all' || $td['opponent'] == $_GET['team']) {
                                    if ($_GET['start'] == 'all' || $td['season'] >= $_GET['start']) {
                                        if ($_GET['end'] == 'all' || $td['season'] <= $_GET['end']) {
                                        ?>
                                        <tr>
                                            <td><?php print $count; $count++; ?></td>
                                            <td><?php print $td['season']; ?></td>
                                            <td><?php print $td['week']; ?></td>
                                            <td><?php print $td['opponent']; ?></td>
                                            <td><?php print $td['yards_gained']; ?></td>
                                            <td><a class="text-red-700 underline" onclick="gifModal('<?php print $td['gfycat_id']; ?>')"><?php print $td['title']; ?></a></td>
                                        </tr>
                                        <?php
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </body>
</html>