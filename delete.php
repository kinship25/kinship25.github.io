<?php
header("Content-Type: application/json");

$dataFile = "uploads/data.json";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $service = $input['service'] ?? '';
    $index   = $input['index'] ?? -1;

    if ($service !== '' && $index > -1 && file_exists($dataFile)) {
        $data = json_decode(file_get_contents($dataFile), true);

        if (isset($data[$service][$index])) {
            $filePath = $data[$service][$index]['src'];

            // Delete file from server
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Remove from array
            array_splice($data[$service], $index, 1);

            file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
            echo json_encode(["success" => true]);
            exit;
        }
    }
}

echo json_encode(["success" => false, "error" => "Delete failed."]);
