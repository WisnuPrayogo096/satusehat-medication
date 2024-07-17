<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data JSON yang diterima
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        // Menampilkan data yang diterima
        echo "Data yang diterima dari index.php: <br>";
        echo "ID: " . htmlspecialchars($data['id']) . "<br>";
        echo "ID Medic: " . htmlspecialchars($data['id_medic']) . "<br>";
        echo "Display: " . htmlspecialchars($data['display']) . "<br>";
        echo "ID Pasien: " . htmlspecialchars($data['id_pasien']) . "<br>";
        echo "Nama Pasien: " . htmlspecialchars($data['nama_pasien']) . "<br>";
        echo "ID Encounter: " . htmlspecialchars($data['id_encounter']) . "<br>";
        echo "ID Med Req: " . htmlspecialchars($data['id_med_req']) . "<br>";
        echo "Instruksi Dosis: " . htmlspecialchars($data['instruksi_dosis']) . "<br>";
        echo "Status: " . htmlspecialchars($data['status']) . "<br>";
    } else {
        echo "Tidak ada data yang diterima.";
    }
} else {
    echo "Metode request bukan POST.";
}
?>