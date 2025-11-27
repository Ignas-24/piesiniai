<?php
session_start();
include("../include/nustatymai.php");
if (
    !isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursai/perziureti_paveiksla") &&
        ($_SESSION['prev'] != "konkursai/perziureti_konkursa")
        && ($_SESSION['prev'] != "konkursai/komentuoti_paveiksla")
        && ($_SESSION['prev'] != "konkursai/trinti_paveiksla"))
) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/perziureti_paveiksla";

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Negaliu prisijungti prie DB";
    exit;
}
$paveikslas_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($paveikslas_id <= 0) {
    echo "Nenurodytas paveikslas.";
    exit;
}

$stmt = mysqli_prepare($db, "SELECT p.id, p.pavadinimas, p.komentaras, p.ikelimo_data, p.failo_vieta, u.slapyvardis, p.fk_Konkursasid, u.uid
    FROM " . TBL_PAVEIKSLAS . " p LEFT JOIN " . TBL_USERS . " u
    ON p.fk_Vartotojasuid = u.uid
    WHERE p.id = ? ORDER BY p.ikelimo_data DESC");
if (!$stmt) {
    echo "Klaida ruošiant užklausą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $pavadinimas = htmlspecialchars($row['pavadinimas']);
    $komentaras = nl2br(htmlspecialchars($row['komentaras']));
    $ikelimo_data = htmlspecialchars($row['ikelimo_data']);
    $failo_vieta = htmlspecialchars($row['failo_vieta']);
    $slapyvardis = htmlspecialchars($row['slapyvardis']);
    $konkursas_id = (int) $row['fk_Konkursasid'];
    $ikelejo_uid = htmlspecialchars($row['uid']);
} else {
    echo "Klaida skaitant paveikslo informaciją.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($db, "SELECT vertinimo_pradzia FROM " . TBL_KONKURSAS . " WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $konkursas_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $vertinimo_pradzia = $row['vertinimo_pradzia'];
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Klaida ruošiant užklausą: " . mysqli_error($db);
    mysqli_close($db);
    exit;
}
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Peržiūrėti paveikslą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center><img src="../include/top.png"></center>
            </td>
        </tr>
        <tr>
            <td>
                <a href="../konkursai/perziureti_konkursa.php?id=<?php echo $konkursas_id; ?>">Atgal į konkurso
                    peržiūrą</a>
            </td>
        </tr>

    </table>
    <?php
    if (!empty($_GET['msg']) && $_GET['msg'] === 'vertinta') {
        echo '<p style="color:green;">Paveikslas sėkmingai įvertintas.</p>';
    }
    ?>
    <h3><?php echo $pavadinimas; ?></h3>
    <p><b>Įkėlė:</b> <?php echo $slapyvardis; ?></p>
    <p><b>Įkėlimo data:</b> <?php echo $ikelimo_data; ?></p>
    <p><b>Komentaras:</b> <?php echo $komentaras; ?></p>
    <p><b>Vertinimai</b></p>
    <?php
    $stmt = mysqli_prepare($db, "SELECT AVG(kompozicija) AS avg_kompozicija, AVG(spalvingumas) AS avg_spalvingumas, AVG(temos_atitikimas) AS avg_temos_atitikimas
            FROM " . TBL_VERTINIMAS . " WHERE fk_Paveikslasid = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            $avg_kompozicija = number_format($row['avg_kompozicija'] ?? 0, 2);
            $avg_spalvingumas = number_format($row['avg_spalvingumas'] ?? 0, 2);
            $avg_temos_atitikimas = number_format($row['avg_temos_atitikimas'] ?? 0, 2);
            echo "<p>Kompozicija: " . $avg_kompozicija . " Spalvingumas: " . $avg_spalvingumas . " Temos atitikimas: " . $avg_temos_atitikimas . "</p>";
        } else {
            echo "<p>Klaida skaitant vertinimus.</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Klaida ruošiant užklausą vertinimams.</p>";
    }
    ?>
    <img src="../<?php echo $failo_vieta; ?>" alt="<?php echo $pavadinimas; ?>"
        style="max-width:1000px; max-height:400px;">
    <form action="komentuoti_paveiksla.php" method="post">
        <input type="hidden" name="paveikslas_id" value="<?php echo $paveikslas_id; ?>">
        <label for="komentaras">Palikti komentarą:</label><br>
        <textarea id="komentaras" name="komentaras" rows="2" cols="50" style="width:30%" required></textarea><br>
        <?php
        if ($_SESSION['ulevel'] == $user_roles['Svecias']) {
            echo '<label for="autorius">Vardas:</label><br>';
            echo '<input type="text" id="autorius" style="width:30%" name="autorius"><br>';
        }
        ?>
        <input type="submit" value="Komentuoti">
    </form>
    <?php
    $stmt = mysqli_prepare($db, "SELECT autorius, turinys, sukurta FROM " . TBL_KOMENTARAS . " WHERE fk_Paveikslasid = ? ORDER BY sukurta DESC");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $paveikslas_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && mysqli_num_rows($res) > 0) {
            echo "<h3>Komentarai:</h3>";
            while ($row = mysqli_fetch_assoc($res)) {
                $komentaras = nl2br(htmlspecialchars($row['turinys']));
                $sukurta = htmlspecialchars($row['sukurta']);
                $slapyvardis = htmlspecialchars($row['autorius']);
                echo "<p><b>" . $slapyvardis . " (" . $sukurta . "):</b><br>" . $komentaras . "</p>";
            }
        } else {
            echo "<p>Šiam paveikslui nėra komentarų.</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Klaida ruošiant užklausą komentarams.</p>";
    }

    if ($ikelejo_uid == $_SESSION['userid'] && $vertinimo_pradzia > date('Y-m-d H:i:s')) {
        echo "<a style=\"color:red;\" href=\"trinti_paveiksla.php?id=" . $paveikslas_id . "&konkursas_id=" . $konkursas_id . "\">Ištrinti paveikslą</a><br>";
    }
    mysqli_close($db);
    ?>
</body>

</html>