<?php //функции, связанные с взаимодействием с базой данных
//****************************************************************
//доп функции
function get_choice_html($arr, $name)
{
	$res = "";
	if(is_array($arr))
	{
		$res = "<datalist id='$name'>";
		foreach ($arr as $value)
			$res .= "<option> $value </option>";
		$res .= "</datalist>";
	}
	return $res;
}

function pswd_in_db($pswd)
{
	$options = ['cost' => 12,];
	return password_hash($pswd, PASSWORD_BCRYPT, $options);
}

function message($text)
{
	return '<dir class="message">' . $text . '</dir>';
}


//****************************************************************
//подключение
function connect_it()
{
	date_default_timezone_set('Asia/Vladivostok');
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db = "tictactoe";

	$link = mysqli_connect($servername, $username, $password, $db);
	$link -> set_charset("utf-8");
	if (! $link)
	{ echo message('Не могу соедениться с сервером'); return false; }
	return $link;
}

//****************************************************************
//пользователи
function user_register()
{
	$b_res = false;
    if (isset($_POST['reg'])){
		$link = connect_it();
		$name = $_POST['login'];
		$pswd = pswd_in_db($_POST['pswd']);
		//check
		$query = "SELECT * FROM `user` WHERE login = '$name'";
		$result1 = $link->query($query);
		if (! $result1) echo message($link -> error);
		else if($result1->num_rows == 0 ){
			$sql = "INSERT INTO user (`login`,  `pswd`) VALUES ('$name', '$pswd')";
			if ( $link -> query($sql)){
				echo message("Регистрация прошла успешно.");
				$b_res true;
			}
			else echo message($link -> error);
		}
		else echo message("Такой пользователь уже существует.");
		mysqli_close($link);
		mysqli_free_result($result1);
	}
	return b_res;
}

function user_login()
{
	$b_res = false;
    $link = connect_it();
    $name = $_POST['login'];
	$pswd = $_POST['pswd'];
    $query = "SELECT * FROM `user` WHERE login = '$name'";
        //echo "<font color=green> Запрос: </font>" . $query;
    $result1 = $link->query($query);
    if (! $result1) echo message($link -> error);
    else
		while($result = $result1->fetch_array())
		{
			if (password_verify($pswd, $result['pswd']))
			{
				echo message("Password is valid!");
				session_start();
				$_SESSION['user_name'] = $result['login'];
				$_SESSION['user_id'] = $result['id'];
				$_SESSION['session_start'] = getdate();
				$b_res = true;
			}
			else echo message("Invalid password.");
		}
    mysqli_close($link);
    mysqli_free_result($result1);
	return $b_res;
}

function user_pswd($login, $pswd)
{
	$b_res = false;
	$link = connect_it();
	$pswd = pswd_in_db($pswd);
 mysqli_free_result($result);
		$query = "UPDATE `user` SET `pswd` = '$pswd' WHERE `login` = '$login'";
	$result = $link -> query($query);
    if (! $result) { echo message($link -> error);}
    else { echo message("Выполнено"); $b_res = true;}
	mysqli_close($link);
   return $b_res;
}
function user_update($login, $win, $draw,$lose)
{
	$b_res = false;
	$link = connect_it();
 mysqli_free_result($result);
		$query = "UPDATE `user` SET `win` = '$win', `draw` = '$draw', `lose` = '$lose' WHERE `login` = '$login'";
	$result = $link -> query($query);
    if (! $result) { echo message($link -> error);}
    else { echo message("Выполнено"); $b_res = true;}
	mysqli_close($link);
   return $b_res;
}
function user_show()
{
    $obj_res = {};
	$link = connect_it();
    $query = "SELECT * FROM `user` WHERE `id` = '" . $_SESSION['user_id'] . "'";
        //echo "<font color=green> Запрос: </font>" . $query;
    $result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error); return false;}
    else {
		while($result = $result1->fetch_array()){
			$obj_res = {'login' = $result['login']; 
			'pswd' = $result['pswd'];
			'win' = $result['win'];
			'lose'  = $result['lose'];
			'draw'  = $result['draw'];}
        }
    }
	mysqli_close($link);
    mysqli_free_result($result1);
	return $obj_res;
}

function user_delete($id)
{
	$b_res = false;
    $link = connect_it();
    $query = "DELETE FROM `user` WHERE `id` = $id";
        //echo "<font color=green> Запрос на удаление записи: </font>" . $query;
    $result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error);}
    else { echo message("Выполнено"); $b_res = true;}
    mysqli_close($link);
	mysqli_free_result($result1);
	return $b_res;
}

function user_all($record_order = false)
{
	$res = [];
    $link = connect_it();
    $query = " SELECT * FROM `user` ORDER BY ";
	if($record_order) $query.= "`win` ASC, `draw` ASC, `lose` DESC, ";
	$query.= "`login` ASC";
        //echo "<font color=green> Запрос: </font>" . $query;
    $result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error); return false;}
    else $res = $result1->fetch_all();
    mysqli_close($link);
    mysqli_free_result($result1);
	return $res;
}

//****************************************************************
//комнаты
//по умолчанию оба игрока = создавший комнату
function room_create()
{
	$b_res = false;
    $link = connect_it();
	$id = $_SESSION['user_id'];
	$query = "INSERT INTO `rooms`(`player1`, `player2`) VALUES ('$id', '$id')";
	$result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error);}
    else { 
		echo message("Выполнено"); 
		while($result = $result1->fetch_array())
		{ //сразу типа заходим
			$_SESSION['room_id'] = $result['id'];
			$_SESSION['room_start'] = $result['created'];
			$b_res = true;
		}		
	}
    mysqli_close($link);
	mysqli_free_result($result1);
	return $b_res;
}

function room_enter($room_id)
{
	$b_res = false;
    $link = connect_it();
	$id = $_SESSION['user_id'];
	$query = "UPDATE `rooms` SET `player2` = '$id' WHERE `id` = '$room_id'";
	$result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error);}
    else { 
		echo message("Выполнено"); 
		while($result = $result1->fetch_array())
		{
			$_SESSION['room_id'] = $result['id'];
			$_SESSION['room_start'] = $result['created'];
			$b_res = true;
		}		
	}
    mysqli_close($link);
	mysqli_free_result($result1);
	return $b_res;
}

function room_delete($room_id)
{
	$b_res = false;
    $link = connect_it();
    $query = "DELETE FROM `rooms` WHERE `id` = $room_id";
        //echo "<font color=green> Запрос на удаление записи: </font>" . $query;
    $result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error);}
    else { echo message("Выполнено"); $b_res = true;}
    mysqli_close($link);
	mysqli_free_result($result1);
	return $b_res;
}

function room_all($free_only = false)
{
	$res = [];
    $link = connect_it();
    $query = "SELECT `rooms`.`id` as room_id, `player1`.`id` AS player1_id, `player1`.`login` AS player1_name,`player2`.`id` AS player2_id,`player2`.`login` AS player2_name, `created`, CONCAT(`player1`.`id`=`player2`.`id`) AS free FROM `user`,`rooms` "
	if($free_only) $query.= "WHERE `player1` = `player2` ";
	else $query.= " Order by `free` DESC";
        //echo "<font color=green> Запрос: </font>" . $query;
    $result1 = $link -> query($query);
    if (! $result1) { echo message($link -> error); return false;}
    else $res = $result1->fetch_all();
    mysqli_close($link);
    mysqli_free_result($result1);
	return $res;
}
?>