<?php
function fetchToken() {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://upyog.niua.org/user/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'username=NDNIUA&password=Niua@dashb0ard&userType=EMPLOYEE&tenantId=pg&scope=read&grant_type=password',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ZWdvdi11c2VyLWNsaWVudDo='
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response);
    return $data->access_token ?? null;
}

$token = fetchToken();
$currentEpochTimestamp = time();
$data = json_encode([
    "aggregationRequestDto" => [
        "visualizationType" => "METRIC",
        "visualizationCode" => "nurtTotalCitzens",
        "queryType" => "",
        "filters" => new stdClass(),
        "moduleLevel" => "",
        "aggregationFactors" => null,
        "requestDate" => [
            "startDate" => 1680287400000,
            "endDate" => $currentEpochTimestamp,
            "interval" => "month",
            "title" => "home"
        ]
    ],
    "headers" => ["tenantId" => "pg"],
    "RequestInfo" => [
        "apiId" => "Rainmaker",
        "authToken" => $token,
        "msgId" => "1701682996596|en_IN",
        "plainAccessRequest" => new stdClass()
    ]
]);

$curlHandler = curl_init();
curl_setopt_array($curlHandler, array(
    CURLOPT_URL => 'https://upyog.niua.org/dashboard-analytics/dashboard/getChartV2',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json;charset=UTF-8',
        'Authorization: Basic ZWdvdi11c2VyLWNsaWVudDo='
    ),
));
$response = curl_exec($curlHandler);
curl_close($curlHandler);

// Check if the response is valid and contains the expected data
$responseData = json_decode($response);

// Debugging: Log the entire response
error_log(print_r($responseData, true));

$responseData = json_decode($response);

// Check if the response is valid and contains the expected data
if ($responseData && isset($responseData->responseData->data[0]->headerValue)) {
    $citizenCount = $responseData->responseData->data[0]->headerValue;
} else {
    $citizenCount = 0; // Fallback value if data is missing
}

echo json_encode(["citizenCount" => $citizenCount]);
?>
