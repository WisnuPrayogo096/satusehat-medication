<?php
define('WS_AUTH_API', "https://api-satusehat-stg.dto.kemkes.go.id/oauth2/v1"); 
define('WS_BASE_URL', "https://api-satusehat-stg.dto.kemkes.go.id/fhir-r4/v1");
define('WS_CONSENT_URL', "https://api-satusehat-stg.dto.kemkes.go.id/consent/v1");
define('WS_CONFIG', [
    'client_id' => '9QTHkApmf0Wq3XC4csG2ABSexbb3dn23mJpW8fTUoSaJlhOw', 
    'client_secret' => 'JwGqAVmDtEiNXVedQFQSaQTyGZM73hMGCi308M73KbZDCrYbLgGH7mXaKAoTe94F',
]);
define('ORG_ID', '16e50caa-6e41-4ecd-a36a-3801c51f973b');
define('FAR_ID', 'c31817cf-6169-471c-a4e1-ea00ad2a0c5b');

function authToken()
{
    $urlAuth = WS_AUTH_API . '/accesstoken?grant_type=client_credentials';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlAuth,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'client_id=' . WS_CONFIG['client_id'] . '&client_secret=' . WS_CONFIG['client_secret'],
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    $responseArray = json_decode($response, true);

    if (isset($responseArray['access_token'])) {
        return $responseArray['access_token'];
    } else {
        return 'Error: Unable to retrieve access token';
    }
}
?>