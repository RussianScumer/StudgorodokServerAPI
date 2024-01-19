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
    $img = $data["img"];
    $content = $data["content"];
    $currentDateTime = new DateTime('now');
    $currentDate = $currentDateTime->format('Y-m-d');
    if ($img != "" && $img != "unchanged") {
        $filename = "news_img/" . $currentDateTime->format('Y-m-d_H-i-s') . $data["extension"];
        file_put_contents($filename, base64_decode($img));    
    } else {
        $filename = "";
    }
    if ($data["requestType"] == "add") {
        $stmt = $connection->prepare("INSERT INTO newsDB (title, img, content, dateOfNews) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $title, $filename, $content, $currentDate);
        $stmt->execute();
        echo("successful");
        $stmt->close();
    }
    else if ($data["requestType"] == "change") {
        $id = intval($data["id"]);
        if ($img != "unchanged") {
            $result = $connection->query("SELECT img FROM newsDB WHERE id = '$id'");
            $row = $result->fetch_assoc();
            unlink($row["img"]);
            $request = "UPDATE newsDB SET title = '$title', img = '$filename', content = '$content' WHERE id = '$id'";    
        }
        else {
            $request = "UPDATE newsDB SET title = '$title', content = '$content' WHERE id = '$id'";
        }
        if ($connection->query($request)) {
            echo("successful");
        }
    }
    else if ($data["requestType"] == "delete") {
        $id = intval($data["id"]);
        $result = $connection->query("SELECT img FROM newsDB WHERE id = '$id'");
        $row = $result->fetch_assoc();
        unlink($row["img"]);
        $request = "DELETE FROM newsDB WHERE id = '$id'";
        if ($connection->query($request)) {
            echo("successful");
        }
    }
}

// Обработка GET запроса
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $connection->query("SELECT id, title, img, content, dateOfNews FROM newsDB ORDER BY id DESC");
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
}
$connection->close();
?>