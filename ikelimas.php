<?php
session_start();
include("include/nustatymai.php");

if (!isset($_SESSION['prev']) || ($_SESSION['ulevel'] != $user_roles["Naudotojas"] && $_SESSION['ulevel'] != $user_roles['Admin'])) {
    header("Location: logout.php");
    exit;
}
$_SESSION['prev'] = "ikelimas";
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Paveikslų įkėlimas</title>
    <link href="include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center><img src="include/top.png"></center>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                include("include/meniu.php");
                ?>
            </td>
        </tr>
    </table>
    <?php
    if (!empty($_GET['msg']) && $_GET['msg'] === 'ok') {
        echo '<p style="color:green;">Įkėlimas sėkmingas.</p>';
    }
    ?>
    <h3>Pasirinkti galeriją</h3>
    <?php
    $konkursas_id = $_GET['konkursas_id'] ?? '';

    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        echo "Nepavyko prisijungti prie duomenų bazės: " . mysqli_connect_error();
        exit();
    }
    $sql = "SELECT id, pavadinimas FROM " . TBL_KONKURSAS . " 
    WHERE " . TBL_KONKURSAS . ".vertinimo_pradzia > NOW()
      AND " . TBL_KONKURSAS . ".ikelimo_pradzia < NOW()
    ORDER BY pavadinimas";
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        echo "Klaida skaitant lentelę konkursas";
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "Klaida skaitant lentelę konkursas";
        exit;
    }
    if ((mysqli_num_rows($result) < 1)) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "<p style='color:red;'>Šiuo metu nėra aktyvių konkursų galerijų įkėlimui.</p>";
        exit;
    }
    ?>
    <form id="ikelimasform" action="procikelimas.php" method="post" enctype="multipart/form-data"
        style="width:600px;margin:0 auto;">
        <p>Galerija:<br>
            <select name="konkursas" class="s1">
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = (int) $row['id'];
                    $pavadinimas = htmlspecialchars($row['pavadinimas'], ENT_QUOTES, 'UTF-8');
                    $selected = ((int) $konkursas_id === $id) ? ' selected' : '';
                    echo "<option value=\"{$id}\"{$selected}>{$pavadinimas}</option>";
                }
                ?>
            </select>
        </p>

        <p>Pavadinimas (paveikslėlio vardas):<br>
            <input type="text" name="pavadinimas" class="s1"
                value="<?php echo htmlspecialchars($_POST['pavadinimas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </p>

        <p>Komentaras:<br>
            <textarea name="komentaras" class="s1"
                rows="4"><?php echo htmlspecialchars($_POST['komentaras'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </p>

        <p>Pasirinkite paveikslėlį:<br>
            <input type="file" name="paveikslelis" id="paveikslelis" accept="image/*">
        </p>

        <p><input type="submit" value="Įkelti paveikslėlį"></p>
    </form>
    <?php
    mysqli_stmt_close($stmt);
    ?>
</body>

</html>