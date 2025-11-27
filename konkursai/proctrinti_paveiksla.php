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
$paveikslas_id = $_POST['paveikslas_id'] ?? '';
if (empty($paveikslas_id)) {
    echo "Nenurodytas paveikslas.";
    exit;
}
$sql = "SELECT fk_Konkursasid, fk_Vartotojasuid, failo_vieta FROM " . TBL_PAVEIKSLAS . " WHERE id = '$paveikslas_id'";
$result = mysqli_query($db, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $konkursas_id = $row['fk_Konkursasid'];
    $ikelejo_uid = $row['fk_Vartotojasuid'];
    $failo_vieta = $row['failo_vieta'];
} else {
    echo "Klaida gaunant paveikslo informaciją.";
    exit;
}
if($ikelejo_uid != $_SESSION['userid'] && $_SESSION['role'] != ADMIN_LEVEL) {
    echo "Jūs neturite teisės trinti šį paveikslą.";
    exit;
}
$sql = "DELETE FROM " . TBL_PAVEIKSLAS . " WHERE id = '$paveikslas_id'";
$ok = mysqli_query($db, $sql);
if (!$ok) {
    echo "Klaida trinant paveikslus: " . mysqli_error($db);
    exit;
}
mysqli_close($db);

if (is_file("../" . $failo_vieta)) {
    unlink("../" . $failo_vieta);
}
    
header("Location: perziureti_konkursa.php?id=$konkursas_id");
exit;
?>