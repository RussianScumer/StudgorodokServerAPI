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
$result = $connection->query("SELECT * FROM newsDB WHERE DATEDIFF( NOW( ) ,  dateOfNews ) >=7");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($rows);
} else {
    echo "0 results";
}
$connection->close();
?>