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
    <a href="./konkursu_valdymas.php">Atgal į valdymą</a>
    <br><br>
	<table class="center" border="1">
		<tr>
			<td colspan="5">
				<h3>
					<center>Konkursų peržiūra</center>
				</h3>
			</td>
		</tr>
		<tr>
			<td>Konkursas</td>
			<td>Įkėlimo Pradžia</td>
			<td>Vertinimo Pradžia</td>
			<td>Vertinimo Pabaiga</td>
			<td>Veiksmai</td>
		</tr>
		<?php
        $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		$sql = "SELECT id, pavadinimas, ikelimo_pradzia, vertinimo_pradzia, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " ORDER BY ikelimo_pradzia DESC";
		$stmt = mysqli_prepare($db, $sql);
		if (!$stmt) {
			echo "Klaida skaitant lentelę konkursai";
			mysqli_close($db);
			exit;
		}
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if (!$result) {
			mysqli_stmt_close($stmt);
			mysqli_close($db);
			echo "Klaida skaitant lentelę konkursai";
			exit;
		}
		while ($row = mysqli_fetch_assoc($result)) {
			$id = $row['id'];
			$pavadinimas = $row['pavadinimas'];
			$ikelimo_pradzia = $row['ikelimo_pradzia'];
			$vertinimo_pradzia = $row['vertinimo_pradzia'];
			$vertinimo_pabaiga = $row['vertinimo_pabaiga'];
			echo "<tr><td>" . $pavadinimas . "</td><td>" . $ikelimo_pradzia . "</td><td>" . $vertinimo_pradzia . "</td><td>" . $vertinimo_pabaiga . "</td>";
			echo "<td><a href=\"konkursai/trinti_konkursa.php?id=" . $id . "\">Trinti</a></td></tr>";

		}
		mysqli_stmt_close($stmt);
		mysqli_close($db);

		?>
	</table>

</body>

</html>