<?php
// funkcijos  include/functions.php

function inisession($arg)
{   //valom sesijos kintamuosius
	if ($arg == "full") {
		$_SESSION['message'] = "";
		$_SESSION['user'] = "";
		$_SESSION['ulevel'] = 0;
		$_SESSION['userid'] = 0;
		$_SESSION['umail'] = 0;
	}
	$_SESSION['name_login'] = "";
	$_SESSION['pass_login'] = "";
	$_SESSION['name_error'] = "";
	$_SESSION['pass_error'] = "";
	$_SESSION['fullname_login'] = "";
	$_SESSION['birthday_login'] = "";

}

function checkname($username)
{   // Vartotojo vardo sintakse
	if (!$username || strlen($username = trim($username)) == 0) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neįvestas vartotojo vardas</font>";
		"";
		return false;
	} elseif (!preg_match("/^([0-9a-zA-Z])*$/", $username))  /* Check if username is not alphanumeric */ {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Vartotojo vardas gali būti sudarytas<br>
				&nbsp;&nbsp;tik iš raidžių ir skaičių</font>";
		return false;
	} else
		return true;
}

function checkpass($pwd, $dbpwd)
{     //  slaptazodzio tikrinimas (tik demo: min 4 raides ir/ar skaiciai) ir ar sutampa su DB esanciu
	if (!$pwd || strlen($pwd = trim($pwd)) == 0) {
		$_SESSION['pass_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neįvestas slaptažodis</font>";
		return false;
	} elseif (!preg_match("/^([0-9a-zA-Z])*$/", $pwd))  /* Check if $pass is not alphanumeric */ {
		$_SESSION['pass_error'] = "* Čia slaptažodis gali būti sudarytas<br>&nbsp;&nbsp;tik iš raidžių ir skaičių";
		return false;
	} elseif (strlen($pwd) < 4)  // per trumpas
	{
		$_SESSION['pass_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Slaptažodžio ilgis <4 simbolius</font>";
		return false;
	} elseif ($dbpwd != substr(hash('sha256', $pwd), 5, 32)) {
		$_SESSION['pass_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neteisingas slaptažodis</font>";
		return false;
	} else
		return true;
}

function checkdb($username)
{  // iesko DB pagal varda, grazina {vardas,slaptazodis,lygis,id} ir nustato name_error
	$uname = $upass = $ulevel = $uid = null;
	$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if (!$db) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Klaida prisijungiant prie duomenų bazės</font>";
		return array($uname, $upass, $ulevel, $uid);
	}
	$stmt = mysqli_prepare($db, "SELECT slapyvardis, slaptazodis, role, uid FROM " . TBL_USERS . " WHERE slapyvardis = ?");
	if (!$stmt) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Klaida ruošiant užklausą</font>";
		mysqli_close($db);
		return array($uname, $upass, $ulevel, $uid);
	}
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	if (!$res || (mysqli_num_rows($res) != 1)) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Tokio vartotojo nėra</font>";
		mysqli_stmt_close($stmt);
		mysqli_close($db);
		return array($uname, $upass, $ulevel, $uid);
	}
	$row = mysqli_fetch_assoc($res);
	$uname = $row["slapyvardis"];
	$upass = $row["slaptazodis"];
	$ulevel = $row["role"];
	$uid = $row["uid"];
	mysqli_stmt_close($stmt);
	mysqli_close($db);
	return array($uname, $upass, $ulevel, $uid);
}

function checkfullname($fullname)
{   // Vartotojo pilno vardo sintakse
	if (!$fullname || strlen($fullname = trim($fullname)) == 0) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neįvestas vartotojo pilnas vardas</font>";
		"";
		return false;
	} elseif (strlen($fullname) < 3)  // per trumpas
	{
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Vardas per trumpas</font>";
		return false;
	} else
		return true;
}

function checkbirthdate($date)
{   // Vartotojo gimtadienio sintakse YYYY-MM-DD
	if (!$date || strlen($date = trim($date)) == 0) {
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neįvestas vartotojo gimtadienis</font>";
		"";
		return false;
	} elseif (!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date))  // patikrinam ar YYYY-MM-DD su regex 
	{
		$_SESSION['name_error'] =
			"<font size=\"2\" color=\"#ff0000\">* Neteisinga gimtadienio forma (turi būti YYYY-MM-DD)</font>";
		return false;
	} else
		return true;
}

