<?php
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
    $title = $data["title"];
    $type = $data["type"];
    $price = $data["price"];
    $img = $data["img"];
    $category = $data["category"];
    $currentDateTime = new DateTime('now');
    $filename = "canteen_img/" . $currentDateTime->format('Y-m-d_H-i-s') . $data["extension"];
    file_put_contents($filename, base64_decode($img));
    $stmt = $connection->prepare("INSERT INTO canteenDB (title, type, price, img, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $title, $type, $price, $filename, $category);
    $stmt->execute();
    echo("successful");
    $stmt->close();
}

// Обработка GET запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $connection->query("SELECT title, type, price, img, category FROM canteenDB ORDER BY id DESC");
    $rows = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row["img"] = "http://a0872478.xsph.ru/" . $row["img"];
            $rows[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($rows);
    } else {
        echo "0 results";
    }
}
$connection->close();
?>