<?php
// procadmin.php  kai adminas keičia vartotojų įgaliojimus ir padaro atžymas lentelėje per admin.php
// ji suformuoja numatytų pakeitimų aiškią lentelę ir prašo patvirtinimo, toliau į procadmindb, kuri įrašys į DB

session_start();
// cia sesijos kontrole
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "admin") && ($_SESSION['prev'] != "procadmin"))) {
	header("Location: logout.php");
	exit;
}

include("include/nustatymai.php");
include("include/functions.php");
$_SESSION['prev'] = "procadmin";

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$sql = "SELECT slapyvardis,role,pilnas_vardas,gimtadienis,sukurta "
	. "FROM " . TBL_USERS . " ORDER BY role DESC,slapyvardis";
$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
	echo "Klaida ruošiant užklausą.";
	mysqli_close($db);
	exit;
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result || (mysqli_num_rows($result) < 1)) {
	mysqli_stmt_close($stmt);
	mysqli_close($db);
	echo "Klaida skaitant lentelę users";
	exit;
}
?>
<html>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
	<title>Administratoriaus sąsaja</title>
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
				<center>
					<font size="5">Vartotojų įgaliojimų pakeitimas</font>
				</center>
			</td>
		</tr>
	</table> <br>
	<form name="vartotojai" action="procadmindb.php" method="post">
		<table class="center" style="width:60%; border-width: 2px; border-style: dotted;">
			<tr>
				<td width="50%">
					[<a href="admin.php">Atgal</a>]</td>
				<td width="40%">Patikrinkite ar teisingi pakeitimai</td>
				<td width="10%"> <input type="submit" value="Atlikti"></td>
			</tr>
		</table> <br>

		<table class="center" border="1" cellspacing="0" cellpadding="3">
			<tr>
				<td><b>Vartotojo vardas</b></td>
				<td><b>Buvusi rolė</b></td>
				<td><b>Nauja rolė</b></td>
			</tr>
			<?php
			$naikpoz = false;   // ar bus naikinamu vartotoju
			while ($row = mysqli_fetch_assoc($result)) {
				$level = $row['role'];
				$user = $row['slapyvardis'];
				$nlevel = $_POST['role_' . $user];
				$naikinti = (isset($_POST['naikinti_' . $user]));
				if ($naikinti || ($nlevel != $level)) {
					$keisti[] = $user;                    // cia isiminti kuriuos keiciam, ka keiciam bus irasyta i $pakeitimai
					echo "<tr><td>" . $user . "</td><td>";    // rodyti sia eilute patvirtinimui
					if ($level == UZBLOKUOTAS)
						echo "Užblokuotas";
					else {
						foreach ($user_roles as $x => $x_value) {
							if ($x_value == $level)
								echo $x;
						}
					}
					echo "</td><td>";
					if ($naikinti) {
						echo "<font color=red>PAŠALINTI</color>";
						$pakeitimai[] = -1; // ir isiminti  kad salinam
						$naikpoz = true;
					} else {
						$pakeitimai[] = $nlevel;    // isiminti i kokia role
						if ($nlevel == UZBLOKUOTAS)
							echo "UŽBLOKUOTAS";
						else {
							foreach ($user_roles as $x => $x_value) {
								if ($x_value == $nlevel)
									echo $x;
							}
						}
					}

					echo "</td></tr>";
				}
			}
			// pakeitimus irasysim i sesija 
			if (empty($keisti)) {
				header("Location:index.php");
				exit;
			}  //nieko nekeicia
			
			$_SESSION['ka_keisti'] = $keisti;
			$_SESSION['pakeitimai'] = $pakeitimai;
			?>
		</table>
	</form>
</body>

</html>