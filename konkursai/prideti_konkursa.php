<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursu_valdymas") && ($_SESSION['prev'] != "konkursai/prideti_konkursa"))) {
    header("Location: ../logout.php");
    exit;
}

include("../include/nustatymai.php");
include("../include/functions.php");

$_SESSION['prev'] = "konkursai/prideti_konkursa";

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // require logged in user id
    $userid = $_SESSION['userid'] ?? '';
    if (empty($userid)) {
        header("Location: ../logout.php");
        exit;
    }

    $pavadinimas = trim($_POST['pavadinimas'] ?? '');
    $aprasas = trim($_POST['aprasas'] ?? '');
    $pradzia = trim($_POST['pradzia'] ?? '');
    $pabaiga = trim($_POST['pabaiga'] ?? '');

    $errors = [];

    if ($pavadinimas === '')
        $errors[] = "Pavadinimas privalomas.";
    // validate dates YYYY-MM-DD
    $d1 = DateTime::createFromFormat('Y-m-d', $pradzia);
    $d2 = DateTime::createFromFormat('Y-m-d', $pabaiga);
    if (!$d1)
        $errors[] = "Netinkama pradžios data.";
    if (!$d2)
        $errors[] = "Netinkama pabaigos data.";
    if ($d1 && $d2 && $d2 < $d1)
        $errors[] = "Pabaigos data negali būti anksčiau už pradžią.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($db, "INSERT INTO " . TBL_KONKURSAS . " (pavadinimas, aprasas, pradzia, pabaiga, fk_Vartotojasuid) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $pavadinimas, $aprasas, $pradzia, $pabaiga, $userid);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            if ($ok) {
                mysqli_close($db);
                header("Location: ../konkursu_valdymas.php");
                exit;
            } else {
                $errors[] = "Klaida įrašant į DB.";
            }
        } else {
            $errors[] = "Klaida paruošiant užklausą.";
        }
    }
}
?>
<!doctype html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Pridėti konkursą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table>
        <td>
            <tr><a href="../konkursu_valdymas.php">Atgal</a></tr>
        </td>
    </table>
    <h3 style="text-align:center;">Pridėti konkursą</h3>

    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?php foreach ($errors as $e)
                echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . "<br>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="prideti_konkursa.php" style="width:600px;margin:0 auto;">
        <p>Pavadinimas:<br>
            <input type="text" name="pavadinimas" class="s1"
                value="<?php echo htmlspecialchars($pavadinimas ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p>Aprašas:<br>
            <textarea name="aprasas" rows="6"
                class="s1"><?php echo htmlspecialchars($aprasas ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </p>
        <p>Pradžia:<br>
            <input type="date" name="pradzia" class="s1"
                value="<?php echo htmlspecialchars($pradzia ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p>Pabaiga:<br>
            <input type="date" name="pabaiga" class="s1"
                value="<?php echo htmlspecialchars($pabaiga ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p><input type="submit" value="Pridėti konkursą"></p>
    </form>
</body>

</html>
<?php mysqli_close($db); ?>