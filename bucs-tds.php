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
    file_put_contents('brady-bucs-tds.json', $newJsonString);
}
?>

<table>
    <thead>
        <th>Season</th>
        <th>Week</th>
        <th>Receiver</th>
        <th>Opponent</th>
        <th>Minutes</th>
        <th>Seconds</th>
        <th>Video ID</th>
    </thead>
    <tbody>
        <form method="post">
            <input type="hidden" name="submitted">
        <?php foreach ($tds_by_week as $week): ?>
            <?php foreach ($week as $td): ?>
                <?php if (empty($td['muse_id'])): ?>
                    <tr>
                    <td><?php print $td['season']; ?></td>
                    <td><?php print $td['week']; ?></td>

                    <td><?php print $td['players_involved']; ?></td>
                    <td><?php print $td['opponent']; ?></td>

                    <td><input type="number" id="minutes-<?php print $td['id']; ?>" name="minutes-<?php print $td['id']; ?>" value="<?php print $td['minutes']; ?>"></td>
                    <td><input type="number" id="seconds-<?php print $td['id']; ?>" name="seconds-<?php print $td['id']; ?>" value="<?php print $td['seconds']; ?>"></td>
                    <td><input type="text" id="muse_id-<?php print $td['id']; ?>" name="muse_id-<?php print $td['id']; ?>" value="<?php print $td['muse_id']; ?>"></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <input type="submit">
        </form>
    </tbody>
</table>