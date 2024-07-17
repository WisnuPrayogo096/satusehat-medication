<?php
require_once '../curlHandle.php';

$currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$currentTime = $currentDateTime->format('Y-m-d\TH:i:sP');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (isset($data['idPasien']) && isset($data['nama'])) {
    $encounterData = [
        "resourceType" => "Encounter",
        "status" => "arrived",
        "class" => [
            "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
            "code" => "AMB",
            "display" => "ambulatory"
        ],
        "subject" => [
            "reference" => "Patient/" . $data['idPasien'],
            "display" => $data['nama']
        ],
        "participant" => [
            [
                "type" => [
                    [
                        "coding" => [
                            [
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                "code" => "ATND",
                                "display" => "attender"
                            ]
                        ]
                    ]
                ],
                "individual" => [
                    "reference" => "Practitioner/N10000001",
                    "display" => "Dokter Bronsig"
                ]
            ]
        ],
        "period" => [
            "start" => $currentTime
        ],
        "location" => [
            [
                "location" => [
                    "reference" => "Location/b017aa54-f1df-4ec2-9d84-8823815d7228",
                    "display" => "Ruang 1A, Poliklinik Bedah Rawat Jalan Terpadu, Lantai 2, Gedung G"
                ]
            ]
        ],
        "statusHistory" => [
            [
                "status" => "arrived",
                "period" => [
                    "start" => $currentTime
                ]
            ]
        ],
        "serviceProvider" => [
            "reference" => "Organization/" . ORG_ID
        ],
        "identifier" => [
            [
                "system" => "http://sys-ids.kemkes.go.id/encounter/" . ORG_ID,
                "value" => "P20240001"
            ]
        ]
    ];

    $response = addEncounter($encounterData);
    http_response_code($response['status']);

    if ($response['status'] == 200 || $response['status'] == 201) {
        $db = connectDB();

        $stmt = $db->prepare("INSERT INTO encounter (id_encounter, id_patient, nama, waktu_dimulai) VALUES (:id_encounter, :id_patient, :nama, :waktu_dimulai)");
        $stmt->execute([
            'id_encounter' => $response['body']['id'],
            'id_patient' => str_replace('Patient/', '', $response['body']['subject']['reference']),
            'nama' => $response['body']['subject']['display'],
            'waktu_dimulai' => $response['body']['period']['start']
        ]);
    }

    echo json_encode($response['body']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
}
?>