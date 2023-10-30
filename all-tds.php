<?php
$bucs_tds = file_get_contents('brady-bucs-tds.json');
$pats_tds = file_get_contents('brady-pats-tds.json');
$bucs_json = json_decode($bucs_tds, true);
$pats_json = json_decode($pats_tds, true);
$json = array_merge($bucs_json, $pats_json);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($json);