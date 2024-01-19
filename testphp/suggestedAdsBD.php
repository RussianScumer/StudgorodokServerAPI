<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type:application/json");
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $type = $data["type"];
    if ($type == "approved") {
        $id = intval($data["id"]);
        $result = $connection->query("SELECT title, comments, contacts, price, img, stud_number FROM suggestedAdsDB WHERE id = $id");
        $rows = array();
        if ($result->num_rows != 1) {
            echo("failed");
        }
        $row = $result->fetch_assoc();
        $stmt = $connection->prepare("INSERT INTO barterDB (title, comments, contacts, price, img, stud_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $row["title"], $row["comments"], $row["contacts"], $row["price"], $row["img"], $row["stud_number"]);
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
        if ($row["img"] != "") {
            unlink($row["img"]);
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
}

// Обработка GET запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $connection->query("SELECT id, title, comments, contacts, price, img, stud_number FROM suggestedAdsDB ORDER BY id DESC");
    $rows = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["img"] != "") {
                $row["img"] = "http://a0872478.xsph.ru/" . $row["img"];    
            }
            $rows[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($rows);
    } 
    else {
        echo "0 results";
    }
}
$connection->close();
?>