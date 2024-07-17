<?php

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

error_log(print_r($data, true));

echo json_encode([
    'status' => 'success',
    'data' => $data,
]);
?>