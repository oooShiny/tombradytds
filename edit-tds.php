<?php
$bucs_tds = file_get_contents('brady-bucs-tds.json');
$json = json_decode($bucs_tds, true);
 
$tds_by_week = [];
$playoff_weeks =  [
    18 => 'Wildcard',
    19 => 'Divisional',
    20 => 'Conference',
    21 => 'Super Bowl'
];
foreach($json as $td) {
    $tds_by_week[$td['season'] . '.' . sprintf('%02d',$td['week'])][] = $td;
}
ksort($tds_by_week, SORT_NUMERIC);

if (isset($_POST['submitted'])) {

    foreach ($_POST as $key => $value) {
        if ($key !== 'submitted') {
            $key_arr = explode('-', $key);
            $id = $key_arr[1];
            $time = $key_arr[0];
            foreach ($json as $c => $td) {
                if ($td['id'] == $id) {
                    $json[$c][$time] = $value;
                }
            }
        }
    }
    // var_dump($json);
    $newJsonString = json_encode($json);
    file_put_contents('brady-tds.json', $newJsonString);
}
?>

<table>
    <thead>
        <th>Game #</th>
        <th>Season</th>
        <th>Week</th>
        <th>Receiver</th>
        <th>Opponent</th>
        <th>Video</th>

        <th>Down</th>
        <th>Distance</th>
        <th>Yards</th>
        <th>Air Yards</th>
        
    </thead>
    <tbody>
        <form method="post">
            <input type="hidden" name="submitted">
        <?php foreach ($tds_by_week as $week): ?>
            <?php foreach ($week as $td): ?>
                <?php if (empty($td['down'])): ?>
                    <tr>
                        <td><input type="number" id="game-<?php print $td['id']; ?>" name="game-<?php print $td['id']; ?>" value="<?php print $td['game']; ?>"></td>
                        
                        <td><?php print $td['season']; ?></td>
                        <td><?php print $td['week']; ?></td>
                        <td><?php print $td['players_involved']; ?></td>
                        <td><?php print $td['opponent']; ?></td>
                        <td><a href="https://muse.ai/v/<?php print $td['muse_id']; ?>"><?php print $td['muse_id']; ?></a></td>
                        
                        <td><input type="number" id="down-<?php print $td['id']; ?>" name="down-<?php print $td['id']; ?>" value="<?php print $td['down']; ?>"></td>
                        <td><input type="number" id="distance-<?php print $td['id']; ?>" name="distance-<?php print $td['id']; ?>" value="<?php print $td['distance']; ?>"></td>
                        <td><input type="number" id="yards_gained-<?php print $td['id']; ?>" name="yards_gained-<?php print $td['id']; ?>" value="<?php print $td['yards_gained']; ?>"></td>
                        <td><input type="number" id="air_yards-<?php print $td['id']; ?>" name="air_yards-<?php print $td['id']; ?>" value="<?php print $td['air_yards']; ?>"></td>
                    
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <input type="submit">
        </form>
    </tbody>
</table>