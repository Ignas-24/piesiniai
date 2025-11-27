<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai/trinti_paveiksla") && ($_SESSION['prev'] != "konkursai/proctrinti_paveiksla"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/proctrinti_paveiksla";
include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Nepavyko prisijungti prie DB";
    exit;
}
$paveikslas_id = isset($_POST['paveikslas_id']) ? (int) $_POST['paveikslas_id'] : 0;
if ($paveikslas_id <= 0) {
    echo "Nenurodytas paveikslas.";
    mysqli_close($db);
    exit;
}

$stmt = mysqli_prepare($db, "SELECT fk_Konkursasid, fk_Vartotojasuid, failo_vieta FROM " . TBL_PAVEIKSLAS . " WHERE id = ?");
if (!$stmt) {
    echo "Klaida ruošiant užklausą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $konkursas_id, $ikelejo_uid, $failo_vieta);
if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    echo "Klaida gaunant paveikslo informaciją.";
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

if ($ikelejo_uid != ($_SESSION['userid'] ?? '') && ($_SESSION['role'] ?? '') != ADMIN_LEVEL) {
    echo "Jūs neturite teisės trinti šį paveikslą.";
    mysqli_close($db);
    exit;
}

$stmt = mysqli_prepare($db, "DELETE FROM " . TBL_PAVEIKSLAS . " WHERE id = ?");
if (!$stmt) {
    echo "Klaida ruošiant trinimo užklausą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
if (!mysqli_stmt_execute($stmt)) {
    echo "Klaida trinant paveikslą: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);
mysqli_close($db);

$path = "../" . (string) $failo_vieta;
if (is_file($path)) {
    @unlink($path);
}

header("Location: perziureti_konkursa.php?id=" . ((int) $konkursas_id));
exit;
?>