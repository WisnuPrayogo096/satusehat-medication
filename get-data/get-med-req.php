<?php
require 'koneksi.php';

$sql = "SELECT id, id_encounter, id_patient, nama FROM encounter";
$result = $conn->query($sql);

$encounters = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $encounters[] = $row;
    }
}

$sql2 = "SELECT code_obat, nama_obat FROM medication_obat";
$result2 = $conn->query($sql2);

$medication_obat = array();
if ($result2->num_rows > 0) {
    while($row2 = $result2->fetch_assoc()) {
        $medication_obat[] = $row2;
    }
}
$conn->close();