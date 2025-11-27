<?php
session_start();
if (
    !isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai/vertinti_paveiksla") &&
        ($_SESSION['prev'] != "konkursai/vertinti_konkursa"))
) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION["prev"] = "konkursai/vertinti_paveiksla";

include("../include/nustatymai.php");

$paveikslas_id = isset($_POST['paveikslas_id']) ? (int) $_POST['paveikslas_id'] : 0;
if ($paveikslas_id <= 0) {
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
    <?php echo "<a href=\"../konkursai/vertinti_konkursa.php?id=" . htmlspecialchars($_POST['konkursas_id'] ?? '') . "\">Grįžti į konkursą</a><br>"; ?>
</body>

</html>
<?php
$spalvingumas = isset($_POST['spalvingumas']) ? (int) $_POST['spalvingumas'] : 0;
$kompozicija = isset($_POST['kompozicija']) ? (int) $_POST['kompozicija'] : 0;
$temos_atitikimas = isset($_POST['temos_atitikimas']) ? (int) $_POST['temos_atitikimas'] : 0;

if ($spalvingumas < 1 || $spalvingumas > 10 || $kompozicija < 1 || $kompozicija > 10 || $temos_atitikimas < 1 || $temos_atitikimas > 10) {
    echo "Trūksta arba netinkami įvertinimo duomenys.";
    exit;
}

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}

$stmt = mysqli_prepare($db, "SELECT COUNT(*) FROM " . TBL_VERTINIMAS . " WHERE fk_Paveikslasid = ? AND fk_Vartotojasuid = ?");
if (!$stmt) {
    echo "Klaida ruošiant užklausą.";
    mysqli_close($db);
    exit;
}
$userid = $_SESSION['userid'] ?? '';
mysqli_stmt_bind_param($stmt, "is", $paveikslas_id, $userid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $existing_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
if ((int) $existing_count > 0) {
    echo "Jūs jau įvertinote šį paveikslą.";
    mysqli_close($db);
    exit;
}

$insert = mysqli_prepare($db, "INSERT INTO " . TBL_VERTINIMAS . " (kompozicija, spalvingumas, temos_atitikimas, sukurta, fk_Paveikslasid, fk_Vartotojasuid) VALUES (?, ?, ?, NOW(), ?, ?)");
if (!$insert) {
    echo "Klaida ruošiant įterpimo užklausą.";
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($insert, "iiiis", $kompozicija, $spalvingumas, $temos_atitikimas, $paveikslas_id, $userid);
if (!mysqli_stmt_execute($insert)) {
    echo "Klaida įrašant įvertinimą: " . mysqli_stmt_error($insert);
    mysqli_stmt_close($insert);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($insert);

$konkursas_id = isset($_POST['konkursas_id']) ? (int) $_POST['konkursas_id'] : 0;
mysqli_close($db);
header("Location: ../konkursai/vertinti_konkursa.php?id=" . $konkursas_id . "&vertintas=" . $paveikslas_id . "&msg=vertinta");
exit;
?>