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
        if ($data["requestType"] == "add") {
            $title = $data["title"];
            $comments = $data["comments"];
            $contacts = $data["contacts"];
            $price = $data["price"];
            $stud_number = $data["stud_number"];
            $sender_name = $data["sender_name"];
            $currentDateTime = new DateTime('now');
            $filename = "";
            $stmt = $connection->prepare("INSERT INTO suggestedAdsDB (title, comments, contacts, price, img, stud_number, sender_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssss', $title, $comments, $contacts, $price, $filename, $stud_number, $sender_name);
            $stmt->execute(); 
            echo("successful");
            $stmt->close();
        } else if ($data["requestType"] == "delete") {
            $id = intval($data["id"]);
            $result = $connection->query("SELECT img FROM barterDB WHERE id = '$id'");
            $row = $result->fetch_assoc();
            $images = explode(",", $row["img"]);
            foreach ($images as $image) {
                unlink(substr($image, strlen("http://a0872478.xsph.ru/")));    
            }
            $request = "DELETE FROM barterDB WHERE id = '$id'";
            if ($connection->query($request)) {
                echo("successful");
            }
        } else if ($data["requestType"] == "add_image") {
            $stud_number = $data["stud_number"];
            $result = $connection->query("SELECT id, img FROM suggestedAdsDB WHERE id = (SELECT MAX(id) FROM suggestedAdsDB WHERE stud_number = '$stud_number')");
            $row = $result->fetch_assoc();
            $currentDateTime = new DateTime('now');
            $filename = "barter_img/" . $currentDateTime->format('Y-m-d_H-i-s') . $data["extension"];
            file_put_contents($filename, base64_decode($data["img"]));
            if ($row["img"] == "") {
                $filename = "http://a0872478.xsph.ru/" . $filename;
            } else {
                $filename = $row["img"] . ",http://a0872478.xsph.ru/" . $filename;
            }
            $request = "UPDATE suggestedAdsDB SET img = '$filename' WHERE id = " . $row["id"];
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
    $result = $connection->query("SELECT id, title, comments, contacts, price, img, stud_number, sender_name FROM barterDB ORDER BY id DESC");
    $rows = array();
    $acctoken = $_GET["PHP_ACCTOKEN"];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
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