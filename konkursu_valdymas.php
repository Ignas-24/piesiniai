<?php
include("include/nustatymai.php");
session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['ulevel'] != $user_roles[ADMIN_LEVEL])) {
	header("Location: logout.php");
	exit;
}
$_SESSION['prev'] = "konkursu_valdymas";
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
	<title>Operacija 3</title>
	<link href="include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
	<table class="center">
		<tr>
			<td>
				<center><img src="include/top.png"></center>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				include("include/meniu.php");
				?>
			</td>
		</tr>
	</table>
	<br><br>
	<table class="center" border="1">
		<tr>
			<td colspan="5">
				<h3>
					<center>Konkursų valdymas</center>
				</h3>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<?php
				$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
				$sql = "SELECT COUNT(*) AS VERTINTOJAI FROM " . TBL_USERS . " WHERE role = ?";
				$stmt = mysqli_prepare($db, $sql);
				if (!$stmt) {
					echo "Klaida skaitant lentelę vertintojai";
					mysqli_close($db);
					exit;
				}
				$role_v = (int) $user_roles['Vertintojas'];
				mysqli_stmt_bind_param($stmt, "i", $role_v);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt, $vertintojai_count);
				mysqli_stmt_fetch($stmt);
				mysqli_stmt_close($stmt);
				$vertintojai = (int) ($vertintojai_count ?? 0);
				if ($vertintojai >= 5) {
					echo '<center><a href="konkursai/prideti_konkursa.php">Pridėti konkursą</a></center>';
				} else {
					echo '<center>Norint pridėti konkursą, reikia turėti bent 5 vertintojus</center>';
				}
			?>
			</td>
		</tr>
		<tr>
			<td>
				<center><a href="./konkursu_perziura.php">Peržiūrėti konkursus</a></center>
			</td>
		</tr>
	</table>

</body>

</html>