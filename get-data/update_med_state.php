<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_encounter = $_POST['id_encounter'];
    $id_med_req = $_POST['id_med_req'];
    $status = $_POST['status_penggunaan'];
    $dosis = $_POST['dosis'];
    $medis = $_POST['medis'];

    $response = [];

    $sql = "UPDATE medication_dispense SET status='$status' WHERE id_encounter='$id_encounter' AND id_med_req='$id_med_req'";
    if ($conn->query($sql) === TRUE) {
        $sql = "UPDATE pesanan_obat SET instruksi_dosis='$dosis', instruksi_pasien='$medis' WHERE id_encounter='$id_encounter' AND id_med_req='$id_med_req'";
        if ($conn->query($sql) === TRUE) {
            $sql = "SELECT md.id_med_obat, po.id_pasien, po.nama_pasien, po.nama_obat
                    FROM medication_dispense md 
                    JOIN pesanan_obat po ON md.id_encounter = po.id_encounter 
                    WHERE md.id_encounter='$id_encounter' AND md.id_med_req='$id_med_req'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $response = [
                    'message' => 'Record updated successfully',
                    'id_med_obat' => $row['id_med_obat'],
                    'id_pasien' => $row['id_pasien'],
                    'nama_pasien' => $row['nama_pasien'],
                    'display' => $row['nama_obat'],
                    'instruksi_dosis' => $dosis,
                    'id_encounter' => $id_encounter
                ];

                $json_data = json_encode($response);

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://medication.test:8080/api/payload/post-medication-state.php',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $json_data,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($json_data)
                    )
                ));
                $response_curl = curl_exec($curl);
                if ($response_curl === false) {
                    $response['error'] = 'Curl error: ' . curl_error($curl);
                }
                curl_close($curl);
            } else {
                $response['error'] = 'Error fetching additional data: No matching records found';
            }
        } else {
            $response['error'] = 'Error updating record: ' . $conn->error;
        }
    } else {
        $response['error'] = 'Error updating record: ' . $conn->error;
    }

    $conn->close();

    echo json_encode($response);
    exit();
}
?>