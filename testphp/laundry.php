<?php
$mysql_host = "localhost";
$mysql_user = "a0872478_StudgorodokDB";
$mysql_password = "BkmzRjhyttdtw2003!";
$mysql_database = "a0872478_StudgorodokDB";
$charset = 'utf8';

// Подключение к базе данных
$connection = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
if ($connection->connect_error) {
    die("ConnectError: " . $connection->connect_error);
}

if (!$connection->set_charset($charset)) {
    echo "EncodeError";
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["PHP_ID"])) {
        $id = $_GET["PHP_ID"];
        $acctoken = $_GET["PHP_ACCTOKEN"];
        $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
        if ($token->num_rows != 0) {
            $result = $connection->query('SELECT id, status FROM laundryDB WHERE id = "' . $id . '"');
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $response = array("status" => $row["status"]);
                echo json_encode($response);
            } else {
                $response = array("error" => "Элемент не найден");
                echo json_encode($response);
            }
        } else {
            $connection->close();
            header("HTTP/1.1 401 Unauthorized");
            exit();
        }

        $sql = "SELECT id, status FROM laundryDB WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Обработка результатов запроса
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response = array("status" => $row["status"]);
            echo json_encode($response);
        } else {
            $response = array("error" => "Элемент не найден");
            echo json_encode($response);
        }

        // Закрытие соединения с базой данных
        $stmt->close();
        $connection->close();
    } else {
        // Если параметр "id" не был передан, возвращаем сообщение об ошибке
        $response = array("error" => "Отсутствует параметр 'id'");
        echo json_encode($response);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type:application/json");
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $acctoken = $data['acctoken'];
    $token = $connection->query('SELECT acctoken FROM tokens WHERE acctoken = "' . $acctoken . '"');
    if ($token->num_rows != 0) {
        $id = $data["id"];
        if ($data["requestType"] == "add") {
            $stmt = $connection->prepare("INSERT INTO laundryDB (id, status) VALUES (?, 0)");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo("successful");
            $stmt->close();
        } else if ($data["requestType"] == "change") {
            $id = intval($data["id"]);
            $status = $data["status"];
            $request = "UPDATE laundryDB SET status = '$status' WHERE id = '$id'";
            if ($connection->query($request)) {
                echo("successful");
            }
        } else if ($data["requestType"] == "delete") {
            $id = intval($data["id"]);
            $request = "DELETE FROM laundryDB WHERE id = '$id'";
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
?>
