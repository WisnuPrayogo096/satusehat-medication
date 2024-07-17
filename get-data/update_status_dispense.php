<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'koneksi.php';

    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
        $id = $data['id'];
        $idMedic = $data['id_medic'];
        $display = $data['display'];
        $idPasien = $data['id_pasien'];
        $namaPasien = $data['nama_pasien'];
        $idEncounter = $data['id_encounter'];
        $idMedReq = $data['id_med_req'];
        $instruksiDosis = $data['instruksi_dosis'];
        
        if (isset($data['status'])) {
            $status = $data['status'];
        } else {
            $status = 'Done';
        }

        $sql = "UPDATE pesanan_obat SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo "Success Terkirim";
            
            $dataToSend = array(
                'id' => $id,
                'id_medic' => $idMedic,
                'display' => $display,
                'id_pasien' => $idPasien,
                'nama_pasien' => $namaPasien,
                'id_encounter' => $idEncounter,
                'id_med_req' => $idMedReq,
                'instruksi_dosis' => $instruksiDosis,
                'status' => $status
            );

            $ch = curl_init('http://medication.test:8080/api/payload/post-medication-dispense.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataToSend));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                echo 'Response API: ' . $response;
            }

            curl_close($ch);

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
    
    $conn->close();
}
?>