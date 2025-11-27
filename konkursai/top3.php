<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai") && ($_SESSION['prev'] != "konkursai/top3"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/top3";
include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
$konkursas_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($konkursas_id <= 0) {
    echo "Nenurodytas konkursas.";
    exit;
}

$sql = "SELECT 
    p.id AS paveikslas_id,
    p.pavadinimas,
    p.failo_vieta,
    p.ikelimo_data,
    vart.slapyvardis,
    vart.gimtadienis,
    ROUND(AVG(v.kompozicija), 2) AS vidutine_kompozicija,
    ROUND(AVG(v.spalvingumas), 2) AS vidutinis_spalvingumas,
    ROUND(AVG(v.temos_atitikimas), 2) AS vidutinis_temos_atitikimas,
    ROUND(AVG((v.kompozicija + v.spalvingumas + v.temos_atitikimas) / 3), 2) AS bendras_vidurkis,
    COUNT(v.id) AS vertinimu_skaicius,
    p.ikelimo_data
FROM 
    " . TBL_PAVEIKSLAS . " p
JOIN 
    " . TBL_VERTINIMAS . " v ON p.id = v.fk_Paveikslasid
JOIN
    " . TBL_USERS . " vart ON p.fk_Vartotojasuid = vart.uid
WHERE 
    p.fk_Konkursasid = ?
GROUP BY 
    p.id, p.pavadinimas, p.failo_vieta, vart.slapyvardis, vart.gimtadienis, p.ikelimo_data
HAVING
    vertinimu_skaicius >= 5
ORDER BY 
    bendras_vidurkis DESC,
    p.ikelimo_data ASC
LIMIT 3;";

$stmt = mysqli_prepare($db, $sql);
if (!$stmt) {
    echo "Klaida ruošiant užklausą.";
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (!$result) {
    echo "Klaida vykdant užklausą.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
if (mysqli_num_rows($result) === 0) {
    echo "Konkurse nėra įvertintų paveikslų.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
?>

<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Top 3 paveikslai</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <a href="../konkursai.php">Atgal į konkursų sąrašą</a><br>
    <center>
        <h2>Top 3 paveikslai konkurse</h2>
        <table class="center" border="1">
            <tr>
                <th>Vieta</th>
                <th>Vartotojo slapyvardis</th>
                <th>Paveikslo pavadinimas</th>
                <th>Paveikslas</th>
                <th>Kompozicija</th>
                <th>Spalvingumas</th>
                <th>Temos atitikimas</th>
                <th>Bendras vidurkis</th>
                <th>Ikėlimo data</th>
            </tr>
            <?php
            $i = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $i . "</td>";
                echo "<td>" . htmlspecialchars($row['slapyvardis']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pavadinimas']) . "</td>";
                echo "<td><img src='../" . htmlspecialchars($row['failo_vieta']) . "' alt='" . htmlspecialchars($row['pavadinimas']) . "'style=\"max-width:500px; max-height:300px;\"></td>";
                echo "<td>" . htmlspecialchars($row['vidutine_kompozicija']) . "</td>";
                echo "<td>" . htmlspecialchars($row['vidutinis_spalvingumas']) . "</td>";
                echo "<td>" . htmlspecialchars($row['vidutinis_temos_atitikimas']) . "</td>";
                echo "<td>" . htmlspecialchars($row['bendras_vidurkis']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ikelimo_data']) . "</td>";
                echo "</tr>";
                $i++;
            }
            mysqli_stmt_close($stmt);
            mysqli_close($db);
            ?>
        </table>
    </center>
</body>

</html>