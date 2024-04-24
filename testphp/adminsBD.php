<?php
include 'sqlauth.php';
use sqlauth\sqlpass;
$charset = 'utf8';
$connection = new mysqli(sqlpass::$mysql_host, sqlpass::$mysql_user, sqlpass::$mysql_password, sqlpass::$mysql_database);
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