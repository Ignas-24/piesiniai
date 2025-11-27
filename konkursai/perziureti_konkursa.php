<?php
session_start();
if (
    !isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai") &&
        ($_SESSION['prev'] != "konkursai/perziureti_konkursa") && ($_SESSION['prev'] != "konkursai/perziureti_paveiksla") && ($_SESSION['prev'] != "konkursai/proctrinti_paveiksla"))
) {
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
    </table>
    <?php
    $konkursas_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($konkursas_id <= 0) {
        echo "Nenurodytas konkursas.";
        exit;
    }

    $stmt = mysqli_prepare($db, "SELECT pavadinimas, aprasas, ikelimo_pradzia, vertinimo_pradzia, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " WHERE id = ?");
    if (!$stmt) {
        echo "Klaida ruošiant užklausą: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        echo "<h3>" . htmlspecialchars($row['pavadinimas']) . "</h3>";
        echo "<b>Aprašas:</b> " . nl2br(htmlspecialchars($row['aprasas'])) . "<br>";
        echo "<b>Įkėlimo pradžia:</b> " . htmlspecialchars($row['ikelimo_pradzia']) . "<br>";
        $vertinimo_pradzia = htmlspecialchars($row['vertinimo_pradzia']);
        echo "<b>Vertinimo pradžia:</b>$vertinimo_pradzia<br>";
        echo "<b>Vertinimo pabaiga:</b> " . htmlspecialchars($row['vertinimo_pabaiga']) . "<br>";
    } else {
        echo "Klaida skaitant konkurso informaciją.";
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_close($stmt);

    if ($vertinimo_pradzia > date('Y-m-d H:i:s')) {
        $vertinimu_kiekis = 0;
    } else {
        $vertinimu_kiekis = 5;
    }

    $sql = "SELECT p.id,
       p.pavadinimas,
       p.komentaras,
       p.ikelimo_data,
       p.failo_vieta,
       v.slapyvardis
        FROM " . TBL_PAVEIKSLAS . " p 
        LEFT JOIN " . TBL_USERS . " v
            ON p.fk_Vartotojasuid = v.uid
            LEFT JOIN " . TBL_VERTINIMAS . " ve
            ON ve.fk_paveikslasId = p.id
        WHERE p.fk_Konkursasid = ?
        GROUP BY p.id, p.pavadinimas, p.komentaras,
                p.ikelimo_data, p.failo_vieta, v.slapyvardis
        HAVING COUNT(ve.id) >= ?
        ORDER BY p.ikelimo_data DESC";

    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        echo "Klaida ruošiant užklausą: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ii", $konkursas_id, $vertinimu_kiekis);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        echo "Klaida nuskaitant paveikslus.";
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        exit;
    }
    if (mysqli_num_rows($result) < 1) {
        echo "<p>Šiame konkurse nėra paveikslų, kurie atitinka reikalavimus.</p>";
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        exit;
    }
    echo "<h3>Paveikslai</h3>";
    echo "<center><table class=\"center\" border='1'>
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
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    ?>
</body>

</html>