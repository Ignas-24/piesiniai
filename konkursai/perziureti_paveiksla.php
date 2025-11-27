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
$paveikslas_id = $_GET['id'] ?? '';
if (empty($paveikslas_id)) {
    echo "Nenurodytas paveikslas.";
    exit;
}
$sql = "SELECT id, pavadinimas, komentaras, ikelimo_data, failo_vieta, vartotojas.slapyvardis, fk_Konkursasid , vartotojas.uid
    FROM " . TBL_PAVEIKSLAS . " LEFT JOIN " . TBL_USERS . " 
    ON " . TBL_PAVEIKSLAS . ".fk_Vartotojasuid =" . TBL_USERS . ".uid 
    WHERE id = $paveikslas_id ORDER BY ikelimo_data DESC";

$result = mysqli_query($db, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $pavadinimas = htmlspecialchars($row['pavadinimas']);
    $komentaras = nl2br(htmlspecialchars($row['komentaras']));
    $ikelimo_data = htmlspecialchars($row['ikelimo_data']);
    $failo_vieta = htmlspecialchars($row['failo_vieta']);
    $slapyvardis = htmlspecialchars($row['slapyvardis']);
    $konkursas_id = htmlspecialchars($row['fk_Konkursasid']);
    $ikelejo_uid = htmlspecialchars($row['uid']);
} else {
    echo "Klaida skaitant paveikslo informaciją.";
    mysqli_close($db);
    exit;
}
$sql = "SELECT vertinimo_pradzia FROM " . TBL_KONKURSAS . " WHERE id = $konkursas_id";
$result = mysqli_query($db, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $vertinimo_pradzia = $row['vertinimo_pradzia'];
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
    $sql = "SELECT AVG(kompozicija) AS avg_kompozicija, AVG(spalvingumas) AS avg_spalvingumas, AVG(temos_atitikimas) AS avg_temos_atitikimas
            FROM " . TBL_VERTINIMAS . "
            WHERE fk_Paveikslasid = $paveikslas_id";
    $result = mysqli_query($db, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $avg_kompozicija = number_format($row['avg_kompozicija'], 2);
        $avg_spalvingumas = number_format($row['avg_spalvingumas'], 2);
        $avg_temos_atitikimas = number_format($row['avg_temos_atitikimas'], 2);
        echo "<p>Kompozicija: " . $avg_kompozicija . " Spalvingumas: " . $avg_spalvingumas . " Temos atitikimas: " . $avg_temos_atitikimas . "</p>";
    } else {
        echo "<p>Klaida skaitant vertinimus.</p>";
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
    $sql = "SELECT autorius, turinys, sukurta
            FROM " . TBL_KOMENTARAS .
        " WHERE fk_Paveikslasid = $paveikslas_id ORDER BY sukurta DESC";
    $result = mysqli_query($db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<h3>Komentarai:</h3>";
        while ($row = mysqli_fetch_assoc($result)) {
            $komentaras = nl2br(htmlspecialchars($row['turinys']));
            $sukurta = htmlspecialchars($row['sukurta']);
            $slapyvardis = htmlspecialchars($row['autorius']);
            echo "<p><b>" . $slapyvardis . " (" . $sukurta . "):</b><br>" . $komentaras . "</p>";
        }
    } else {
        echo "<p>Šiam paveikslui nėra komentarų.</p>";
    }
    if($ikelejo_uid == $_SESSION['userid'] && $vertinimo_pradzia > date('Y-m-d H:i:s')) {
        echo "<a style=\"color:red;\" href=\"trinti_paveiksla.php?id=" . $paveikslas_id . "&konkursas_id=" . $konkursas_id . "\">Ištrinti paveikslą</a><br>";
    }
    mysqli_close($db);
    ?>
</body>

</html>