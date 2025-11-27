<?php
session_start();
if (
    !isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai") &&
        ($_SESSION['prev'] != "konkursai/vertinti_konkursa") && ($_SESSION['prev'] != "konkursai/vertinti_paveiksla"))
) {
    header("Location: ../logout.php");
    exit;
}
include("../include/nustatymai.php");
if($_SESSION['ulevel'] != $user_roles["Vertintojas"]){
    header("Location: ../konkursai.php");
    exit;
}


$_SESSION['prev'] = "konkursai/vertinti_konkursa";
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Vertinti konkursą</title>
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
    $msg = $_GET['msg'] ?? '';
    if(!empty($msg)) {
        $vertintas_paveikslas_id = $_GET['vertintas'] ?? '';
        if(!empty($vertintas_paveikslas_id)) {
            echo "<p style='color:green;'>Paveikslas sėkmingai įvertintas.</p>";
        }
    }
    $sql = "SELECT pavadinimas, aprasas, ikelimo_pradzia, vertinimo_pradzia, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " WHERE id = $konkursas_id";
    $result = mysqli_query($db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<h3>" . htmlspecialchars($row['pavadinimas']) . "</h3>";
        echo "<b>Aprašas:</b> " . nl2br(htmlspecialchars($row['aprasas'])) . "<br>";
        echo "<b>Įkėlimo pradžia:</b> " . htmlspecialchars($row['ikelimo_pradzia']) . "<br>";
        echo "<b>Vertinimo pradžia:</b> " . htmlspecialchars($row['vertinimo_pradzia']) . "<br>";
        echo "<b>Vertinimo pabaiga:</b> " . htmlspecialchars($row['vertinimo_pabaiga']) . "<br>";
    } else {
        echo "Klaida skaitant konkurso informaciją.";
        mysqli_close($db);
        exit;
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
        WHERE p.fk_Konkursasid = $konkursas_id
        GROUP BY p.id, p.pavadinimas, p.komentaras,
                p.ikelimo_data, p.failo_vieta, v.slapyvardis
        ORDER BY p.ikelimo_data DESC";
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
    <th>Vertinimas</th>
    </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $paveikslas_id = $row['id'];
        $pavadinimas = htmlspecialchars($row['pavadinimas']);
        $komentaras = nl2br(htmlspecialchars($row['komentaras']));
        $ikelimo_data = htmlspecialchars($row['ikelimo_data']);
        $slapyvardis = htmlspecialchars($row['slapyvardis']);
        $failo_vieta = htmlspecialchars($row['failo_vieta']);
        echo "<tr>
        <td><center><img src=\"../" . $failo_vieta . "\" alt=\""
            . $pavadinimas . "\" style=\"max-width:1000px; max-height:400px;\"></center></td>
        <td>" . $pavadinimas . "</td>
        <td>" . $komentaras . "</td>
        <td>" . $ikelimo_data . "</td>
        <td>" . $slapyvardis . "</td>
        <td>
        <form action=\"vertinti_paveiksla.php\" method=\"post\">
            <input type=\"hidden\" name=\"paveikslas_id\" value=\"" . $paveikslas_id . "\"> 
            <input type=\"hidden\" name=\"konkursas_id\" value=\"" . $konkursas_id . "\">
            <label for=\"kompozicija_" . $paveikslas_id . "\">Kompozicija:</label>
            <input type=\"number\" id=\"kompozicija_" . $paveikslas_id . "\" name=\"kompozicija\" min=\"1\" max=\"10\" required style=\"width:60px\"><br>
            <label for=\"spalvingumas_" . $paveikslas_id . "\">Spalvingumas:</label>
            <input type=\"number\" id=\"spalvingumas_" . $paveikslas_id . "\" name=\"spalvingumas\" min=\"1\" max=\"10\" required style=\"width:60px\"><br>
            <label for=\"temos_atitikimas_" . $paveikslas_id . "\">Temos atitikimas:</label>
            <input type=\"number\" id=\"temos_atitikimas_" . $paveikslas_id . "\" name=\"temos_atitikimas\" min=\"1\" max=\"10\" required style=\"width:60px\"><br>
            <input type=\"submit\" value=\"Įvertinti\">
        </form>
        </td>";

    }
    echo "</tr>";

    echo "</table></center>";
    mysqli_close($db);
    ?>
</body>

</html>