<?php
session_start();
if (
    isset($_SESSION["prev"]) &
    $_SESSION["prev"] != "konkursai/vertinti_paveiksla" &&
    $_SESSION["prev"] != "konkursai/vertinti_konkursa"
) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION["prev"] = "konkursai/vertinti_paveiksla";

include("../include/nustatymai.php");
$paveikslas_id = $_POST['paveikslas_id'] ?? '';
if (empty($paveikslas_id)) {
    echo "Nenurodytas paveikslas.";
    exit;
}
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Įvertinimas užbaigtas</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <?php echo "<a href=\"../konkursai/vertinti_konkursa.php?id=" . ($_POST['konkursas_id'] ?? '') . "\">Grįžti į konkursą</a><br>"; ?>
</body>

</html>
<?php
$spalvingumas = $_POST['spalvingumas'] ?? '';
$kompozicija = $_POST['kompozicija'] ?? '';
$temos_atitikimas = $_POST['temos_atitikimas'] ?? '';

if (empty($spalvingumas) || empty($kompozicija) || empty($temos_atitikimas)) {
    echo "Trūksta įvertinimo duomenų.";
    exit;
}
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
$sql = "Select COUNT(*) as count FROM " . TBL_VERTINIMAS . " WHERE fk_Paveikslasid = $paveikslas_id AND fk_Vartotojasuid = \"" . $_SESSION['userid'] . "\"";
$result = mysqli_query($db, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row['count'] > 0) {
        echo "Jūs jau įvertinote šį paveikslą.";
        mysqli_close($db);
        exit;
    }
} else {
    echo "Klaida tikrinant ankstesnį įvertinimą.";
    mysqli_close($db);
    exit;
}
$sql = "INSERT INTO " . TBL_VERTINIMAS . " (kompozicija, spalvingumas, temos_atitikimas, sukurta, fk_Paveikslasid, fk_Vartotojasuid) 
        VALUES ($kompozicija, $spalvingumas, $temos_atitikimas, NOW(), $paveikslas_id, \"" . $_SESSION['userid'] . "\")";

if (!mysqli_query($db, $sql)) {
    echo "Klaida įrašant įvertinimą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
$konkursas_id = $_POST['konkursas_id'] ?? '';
mysqli_close($db);
header("Location: ../konkursai/vertinti_konkursa.php?id=$konkursas_id&vertintas=$paveikslas_id&msg=vertinta");
exit;
?>