<?php
session_start();
if (!isset($_SESSION['prev']) || (($_SESSION['prev'] != "konkursu_valdymas") && ($_SESSION['prev'] != "konkursai/trinti_konkursa"))) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai/trinti_konkursa";

include("../include/nustatymai.php");

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$db) {
    echo "Nepavyko prisijungti prie DB";
    exit;
}
$konkursas_id = $_GET['id'] ?? '';

if (empty($konkursas_id)) {
    echo "Nenurodytas konkursas.";
    exit;
}
?>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Trinti konkursą</title>
    <link href="../include/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
    <table class="center">
        <tr>
            <td>
                <center>
                    <h3>Ar tikrai norite ištrinti konkursą?</h3>
                </center>
            </td>
        </tr>
        <tr>
            <td>
                <p>Konkurso informacija</p>
                <?php
                $sql = "SELECT pavadinimas, aprasas, pradzia, pabaiga FROM " . TBL_KONKURSAS . " WHERE id = $konkursas_id";
                $result = mysqli_query($db, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    echo "<b>Pavadinimas:</b> " . htmlspecialchars($row['pavadinimas']) . "<br>";
                    echo "<b>Aprašas:</b> " . nl2br(htmlspecialchars($row['aprasas'])) . "<br>";
                    echo "<b>Pradžia:</b> " . htmlspecialchars($row['pradzia']) . "<br>";
                    echo "<b>Pabaiga:</b> " . htmlspecialchars($row['pabaiga']) . "<br>";
                } else {
                    echo "Klaida skaitant konkurso informaciją.";
                    mysqli_close($db);
                    exit;
                    ;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <form method="post" action="proctrinti_konkursa.php">
                    <input type="hidden" name="konkursas_id" value="<?php echo htmlspecialchars($konkursas_id); ?>">
                    <input type="submit" value="Taip, trinti konkursą">
                    <a href="../konkursu_valdymas.php">Atšaukti</a>
                </form>
                
            </td>
        </tr>
    </table>
</body>

</html>