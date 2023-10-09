<?php // сохранить utf-8 !
// -------------------------------------------------------------------------- логины пароли
echo("works");
$mysql_host = "localhost"; // sql сервер
$mysql_user = "a0872478_chat"; // пользователь
$mysql_password = "BkmzRjhyttdtw2003!"; // пароль
$mysql_database = "a0872478_chat"; // имя базы данных chat
$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_database);
// -------------------------------------------------------------------------- если база недоступна
if (!mysqli_connect($mysql_host, $mysql_user, $mysql_password)){
	echo "<h2>База недоступна!</h2>";
exit;
}else{
// -------------------------------------------------------------------------- если база доступна
echo "<h2>База доступна!</h2>";


mysqli_select_db($mysqli, "f0872518_chat" );
mysqli_set_charset($mysqli,'utf8');
// -------------------------------------------------------------------------- выведем JSON
$q=mysqli_query($mysqli,"SELECT * FROM chat");
echo "<h3>Json ответ:</h3>";
// Выводим json
while($e=mysqli_fetch_assoc($q))
        $output[]=$e;
print(json_encode($output));

// -------------------------------------------------------------------------- выведем таблицу
$q=mysqli_query($mysqli,"SELECT * FROM chat");
echo "<h3>Табличный вид:</h3>";
echo "<table border=\"1\" width=\"100%\" bgcolor=\"#999999\">";
echo "<tr><td>_id</td><td>author</td>";
echo "<td>client</td><td>data</td><td>text</td></tr>";

for ($c=0; $c<mysqli_num_rows($q); $c++){

$f = mysqli_fetch_array($q);
echo "<tr><td>$f[_id]</td><td>$f[author]</td><td>$f[client]</td><td>$f[data]</td><td>$f[text]</td></tr>";

}
 echo "</tr></table>";

}
mysqli_close($mysqli);
// -------------------------------------------------------------------------- разорвем соединение с БД
?>