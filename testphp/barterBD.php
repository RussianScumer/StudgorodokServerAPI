<?php
$acctoken = $_GET["PHP_ACCTOKEN"];
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
    $acctoken = json_decode($data['acctoken']);
    $token = $connection->execute_query("SELECT acctoken FROM tokens WHERE acctoken = ?", $acctoken);
    if($token->num_rows != 0){
    if ($data["requestType"] == "add") {
        $title = $data["title"];
        $comments = $data["comments"];
        $contacts = $data["contacts"];
        $price = $data["price"];
        $img = $data["img"];
        $stud_number = $data["stud_number"];
        $currentDateTime = new DateTime('now');
        if ($img != "") {
            $filename = "barter_img/" . $currentDateTime->format('Y-m-d_H-i-s') . $data["extension"];
            file_put_contents($filename, base64_decode($img));    
        } else {
            $filename = "";
        }
        $stmt = $connection->prepare("INSERT INTO suggestedAdsDB (title, comments, contacts, price, img, stud_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $title, $comments, $contacts, $price, $filename, $stud_number);
        $stmt->execute(); 
        echo("successful");
        $stmt->close();
    } else if ($data["requestType"] == "delete") {
        $id = intval($data["id"]);
        $result = $connection->query("SELECT img FROM barterDB WHERE id = '$id'");
        $row = $result->fetch_assoc();
        unlink($row["img"]);
        $request = "DELETE FROM barterDB WHERE id = '$id'";
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
    $token = $connection->execute_query("SELECT acctoken FROM tokens WHERE acctoken = ?", $acctoken);
    if($token->num_rows != 0){
    $result = $connection->query("SELECT id, title, comments, contacts, price, img, stud_number FROM barterDB ORDER BY id DESC");
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
}else {
    $connection->close();
    header("HTTP/1.1 401 Unauthorized");
    exit();
}
}
$connection->close();
?>