<?php // функции пользовательского интерфейса 

function headerer()
{ ?>
	
<?php }

function loging_in($page)
{ 
	if(isset($_SESSION['session_uname']))
		echo " <p>Добро пожаловать, " .$_SESSION['session_uname']. ". </p> <form method='post' action='index.php'> <button name='quit'> Выйти из аккаунта </button> </form> <div>";
	else 
	{
		if ($page =='index') echo "<form action='account.php'"; else echo "<form action='" . $_SERVER['PHP_SELF'] ."' ";
		echo "method=\"post\" > <fieldset> <legend> Войти  </legend> <input type='text' name='login' maxlength=20 autofocus ";
		if (isset($_POST['pass'])) echo "value='" . $_POST['login'] . "'";
		echo "> <input type='password' name='pswd' maxlength=20 > <input type='submit' name='pass' value='Зайти' > <p> Ещё не зарегистрированы? <button formaction='nopass.php'> Зарегистрироваться </button> </p> ";
		echo "<a href = 'remember.php'> Забыли пароль? </a> </fieldset></form> ";
	}
}

function menu($page)
{ ?>

	<table>
        <td><center> <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    
		<input type="submit" name="sub" value="Комнаты"  class="<?php if ($page == "index") echo 'on'; else echo 'off' ?>"> 
		<input type="submit" name="sub2" value="Рейтинг" class="<?php if (($page == "store") || ($page =="search") || ($page == 'readmore')) echo 'on'; else echo 'off' ?>"> 
		<input type="submit" name="sub3" value="Аккаунт" class="<?php if ($page == "acc") echo 'on'; else echo 'off' ?>"> 
        </form> </center></td><td>
	<?php loging_in($page); ?> </td></tr></table> <?php
}

function page_contest($page)
{ 
	if (isset($_POST['sub'])) header("Location: index.php?".session_name().'='.session_id());
	else if (isset($_POST['sub2'])) header("Location: rate.php?".session_name().'='.session_id());
	else if (isset($_POST['sub3'])) header("Location: acc.php?".session_name().'='.session_id());

	if(isset($_POST['quit']))
	{
		if (ini_get("session.use_cookies")) 
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]);
		}
		session_unset();

	}

	if (isset($_POST['reg'])) register();
	if (isset($_POST['remember'])) remember_pswd($_POST['email'], $_POST['phone']);
	
	if (!isset($_SESSION['session_uname'])) 
	{
		echo "Пожалуйста, зайдите в свою учетную запись или зарегистрируйтесь <br/>";
		if ($page == "index") readfile ("inc/page1.inc");
	}
	else
	{
		//echo $_SESSION['session_uname'];
		switch ($page)
		{
			case "index": //Главная
				readfile ("inc/page1.inc");
				break;

			case "acc": //Аккаунт
				if (isset($_POST['acc_change'])) acc_change($_POST['fio'], $_POST['login'], $_POST['pswd'], $_POST['address'], $_POST['phone'], $_POST['email'], $_POST['bday'], $_POST['photo']);
				user_acc();
				break;

			case "users": //Пользователи
				if (isset($_POST['u_delete'])) u_delete($_POST['u_delete']);
				if (isset($_POST['u_change'])) u_change($_POST['u_change'], $_POST['dc'], $_POST['role'],  $_POST['since']);
				if (isset($_POST['u_edit'])) u_edit($_POST['u_edit']); else	all_users();
				break;

			default:
				echo ("Ошибка. Вызов данной странцы не должен был быть произведен.");
				break;
		}
	}
}
?>