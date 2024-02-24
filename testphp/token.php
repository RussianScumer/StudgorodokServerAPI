<?php
function gen_token() {
	$bytes = openssl_random_pseudo_bytes(20, $cstrong);
    $accToken = bin2hex($bytes);
    echo " token:";
    echo $accToken; 
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
    $stmt = $connection->prepare("INSERT INTO tokens (acctoken) VALUES (?)");
    $stmt->bind_param('s', $accToken);
    $stmt->execute();
    $stmt->close();
    $connection->close();
}
?>