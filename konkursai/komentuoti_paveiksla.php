<?php
session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "konkursai/perziureti_paveiksla") && ($_SESSION['prev'] != "konkursai/komentuoti_paveiksla")) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/komentuoti_paveiksla";

include("../include/nustatymai.php");

$paveikslas_id = $_POST['paveikslas_id'] ?? '';
$komentaras = $_POST['komentaras'] ?? '';


if (empty($paveikslas_id) || empty($komentaras)) {
    echo "Trūksta komentaro duomenų.";
    exit;
}
$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
$is_guest = $_SESSION['ulevel'] == $user_roles['Svecias'];
if (!$is_guest) {
    $sql = "SELECT slapyvardis from " . TBL_USERS . " WHERE uid = \"" . $_SESSION['userid'] . "\"";
    $result = mysqli_query($db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $autorius = $row['slapyvardis'];
    } else {
        echo "Klaida gaunant vartotojo informaciją.";
        mysqli_close($db);
        exit;
    }
} else {
    $autorius = $_POST['autorius'] ?? '';
    if (empty($autorius)) {
        $autorius = "Svecias";
    }
}
if (!$is_guest) {
    $sql = "INSERT INTO " . TBL_KOMENTARAS . " (autorius, turinys, sukurta, fk_Paveikslasid, fk_Vartotojasuid) 
        VALUES ('$autorius', '$komentaras', NOW(), $paveikslas_id, \"" . $_SESSION['userid'] . "\")";
} else {
    $sql = "INSERT INTO " . TBL_KOMENTARAS . " (autorius, turinys, sukurta, fk_Paveikslasid) 
            VALUES ('$autorius', '$komentaras', NOW(), $paveikslas_id)";
}
if (mysqli_query($db, $sql)) {
    header("Location: perziureti_paveiksla.php?id=$paveikslas_id");
    exit;
} else {
    echo "Klaida pridedant komentarą: " . mysqli_error($db);
}
mysqli_close($db);
?>