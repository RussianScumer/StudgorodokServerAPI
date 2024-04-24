<?php
include 'sqlauth.php';
use sqlauth\sqlpass;
$charset = 'utf8';
$connection = new mysqli(sqlpass::$mysql_host, sqlpass::$mysql_user, sqlpass::$mysql_password, sqlpass::$mysql_database);
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
?>
