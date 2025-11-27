<?php
session_start();
include("include/nustatymai.php");
include("include/functions.php");

if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "ikelimas") && ($_SESSION['prev'] != "procikelimas"))) {
    header("Location: logout.php");
    exit;
}

$_SESSION['prev'] = "procikelimas";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['paveikslelis'])) {
    echo "Nėra įkeltas paveikslelis.";
    exit;
}
$file = $_FILES['paveikslelis'];

$allowed_types = ['image/jpeg', 'image/png'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo "Klaida įkeliant paveikslėlį (kodas: {$file['error']}).";
    exit;
}

//file type check
if (!in_array($file['type'], $allowed_types)) {
    echo "Leidžiami tik JPEG ir PNG paveikslėliai.";
    exit;
}
//check comment and name
$komentaras = trim($_POST['komentaras'] ?? '');
$pavadinimas = trim($_POST['pavadinimas'] ?? '');
$konkursas = trim($_POST['konkursas'] ?? '');
if ($pavadinimas === '') {
    echo "Pavadinimas privalomas.";
    exit;
}
if ($konkursas === '') {
    echo "Nepasirinktas konkursas.";
    exit;
}
$konkursas_id = (int) $konkursas;
if ($konkursas_id <= 0) {
    echo "Neteisingas konkurso identifikatorius.";
    exit;
}

// prepare upload directory
$uploadBase = __DIR__ . '/uploads';
$uploadDir = $uploadBase . '/' . $konkursas_id;
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo "Nepavyko sukurti aplanko įkėlimams.";
        exit;
    }
} else {
    @chmod($uploadDir, 0777);
}

// unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = bin2hex(random_bytes(10)) . '.' . $ext;
$destination = $uploadDir . '/' . $filename;

if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $destination)) {
    echo "Nepavyko išsaugoti įkelto failo.";
    exit;
}

@chmod($destination, 0777);

$relativePath = 'uploads/' . $konkursas_id . '/' . $filename;

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    @unlink($destination);
    echo "DB ryšio klaida.";
    exit;
}

$uploader = $_SESSION['userid'] ?? '';

$stmt = mysqli_prepare($db, "INSERT INTO " . TBL_PAVEIKSLAS . " (pavadinimas, komentaras, ikelimo_data, failo_vieta, fk_Vartotojasuid, fk_Konkursasid) VALUES (?, ?, NOW(), ?, ?, ?)");
if (!$stmt) {
    @unlink($destination);
    echo "Klaida ruošiant užklausą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "ssssi", $pavadinimas, $komentaras, $relativePath, $uploader, $konkursas_id);
if (!mysqli_stmt_execute($stmt)) {
    @unlink($destination);
    echo "Klaida įrašant į DB: " . mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);
mysqli_close($db);

header("Location: ikelimas.php?msg=ok");
exit;
?>