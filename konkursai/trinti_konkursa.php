<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursu_valdymas") && ($_SESSION['prev'] != "konkursai/trinti_konkursa"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/trinti_konkursa";

include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Nepavyko prisijungti prie DB";
    exit;
}
$konkursas_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($konkursas_id <= 0) {
    echo "Nenurodytas konkursas.";
    exit;
}
$stmt = mysqli_prepare($db, "SELECT pavadinimas, aprasas, ikelimo_pradzia, vertinimo_pradzia, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " WHERE id = ?");
if (!$stmt) {
    echo "Klaida ruošiant užklausą.";
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $pavadinimas = htmlspecialchars($row['pavadinimas']);
    $aprasas = nl2br(htmlspecialchars($row['aprasas']));
    $ikelimo_pradzia = htmlspecialchars($row['ikelimo_pradzia']);
    $vertinimo_pradzia = htmlspecialchars($row['vertinimo_pradzia']);
    $vertinimo_pabaiga = htmlspecialchars($row['vertinimo_pabaiga']);
} else {
    echo "Klaida skaitant konkurso informaciją.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Trinti konkursą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center>
                    <h3>Ar tikrai norite ištrinti konkursą?</h3>
                </center>
            </td>
        </tr>
        <tr>
            <td>
                <p>Konkurso informacija</p>
                <?php
                echo "<b>Pavadinimas:</b> " . $pavadinimas . "<br>";
                echo "<b>Aprašas:</b> " . $aprasas . "<br>";
                echo "<b>Įkėlimo pradžia:</b> " . $ikelimo_pradzia . "<br>";
                echo "<b>Vertinimo pradžia:</b> " . $vertinimo_pradzia . "<br>";
                echo "<b>Vertinimo pabaiga:</b> " . $vertinimo_pabaiga . "<br>";
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <form method="post" action="proctrinti_konkursa.php">
                    <input type="hidden" name="konkursas_id" value="<?php echo (int) $konkursas_id; ?>">
                    <input type="submit" value="Taip, trinti konkursą">
                    <a href="../konkursu_valdymas.php">Atšaukti</a>
                </form>
            </td>
        </tr>
    </table>
</body>

</html>