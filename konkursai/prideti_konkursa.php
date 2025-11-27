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

$errors = [];
$pavadinimas = $aprasas = $ikelimo_pradzia = $vertinimo_pradzia = $vertinimo_pabaiga = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userid = $_SESSION['userid'] ?? '';
    if (empty($userid)) {
        header("Location: ../logout.php");
        exit;
    }
    $role_v = (int) $user_roles['Vertintojas'];
    $stmt = mysqli_prepare($db, "SELECT COUNT(*) AS VERTINTOJAI FROM " . TBL_USERS . " WHERE role = ?");
    if (!$stmt) {
        echo "Klaida ruošiant užklausą vertintojai: " . mysqli_error($db);
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $role_v);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $vertintojai_count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    $vertintojai = (int) ($vertintojai_count ?? 0);
    if ($vertintojai < 5) {
        mysqli_close($db);
        header("Location: ../konkursu_valdymas.php");
        exit;
    }

    $pavadinimas = trim($_POST['pavadinimas'] ?? '');
    $aprasas = trim($_POST['aprasas'] ?? '');
    $ikelimo_pradzia = trim($_POST['ikelimo_pradzia'] ?? '');
    $vertinimo_pradzia = trim($_POST['vertinimo_pradzia'] ?? '');
    $vertinimo_pabaiga = trim($_POST['vertinimo_pabaiga'] ?? '');

    $errors = [];

    if ($pavadinimas === '')
        $errors[] = "Pavadinimas privalomas.";

    $d1 = DateTime::createFromFormat('Y-m-d\TH:i', $ikelimo_pradzia);
    $d2 = DateTime::createFromFormat('Y-m-d\TH:i', $vertinimo_pradzia);
    $d3 = DateTime::createFromFormat('Y-m-d\TH:i', $vertinimo_pabaiga);
    if (!$d1)
        $errors[] = "Netinkama įkėlimo pradžios data.";
    if (!$d2)
        $errors[] = "Netinkama vertinimo pradžios data.";
    if (!$d3)
        $errors[] = "Netinkama vertinimo pabaigos data.";
    if ($d1 && $d2 && $d3 && ($d2 < $d1 || $d3 < $d2))
        $errors[] = "Pabaigos data negali būti anksčiau už pradžią.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($db, "INSERT INTO " . TBL_KONKURSAS . " (pavadinimas, aprasas, ikelimo_pradzia, vertinimo_pradzia, vertinimo_pabaiga, fk_Vartotojasuid) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $pavadinimas, $aprasas, $ikelimo_pradzia, $vertinimo_pradzia, $vertinimo_pabaiga, $userid);
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
} else {
    $now = date('Y-m-d\TH:i');
    $ikelimo_pradzia = $ikelimo_pradzia ?: $now;
    $vertinimo_pradzia = $vertinimo_pradzia ?: $now;
    $vertinimo_pabaiga = $vertinimo_pabaiga ?: $now;
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
                value="<?php echo htmlspecialchars($pavadinimas, ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p>Aprašas:<br>
            <textarea name="aprasas" rows="6"
                class="s1"><?php echo htmlspecialchars($aprasas, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </p>
        <p>Įkėlimo pradžia:<br>
            <input type="datetime-local" name="ikelimo_pradzia" class="s1"
                value="<?php echo htmlspecialchars($ikelimo_pradzia, ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p>Vertinimo pradžia:<br>
            <input type="datetime-local" name="vertinimo_pradzia" class="s1"
                value="<?php echo htmlspecialchars($vertinimo_pradzia, ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p>Vertinimo pabaiga:<br>
            <input type="datetime-local" name="vertinimo_pabaiga" class="s1"
                value="<?php echo htmlspecialchars($vertinimo_pabaiga, ENT_QUOTES, 'UTF-8'); ?>">
        </p>
        <p><input type="submit" value="Pridėti konkursą"></p>
    </form>
</body>

</html>
<?php mysqli_close($db); ?>