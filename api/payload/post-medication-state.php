<?php
require_once '../curlHandle.php';

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (isset($data['id_med_obat'], $data['display'], $data['id_pasien'], $data['nama_pasien'], $data['id_encounter'], $data['instruksi_dosis'])) {
    $idMedic = htmlspecialchars($data['id_med_obat']);
    $namaObat = htmlspecialchars($data['display']);
    $idPasien = htmlspecialchars($data['id_pasien']);
    $namaPasien = htmlspecialchars($data['nama_pasien']);
    $idEncounter = htmlspecialchars($data['id_encounter']);
    $instruksiDosis = htmlspecialchars($data['instruksi_dosis']);

    $data = [
        "resourceType" => "MedicationStatement",
        "status" => "completed",
        "category" => [
            "coding" => [
                [
                    "system" => "http://terminology.hl7.org/CodeSystem/medication-statement-category",
                    "code" => "outpatient",
                    "display" => "Outpatient"
                ]
            ]
        ],
        "medicationReference" => [
            "reference" => "Medication/" . $idMedic,
            "display" => $namaObat
        ],
        "subject" => [
            "reference" => "Patient/" . $idPasien,
            "display" => $namaPasien
        ],
        "dosage" => [
            [
                "text" => $instruksiDosis,
                "timing" => [
                    "repeat" => [
                        "frequency" => 1,
                        "period" => 4,
                        "periodMax" => 6,
                        "periodUnit" => "h"
                    ]
                ]
            ]
        ],
        "effectiveDateTime" => date('c', strtotime('2023-01-23T18:00:00+00:00')),
        "dateAsserted" => date('c'),
        "informationSource" => [
            "reference" => "Patient/" . $idPasien,
            "display" => $namaPasien
        ],
        "context" => [
            "reference" => "Encounter/" . $idEncounter
        ]
    ];

    $response = addMedicationStatement($data);
    http_response_code($response['status']);

    if ($response['status'] == 200 || $response['status'] == 201) {
        $db = connectDB();

        if ($db) {
            $createdAt = date('Y-m-d H:i:s');

            try {
                $stmt = $db->prepare("INSERT INTO medication_statement (id_med_state, id_encounter, id_med_obat, instruksi_dosis, created_at) VALUES (:id_med_state, :id_encounter, :id_med_obat, :instruksi_dosis, :created_at)");
                $stmt->execute([
                    'id_med_state' => $response['body']['id'],
                    'id_encounter' => str_replace('Encounter/', '', $response['body']['context']['reference']),
                    'id_med_obat' => str_replace('Medication/', '', $response['body']['medicationReference']['reference']),
                    'instruksi_dosis' => $response['body']['dosage'][0]['text'],
                    'created_at' => $createdAt
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Database query failed: " . $e->getMessage()]);
                exit;
            }
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Database connection failed"]);
            exit;
        }
    } else {
        http_response_code($response['status']);
        echo json_encode($response['body']);
        exit;
    }

    echo json_encode($response['body']['status']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}
?>