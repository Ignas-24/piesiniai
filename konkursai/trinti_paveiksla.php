<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai/perziureti_paveiksla") && ($_SESSION['prev'] != "konkursai/trinti_paveiksla"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/trinti_paveiksla";
include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Nepavyko prisijungti prie DB";
    exit;
}
$paveikslas_id = $_GET['id'] ?? '';
$konkursas_id = $_GET['konkursas_id'] ?? '';
if (empty($paveikslas_id)) {
    echo "Nenurodytas paveikslas.";
    exit;
}
if (empty($konkursas_id)) {
    echo "Nenurodytas konkursas.";
    exit;
}
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Trinti paveikslą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center>
                    <h3>Ar tikrai norite ištrinti paveikslą?</h3>
                </center>
            </td>
        </tr>
        <tr>
            <td>
                <p>Paveikslo informacija</p>
                <?php
                $stmt = mysqli_prepare($db, "SELECT p.pavadinimas, p.komentaras, p.ikelimo_data, p.failo_vieta, u.slapyvardis
                    FROM " . TBL_PAVEIKSLAS . " p
                    LEFT JOIN " . TBL_USERS . " u ON p.fk_Vartotojasuid = u.uid
                    WHERE p.id = ?");
                if (!$stmt) {
                    echo "Klaida ruošiant užklausą: " . mysqli_error($db);
                    mysqli_close($db);
                    exit;
                }
                mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res && mysqli_num_rows($res) > 0) {
                    $row = mysqli_fetch_assoc($res);
                    $pavadinimas = htmlspecialchars($row['pavadinimas']);
                    echo "<b>Pavadinimas:</b> " . $pavadinimas . "<br>";
                    echo "<b>Komentaras:</b> " . nl2br(htmlspecialchars($row['komentaras'])) . "<br>";
                    echo "<b>Įkėlimo data:</b> " . htmlspecialchars($row['ikelimo_data']) . "<br>";
                    $failo_vieta = htmlspecialchars($row['failo_vieta']);
                    echo "<b>Paveikslas:</b><br> <img src=\"../$failo_vieta\" alt=\"$pavadinimas\" style=\"max-width:1000px; max-height:400px;\"><br>";
                    echo "<b>Įkėlėjas:</b> " . htmlspecialchars($row['slapyvardis']) . "<br>";
                } else {
                    echo "Klaida skaitant paveikslo informaciją.";
                    mysqli_stmt_close($stmt);
                    mysqli_close($db);
                    exit;
                }
                mysqli_stmt_close($stmt);
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <form method="post" action="proctrinti_paveiksla.php">
                    <input type="hidden" name="paveikslas_id" value="<?php echo htmlspecialchars($paveikslas_id); ?>">
                    <input type="submit" value="Taip, trinti paveikslą">
                    <?php echo "<a href=\"perziureti_paveiksla.php?id=$paveikslas_id\">Atšaukti</a>" ?>
                </form>

            </td>
        </tr>
    </table>
</body>

</html>