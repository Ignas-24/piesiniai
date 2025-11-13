<?php
include("include/nustatymai.php");
session_start();
if (!isset($_SESSION['prev'])) {
	header("Location: logout.php");
	exit;
}
$_SESSION['prev'] = "konkursai";
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
				include("include/meniu.php"); //įterpiamas meniu pagal vartotojo rolę
				?>
			</td>
		</tr>
	</table>
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
			<td>Aprašas</td>
			<td>Pradžia</td>
			<td>Pabaiga</td>
			<td>Veiksmai</td>
		</tr>
		<?php
		$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		$sql = "SELECT id, pavadinimas, aprasas, pradzia, pabaiga FROM " . TBL_KONKURSAS . " ORDER BY pradzia DESC";
		$result = mysqli_query($db, $sql);
		if (!$result) {
			echo "Klaida skaitant lentelę konkursai";
			exit;
		}
		while ($row = mysqli_fetch_assoc($result)) {
			$id = $row['id'];
			$pavadinimas = $row['pavadinimas'];
			$aprasas = $row['aprasas'];
			$pradzia = $row['pradzia'];
			$pabaiga = $row['pabaiga'];
			echo 
			"<tr><td>" . $pavadinimas . "</td>
			<td>" . $aprasas . "</td>
			<td>" . $pradzia . "</td>
			<td>" . $pabaiga . "</td>
			<td><a href=\"konkursai/perziureti_konkursa.php?id=" . $id . "\">Peržiūrėti</a><br> 
			<a href=\"konkursai/top3.php?id=".$id."\">Top3</a></td></tr>";

		}
		mysqli_close($db);

		?>
	</table>

</body>

</html>