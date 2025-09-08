<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['mediaFile'])) {
    $service = $_POST['service'] ?? 'general';
    $uploadDir = "uploads/" . $service . "/";

    // Create folder if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['mediaFile']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['mediaFile']['tmp_name'], $targetFile)) {
        // Path to JSON "database"
        $dbFile = "uploads/data.json";
        $data = file_exists($dbFile) ? json_decode(file_get_contents($dbFile), true) : [];

        if (!isset($data[$service])) {
            $data[$service] = [];
        }

        $data[$service][] = [
            "type" => (strpos($_FILES['mediaFile']['type'], "video") === 0) ? "video" : "image",
            "src"  => $targetFile
        ];

        file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT));

        echo json_encode(["success" => true, "src" => $targetFile]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to upload file."]);
    }
    exit;
}

echo json_encode(["success" => false, "error" => "Invalid request."]);
