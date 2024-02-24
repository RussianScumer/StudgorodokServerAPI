<?php
//session_start();
$mysql_host = "localhost"; 
$mysql_user = "a0872478_StudgorodokDB"; 
$mysql_password = "BkmzRjhyttdtw2003!"; 
$mysql_database = "a0872478_StudgorodokDB"; 
$charset = 'utf8';
$connection = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if ($connection->connect_error) {
    die("ConnectError".$connection->connect_error);
}
if(!$connection->set_charset($charset)){
    echo "EncodeError";
}

// Обработка POST запроса
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type:text/plain");
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $user = $data["user"];
    $acctoken = $data['acctoken'];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
        $result = $connection->query("SELECT admins FROM adminsDB WHERE admins = '$user'");
        if ($result->num_rows > 0) {
            echo "admin";
        } else {
            echo "not admin";
        }
    } else {
        $connection->close();
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
}
$connection->close();
?>