<?php
require_once 'config-sandbox.php';

function connectDB()
{
    $host = 'localhost';
    $db = 'satu_sehat_db';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// encounter
function addEncounter($encounterData)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return [
            'status' => 500,
            'body' => $authToken
        ];
    }

    $urlAdd = WS_BASE_URL . '/Encounter';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAdd,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($encounterData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function searchEncounter($subjectId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/Encounter?subject=' . $subjectId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input encounter
// $subjectId = '100000030011';
// $encounterData = searchEncounter($subjectId);
// echo "Encounter Data: " . $encounterData;

function searchEncounterId($encounterId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/Encounter/' . $encounterId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input encounter
// $encounterId = '2fd42f5e-ae29-40e1-9982-13e76824f274';
// $detailEncounterResponse = searchEncounterId($encounterId);
// echo "Detail Encounter Response: " . $detailEncounterResponse;

// medication-obat

function addMedication($medicationData)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return [
            'status' => 500,
            'body' => $authToken
        ];
    }

    $urlAdd = WS_BASE_URL . '/Medication';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAdd,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($medicationData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function searchMedicationId($medicationId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/Medication/' . $medicationId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input medication obat
// $medicationId = '8325d324-ef4f-430d-896f-b0e7a90ce61c';
// $obatResponse = searchMedicationId($medicationId);
// echo "Detail Obat Response: " . $obatResponse;

// medication-request

function addMedicationRequest($medicationRequestData)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return [
            'status' => 500,
            'body' => $authToken
        ];
    }

    $urlAdd = WS_BASE_URL . '/MedicationRequest';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAdd,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($medicationRequestData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function searchMedicationRequest($medicationRequestId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/MedicationRequest/' . $medicationRequestId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input medication request
// $medicationRequestId = 'c9e46c8e-cb5a-410b-a8b6-0a9081f19bc4';
// $cekResponse = searchMedicationRequest($medicationRequestId);
// echo "Detail Response: " . $cekResponse;


function addMedicationDispense($medicationDispenseData)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return [
            'status' => 500,
            'body' => $authToken
        ];
    }

    $urlAdd = WS_BASE_URL . '/MedicationDispense';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAdd,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($medicationDispenseData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function searchMedicationDispense($medicationDispenseId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/MedicationDispense/' . $medicationDispenseId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input medication Dispense
// $id = '0d53bc8b-bfbb-4f6b-a786-ebed1eba33bd';
// $cekResponse = searchMedicationDispense($id);
// echo "Detail Response: " . $cekResponse;

function addMedicationStatement($medicationStatementData)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return [
            'status' => 500,
            'body' => $authToken
        ];
    }

    $urlAdd = WS_BASE_URL . '/MedicationStatement';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAdd,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($medicationStatementData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

function searchMedicationStatement($medicationStatementId)
{
    $authToken = authToken();
    if (strpos($authToken, 'Error') === 0) {
        return $authToken;
    }

    $urlSearch = WS_BASE_URL . '/MedicationStatement/' . $medicationStatementId;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlSearch,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}

// Contoh data input medication Statement
// $id = '9a63e05b-46b2-4a9e-bc80-415254fe13cd';
// $cekResponse = searchMedicationStatement($id);
// echo "Detail Response: " . $cekResponse;
?>