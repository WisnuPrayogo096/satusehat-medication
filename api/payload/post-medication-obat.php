<?php
require_once '../curlHandle.php';

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (isset($data['codeObat']) && isset($data['namaObat'])) {
    $medicationData = [
        "resourceType" => "Medication",
        "meta" => [
            "profile" => [
                "https://fhir.kemkes.go.id/r4/StructureDefinition/Medication"
            ]
        ],
        "identifier" => [
            [
                "system" => "http://sys-ids.kemkes.go.id/medication/" . ORG_ID,
                "use" => "official",
                "value" => "123456789"
            ]
        ],
        "code" => [
            "coding" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/kfa",
                    "code" => $data['codeObat'],
                    "display" => $data['namaObat']
                ]
            ]
        ],
        "status" => "active",
        "manufacturer" => [
            "reference" => "Organization/" . FAR_ID
        ],
        "form" => [
            "coding" => [
                [
                    "system" => "http://terminology.kemkes.go.id/CodeSystem/medication-form",
                    "code" => "BS034",
                    "display" => "Kaplet Salut Selaput"
                ]
            ]
        ],
        "ingredient" => [
            [
                "itemCodeableConcept" => [
                    "coding" => [
                        [
                            "system" => "http://sys-ids.kemkes.go.id/kfa",
                            "code" => "91000330",
                            "display" => "Rifampin"
                        ]
                    ]
                ],
                "isActive" => true,
                "strength" => [
                    "numerator" => [
                        "value" => 150,
                        "system" => "http://unitsofmeasure.org",
                        "code" => "mg"
                    ],
                    "denominator" => [
                        "value" => 1,
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                        "code" => "TAB"
                    ]
                ]
            ]
        ],
        "extension" => [
            [
                "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/MedicationType",
                "valueCodeableConcept" => [
                    "coding" => [
                        [
                            "system" => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                            "code" => "NC",
                            "display" => "Non-compound"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $response = addMedication($medicationData);
    http_response_code($response['status']);

    if ($response['status'] == 200 || $response['status'] == 201) {
        $db = connectDB();

        $stmt = $db->prepare("INSERT INTO medication_obat (id_medic, code_obat, nama_obat, status, jenis_obat, id_manufaktur, last_updated) VALUES (:id_medic, :code_obat, :nama_obat, :status, :jenis_obat, :id_manufaktur, :last_updated)");
        $stmt->execute([
            'id_medic' => $response['body']['id'],
            'code_obat' => $response['body']['code']['coding'][0]['code'],
            'nama_obat' => $response['body']['code']['coding'][0]['display'],
            'status' => $response['body']['status'],
            'jenis_obat' => $response['body']['form']['coding'][0]['display'],
            'id_manufaktur' => str_replace('Organization/', '', $response['body']['manufacturer']['reference']),
            'last_updated' => $response['body']['meta']['lastUpdated']
        ]);
    }

    echo json_encode($response['body']['status']);
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid input"]);
}
?>