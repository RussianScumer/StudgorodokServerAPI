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
    $comments = $data["comments"];
    $contacts = $data["contacts"];
    $price = $data["price"];
    $img = $data["img"];
    $category = $data["category"];
    $stmt = $connection->prepare("INSERT INTO barterDB (title, comments, contacts, price, img, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $title, $comments, $contacts, $price, $img, $category);
    //mysqli_stmt_execute($stmt);
    $stmt->execute();
    echo("successful");
    $stmt->close();
}

// Обработка GET запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $connection->query("SELECT title, comments, contacts, price, img, category FROM barterDB ORDER BY id DESC LIMIT 10");
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
}
$connection->close();
?>