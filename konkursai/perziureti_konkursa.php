<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai") && 
($_SESSION['prev'] != "konkursai/perziureti_konkursa") && ($_SESSION['prev'] != "konkursai/perziureti_paveiksla"))) {
    header("Location: ../logout.php");
    exit;
}

include("../include/nustatymai.php");

$_SESSION['prev'] = "konkursai/perziureti_konkursa";
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Peržiūrėti konkursą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center><img src="../include/top.png"></center>
            </td>
        </tr>
        <tr>
            <td>
                <a href="../konkursai.php">Atgal į konkursų sąrašą</a>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                // include("../include/meniu.php"); //įterpiamas meniu pagal vartotojo rolę
                ?>
            </td>
        </tr>
    </table>
    <?php
    $konkursas_id = $_GET['id'] ?? '';

    if (empty($konkursas_id)) {
        echo "Nenurodytas konkursas.";
        exit;
    }

    $sql = "SELECT pavadinimas, aprasas, pradzia, pabaiga FROM " . TBL_KONKURSAS . " WHERE id = $konkursas_id";
    $result = mysqli_query($db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<h3>" . htmlspecialchars($row['pavadinimas']) . "</h3>";
        echo "<b>Aprašas:</b> " . nl2br(htmlspecialchars($row['aprasas'])) . "<br>";
        echo "<b>Pradžia:</b> " . htmlspecialchars($row['pradzia']) . "<br>";
        echo "<b>Pabaiga:</b> " . htmlspecialchars($row['pabaiga']) . "<br>";
    } else {
        echo "Klaida skaitant konkurso informaciją.";
        mysqli_close($db);
        exit;
    }
    $sql = "SELECT id, pavadinimas, komentaras, ikelimo_data, failo_vieta, vartotojas.slapyvardis 
    FROM " . TBL_PAVEIKSLAS . " LEFT JOIN " . TBL_USERS . " 
    ON " . TBL_PAVEIKSLAS . ".fk_Vartotojasuid =" . TBL_USERS . ".uid 
    WHERE fk_Konkursasid = $konkursas_id ORDER BY ikelimo_data DESC";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        echo "Klaida nuskaitant paveikslus.";
        exit;
    }
    if (mysqli_num_rows($result) < 1) {
        echo "<p>Šiame konkurse nėra paveikslų.</p>";
        mysqli_close($db);
        exit;
    }
    echo "<h3>Paveikslai</h3>";
    echo "<center><table border='1'>
    <tr>
    <th>Paveikslas</th>
    <th>Pavadinimas</th>
    <th>Komentaras</th>
    <th>Įkėlimo data</th>
    <th>Įkėlė</th>
    </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $paveikslas_id = $row['id'];
        $pavadinimas = htmlspecialchars($row['pavadinimas']);
        $komentaras = nl2br(htmlspecialchars($row['komentaras']));
        $ikelimo_data = htmlspecialchars($row['ikelimo_data']);
        $slapyvardis = htmlspecialchars($row['slapyvardis']);
        $failo_vieta = htmlspecialchars($row['failo_vieta']);
        echo "<tr>
        <td><center><a href=\"../konkursai/perziureti_paveiksla.php?id=" . $paveikslas_id . "\"><img src=\"../" . $failo_vieta . "\" alt=\""
            . $pavadinimas . "\" style=\"max-width:1000px; max-height:400px;\"></a></center></td>
        <td>" . $pavadinimas . "</td>
        <td>" . $komentaras . "</td>
        <td>" . $ikelimo_data . "</td>
        <td>" . $slapyvardis . "</td>
        </tr>";
    }
    echo "</table></center>";
    mysqli_close($db);
    ?>
</body>

</html>