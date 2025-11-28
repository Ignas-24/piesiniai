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

$konkursas_id = isset($_POST['konkursas_id']) ? (int) $_POST['konkursas_id'] : 0;
if ($konkursas_id <= 0) {
    echo "Nenurodytas konkursas.";
    mysqli_close($db);
    exit;
}

$stmt = mysqli_prepare($db, "DELETE FROM " . TBL_PAVEIKSLAS . " WHERE fk_Konkursasid = ?");
if (!$stmt) {
    echo "Klaida ruošiant užklausą trinant paveikslus: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
if (!mysqli_stmt_execute($stmt)) {
    echo "Klaida trinant paveikslus: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($db, "DELETE FROM " . TBL_KONKURSAS . " WHERE id = ?");
if (!$stmt) {
    echo "Klaida ruošiant užklausą trinant konkursą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
if (!mysqli_stmt_execute($stmt)) {
    echo "Klaida trinant konkursą: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

mysqli_close($db);

$uploadDir = __DIR__ . "/../uploads/" . $konkursas_id;
if (is_dir($uploadDir)) {
    $files = glob($uploadDir . '/*');
    if ($files !== false) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
    @rmdir($uploadDir);
}

header("Location: ../konkursu_perziura.php");
exit;
?>