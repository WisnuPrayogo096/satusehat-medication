<?php
define('WS_AUTH_API', ""); 
define('WS_BASE_URL', "");
define('WS_CONSENT_URL', "");
define('WS_CONFIG', [
    'client_id' => '', 
    'client_secret' => '',
]);
define('ORG_ID', '');
define('FAR_ID', '');

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
