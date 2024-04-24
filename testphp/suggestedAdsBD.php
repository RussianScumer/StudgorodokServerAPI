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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type:application/json");
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $acctoken = $data["acctoken"];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
        $type = $data["type"];
        if ($type == "approved") {
            $id = intval($data["id"]);
            $result = $connection->query("SELECT title, comments, contacts, price, img, stud_number, sender_name FROM suggestedAdsDB WHERE id = $id");
            $rows = array();
            if ($result->num_rows != 1) {
                echo("failed");
            }
            $row = $result->fetch_assoc();
            $stmt = $connection->prepare("INSERT INTO barterDB (title, comments, contacts, price, img, stud_number, sender_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssss', $row["title"], $row["comments"], $row["contacts"], $row["price"], $row["img"], $row["stud_number"], $row["sender_name"]);
            $stmt->execute(); 
            $stmt->close();
            $connection->query("DELETE FROM suggestedAdsDB WHERE id = $id");
            echo("successful");
        } else if ($type == "rejected") {
            $id = intval($data["id"]);
            $result = $connection->query("SELECT img FROM suggestedAdsDB WHERE id = $id");
            $rows = array();
            if ($result->num_rows != 1) {
                echo("failed");
            }
            $row = $result->fetch_assoc();
            $images = explode(",", $row["img"]);
            foreach ($images as $image) {
                unlink(substr($image, strlen("http://a0872478.xsph.ru/")));    
            }
            $connection->query("DELETE FROM suggestedAdsDB WHERE id = $id");
            echo("successful");
        } else {
            $result = $connection->query("SELECT COUNT(*) AS amount FROM suggestedAdsDB");
            $rows = array();
            if ($result->num_rows != 1) {
                echo("failed");
            }
            $row = $result->fetch_assoc();
            if(intval($row["amount"]) != 0) {
                echo("new_ads");
            } else {
                echo("no_ads");
            }
        }
    } else {
        $connection->close();
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
}

// Обработка GET запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $acctoken = $_GET["PHP_ACCTOKEN"];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
        $result = $connection->query("SELECT id, title, comments, contacts, price, img, stud_number, sender_name FROM suggestedAdsDB ORDER BY id DESC");
        $rows = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode($rows);
        } else {
            echo "0 results";
        }
    } else {
        $connection->close();
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
}
$connection->close();
?>