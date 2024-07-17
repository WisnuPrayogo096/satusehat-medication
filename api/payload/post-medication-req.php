<?php
require_once '../curlHandle.php';

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (isset($data['idMedic'], $data['namaObat'], $data['instruksiDosis'], $data['instruksiPasien'], $data['idEncounter'], $data['idPatient'], $data['namaPasien'])) {
    $idMedic = $data['idMedic'];
    $namaObat = $data['namaObat'];
    $instruksiDosis = $data['instruksiDosis'];
    $instruksiPasien = $data['instruksiPasien'];
    $idEncounter = $data['idEncounter'];
    $idPatient = $data['idPatient'];
    $namaPasien = $data['namaPasien'];
    
    $db = connectDB();

    $checkStmt = $db->prepare("SELECT id, code_obat FROM medication_obat WHERE nama_obat = :namaObat");
    $checkStmt->execute(['namaObat' => $namaObat]);
    $medicationObat = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($medicationObat) {
        $idObat = $medicationObat['id'];
        $codeObat = $medicationObat['code_obat'];
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Obat tidak ditemukan dalam database"]);
        exit;
    }

    $medicationRequestData = [
        "resourceType" => "MedicationRequest", 
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
        "intent" => "order", 
        "category" => [
            [
                "coding" => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-category", 
                        "code" => "outpatient", 
                        "display" => "Outpatient"
                    ]
                ]
            ]
        ], 
        "priority" => "routine", 
        "medicationReference" => [
            "reference" => "Medication/" . $idMedic, 
            "display" => $namaObat
        ], 
        "subject" => [
            "reference" => "Patient/" . $idPatient, 
            "display" => $namaPasien
        ], 
        "encounter" => [
            "reference" => "Encounter/" . $idEncounter,
        ], 
        "authoredOn" => date("c"), // Current timestamp in ISO 8601
        "requester" => [
            "reference" => "Practitioner/N10000001", 
            "display" => "Dokter Bronsig"
        ], 
        "reasonCode" => [
            [
                "coding" => [
                    [
                        "system" => "http://hl7.org/fhir/sid/icd-10", 
                        "code" => "A15.0", 
                        "display" => "Tuberculosis of lung, confirmed by sputum microscopy with or without culture"
                    ]
                ]
            ]
        ], 
        "courseOfTherapyType" => [
            "coding" => [
                [
                    "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-course-of-therapy", 
                    "code" => "continuous", 
                    "display" => "Continuing long term therapy"
                ]
            ]
        ], 
        "dosageInstruction" => [
            [
                "sequence" => 1, 
                "text" => $instruksiDosis,
                "additionalInstruction" => [
                    [
                        "text" => "Diminum setiap hari"
                    ]
                ], 
                "patientInstruction" => $instruksiPasien,
                "timing" => [
                    "repeat" => [
                        "frequency" => 1, 
                        "period" => 1, 
                        "periodUnit" => "d"
                    ]
                ], 
                "route" => [
                    "coding" => [
                        [
                            "system" => "http://www.whocc.no/atc", 
                            "code" => "O", 
                            "display" => "Oral"
                        ]
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
        ], 
        "dispenseRequest" => [
            "dispenseInterval" => [
                "value" => 1, 
                "unit" => "days", 
                "system" => "http://unitsofmeasure.org", 
                "code" => "d"
            ], 
            "validityPeriod" => [
                "start" => date("Y-m-d\TH:i:s\Z", strtotime("now")), 
                "end" => date("Y-m-d\TH:i:s\Z", strtotime("+30 days"))
            ], 
            "numberOfRepeatsAllowed" => 0, 
            "quantity" => [
                "value" => 120, 
                "unit" => "TAB", 
                "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm", 
                "code" => "TAB"
            ], 
            "expectedSupplyDuration" => [
                "value" => 30, 
                "unit" => "days", 
                "system" => "http://unitsofmeasure.org", 
                "code" => "d"
            ], 
            "performer" => [
                "reference" => "Organization/" . ORG_ID
            ]
        ]
    ];

    $response = addMedicationRequest($medicationRequestData);
    http_response_code($response['status']);

    if (in_array($response['status'], [200, 201])) {
        $stmt = $db->prepare("INSERT INTO pesanan_obat (id_pasien, id_encounter, nama_pasien, id_med_req, id_obat, id_medic, code_obat, nama_obat, instruksi_dosis, instruksi_pasien, status, created_at) 
        VALUES (:id_pasien, :id_encounter, :nama_pasien, :id_med_req, :id_obat, :id_medic, :code_obat, :nama_obat, :instruksi_dosis, :instruksi_pasien, :status, :created_at)");

        $stmt->execute([
            'created_at' => $response['body']['authoredOn'],
            'instruksi_pasien' => $response['body']['dosageInstruction'][0]['patientInstruction'],
            'instruksi_dosis' => $response['body']['dosageInstruction'][0]['text'],
            'id_encounter' => str_replace('Encounter/', '', $response['body']['encounter']['reference']),
            'id_med_req' => $response['body']['id'],
            'nama_pasien' => $response['body']['subject']['display'],
            'id_obat' => $idObat,
            'code_obat' => $codeObat,
            'status' => $response['body']['intent'],
            'nama_obat' => $response['body']['medicationReference']['display'],
            'id_medic' => str_replace('Medication/', '', $response['body']['medicationReference']['reference']),
            'id_pasien' => str_replace('Patient/', '', $response['body']['subject']['reference'])
        ]);
    }

    echo json_encode($response['body']['status']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}
?>