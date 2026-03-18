<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$file = 'brady-tds.json';
$plays = json_decode(file_get_contents($file), true);

foreach ($plays as &$play) {
    if ($play['id'] == $input['id']) {
        $play['loc_nudge']        = (int) $input['loc_nudge'];
        $play['throw_nudge']      = (int) $input['throw_nudge'];
        $play['pass_type']        = $input['pass_type'];
        $play['pass_location']    = $input['pass_location'];
        $play['pass_thrown_from'] = $input['pass_thrown_from'];
        $play['throw_type']       = $input['throw_type'];
        $play['air_yards']        = $input['air_yards'];
        $play['down']             = $input['down'];
        $play['distance']         = $input['distance'];
        $play['title']            = $input['title'];
        $play['muse_id']          = $input['muse_id'];
        break;
    }
}

file_put_contents($file, json_encode($plays, JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);
