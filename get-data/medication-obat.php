<?php
require 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $sql = "SELECT * FROM medication_obat";
    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);

} elseif ($method == 'DELETE') {
    $id = $_GET['id'];

    if ($id) {
        $sql = "DELETE FROM medication_obat WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response = array("message" => "Record deleted successfully");
            http_response_code(200);
        } else {
            $response = array("message" => "Error deleting record");
            http_response_code(500);
        }

        $stmt->close();
    } else {
        $response = array("message" => "Invalid input");
        http_response_code(400);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();