<?php
require_once '../curlHandle.php';

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (isset($data['id_medic']) && isset($data['display']) && isset($data['id_pasien']) && isset($data['nama_pasien']) && isset($data['id_encounter']) && isset($data['id_med_req']) && isset($data['instruksi_dosis'])) {
    $idMedic = $data['id_medic'];
    $namaObat = $data['display'];
    $idEncounter = $data['id_encounter'];
    $idMedReq = $data['id_med_req'];
    $namaPasien = $data['nama_pasien'];
    $idPasien = $data['id_pasien'];
    $instruksiDosis = $data['instruksi_dosis'];

    $medicationDispenseData = [
        "resourceType" => "MedicationDispense",
        "identifier" => [
            [
                "system" => "http://sys-ids.kemkes.go.id/prescription/" . ORG_ID,
                "use" => "official",
                "value" => "123456788"
            ],
            [
                "system" => "http://sys-ids.kemkes.go.id/prescription-item/" . ORG_ID,
                "use" => "official",
                "value" => "123456788-1"
            ]
        ],
        "status" => "completed",
        "category" => [
            "coding" => [
                [
                    "system" => "http://terminology.hl7.org/fhir/CodeSystem/medicationdispense-category",
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
        "context" => [
            "reference" => "Encounter/" . $idEncounter
        ],
        "performer" => [
            [
                "actor" => [
                    "reference" => "Practitioner/N10000003",
                    "display" => "John Miller"
                ]
            ]
        ],
        "location" => [
            "reference" => "Location/52e135eb-1956-4871-ba13-e833e662484d",
            "display" => "Apotek RSUD Jati Asih"
        ],
        "authorizingPrescription" => [
            [
                "reference" => "MedicationRequest/" . $idMedReq
            ]
        ],
        "quantity" => [
            "value" => 120,
            "unit" => "TAB",
            "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
            "code" => "TAB"
        ],
        "daysSupply" => [
            "value" => 30,
            "unit" => "Day",
            "system" => "http://unitsofmeasure.org",
            "code" => "d"
        ],
        "whenPrepared" => "2022-01-15T10:20:00Z",
        "whenHandedOver" => "2022-01-15T16:20:00Z",
        "dosageInstruction" => [
            [
                "sequence" => 1,
                "text" => $instruksiDosis,
                "timing" => [
                    "repeat" => [
                        "frequency" => 1,
                        "period" => 1,
                        "periodUnit" => "d"
                    ]
                ],
                "doseAndRate" => [
                    [
                        "type" => [
                            "coding" => [
                                [
                                    "system" => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                    "code" => "ordered",
                                    "display" => "Ordered"
                                ]
                            ]
                        ],
                        "doseQuantity" => [
                            "value" => 4,
                            "unit" => "TAB",
                            "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                            "code" => "TAB"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $response = addMedicationDispense($medicationDispenseData);
    http_response_code($response['status']);

    if ($response['status'] == 200 || $response['status'] == 201) {
        $db = connectDB();

        if ($db) {
            $createdAt = date('Y-m-d H:i:s');
            
            $stmt = $db->prepare("INSERT INTO medication_dispense (id_med_dis, id_med_req, id_med_obat, id_encounter, status, created_at) VALUES (:id_med_dis, :id_med_req, :id_med_obat, :id_encounter, :status, :created_at)");
            $stmt->execute([
                'id_med_dis' => $response['body']['id'],
                'id_med_req' => str_replace('MedicationRequest/', '', $response['body']['authorizingPrescription'][0]['reference']),
                'id_med_obat' => str_replace('Medication/', '', $response['body']['medicationReference']['reference']),
                'id_encounter' => str_replace('Encounter/', '', $response['body']['context']['reference']),
                'created_at' => $createdAt,
                'status' => 'sedang digunakan'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Database connection failed"]);
            exit;
        }
    }

    echo json_encode($response['body']['status']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}
?>