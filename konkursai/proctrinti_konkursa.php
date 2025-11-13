<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai/trinti_konkursa") && ($_SESSION['prev'] != "konkursai/proctrinti_konkursa"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/proctrinti_konkursa";
include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Nepavyko prisijungti prie DB";
    exit;
}
$konkursas_id = $_POST['konkursas_id'] ?? '';
if (empty($konkursas_id)) {
    echo "Nenurodytas konkursas.";
    exit;
}
$sql = "DELETE FROM " . TBL_PAVEIKSLAS . " WHERE fk_Konkursasid = '$konkursas_id'";
$ok = mysqli_query($db, $sql);
if (!$ok) {
    echo "Klaida trinant paveikslus: " . mysqli_error($db);
    exit;
}
$sql = "DELETE FROM " . TBL_KONKURSAS . " WHERE id = '$konkursas_id'";
$ok = mysqli_query($db, $sql);
if (!$ok) {
    echo "Klaida trinant konkursą: " . mysqli_error($db);
    exit;
}
mysqli_close($db);

$uploadDir = "../uploads/" . $konkursas_id;
if (is_dir($uploadDir)) {
    $files = glob($uploadDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($uploadDir);
}
header("Location: ../konkursu_valdymas.php");
exit;
?>