<?php
session_start();
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "konkursai/perziureti_paveiksla") && ($_SESSION['prev'] != "konkursai/komentuoti_paveiksla")) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/komentuoti_paveiksla";

include("../include/nustatymai.php");


$paveikslas_id = isset($_POST['paveikslas_id']) ? (int) $_POST['paveikslas_id'] : 0;
$komentaras = trim($_POST['komentaras'] ?? '');

if ($paveikslas_id <= 0 || $komentaras === '') {
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

    $stmt = mysqli_prepare($db, "SELECT slapyvardis FROM " . TBL_USERS . " WHERE uid = ?");
    if (!$stmt) {
        echo "Klaida ruošiant užklausą: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['userid']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $slapyvardis);
    if (mysqli_stmt_fetch($stmt)) {
        $autorius = $slapyvardis;
    } else {
        echo "Klaida gaunant vartotojo informaciją.";
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_close($stmt);


    $insert_sql = "INSERT INTO " . TBL_KOMENTARAS . " (autorius, turinys, sukurta, fk_Paveikslasid, fk_Vartotojasuid) VALUES (?, ?, NOW(), ?, ?)";
    $stmt = mysqli_prepare($db, $insert_sql);
    if (!$stmt) {
        echo "Klaida ruošiant įterpimo užklausą: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssis", $autorius, $komentaras, $paveikslas_id, $_SESSION['userid']);
} else {
    $autorius = trim($_POST['autorius'] ?? '');
    if ($autorius === '') {
        $autorius = "Svecias";
    }

    $insert_sql = "INSERT INTO " . TBL_KOMENTARAS . " (autorius, turinys, sukurta, fk_Paveikslasid) VALUES (?, ?, NOW(), ?)";
    $stmt = mysqli_prepare($db, $insert_sql);
    if (!$stmt) {
        echo "Klaida ruošiant įterpimo užklausą: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "ssi", $autorius, $komentaras, $paveikslas_id);
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    header("Location: perziureti_paveiksla.php?id=$paveikslas_id");
    exit;
} else {
    echo "Klaida pridedant komentarą: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
?>