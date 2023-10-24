<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    echo "aboba";
} 
$username = $_GET["PHP_AUTH_USER"];
$password = $_GET["PHP_AUTH_PW"];
//$username = '8211862';
//$password = 'BkmzRjhyttdtw2003';
$resultString = $username . ':' . $password;
$api_url = 'https://orioks.miet.ru/api/v1/auth'; // Замените на ваш URL
$encoded_auth = base64_encode($resultString); // Замените на ваши реальные учетные данные

$headers = array(
    'Accept: application/json',
    'Authorization: Basic ' . $encoded_auth,
    'User-Agent: TestApiAPP/0.1 Windows 10',
    // Замените на реальные данные
);
$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$token = curl_exec($ch);
$api_url2 = 'https://orioks.miet.ru/api/v1/student';
if ($token === false) {
    echo 'Ошибка cURL: ' . curl_error($ch);
} else {
    $decoded_response = json_decode($token, true); // Парсинг JSON
    if (isset($decoded_response['token'])) {
        //echo "true";
        $headersNew = [
            'Accept: application/json',
            'Authorization: Bearer ' . $decoded_response['token'],
            'User-Agent: TestApiAPP/0.1 Windows 10',
        ];
        $ch2 = curl_init($api_url2);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headersNew);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch2);
        //echo $response;
        $decoded_response2 = json_decode($response, true);
        echo $decoded_response2['full_name'];
        echo " ";
        echo $decoded_response2['group'];
    } else {
        echo "The response does not contain a token field.";
    }
}
curl_close($ch);
?>