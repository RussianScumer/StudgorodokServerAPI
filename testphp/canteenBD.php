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
    $acctoken = $data['acctoken'];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
        $title = $data["title"];
        $type = $data["type"];
        $price = $data["price"];
        $img = $data["img"];
        $currentDateTime = new DateTime('now');
        if ($img != "" && $img != "unchanged") {
            $filename = "canteen_img/" . $currentDateTime->format('Y-m-d_H-i-s') . $data["extension"];
            file_put_contents($filename, base64_decode($img));    
        } else {
            $filename = "";
        }
        if ($data["requestType"] == "add") {
            $stmt = $connection->prepare("INSERT INTO canteenDB (title, type, price, img) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $title, $type, $price, $filename);
            $stmt->execute();
            echo("successful");
            $stmt->close();
        } else if ($data["requestType"] == "change") {
            $id = intval($data["id"]);
            if ($img != "unchanged") {
                $result = $connection->query("SELECT img FROM canteenDB WHERE id = '$id'");
                $row = $result->fetch_assoc();
                unlink($row["img"]);
                $request = "UPDATE canteenDB SET title = '$title', img = '$filename', price = '$price', type = '$type' WHERE id = '$id'";    
            } else {
                $request = "UPDATE canteenDB SET title = '$title', price = '$price', type = '$type' WHERE id = '$id'";
            }
            if ($connection->query($request)) {
                echo("successful");
            }
        } else if ($data["requestType"] == "delete") {
            $id = intval($data["id"]);
            $result = $connection->query("SELECT img FROM canteenDB WHERE id = '$id'");
            $row = $result->fetch_assoc();
            unlink($row["img"]);
            $request = "DELETE FROM canteenDB WHERE id = '$id'";
            if ($connection->query($request)) {
                echo("successful");
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
        $result = $connection->query("SELECT * FROM canteenDB ORDER BY id DESC");
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