<?php
include("include/nustatymai.php");
session_start();
if (!isset($_SESSION['prev'])) {
    header("Location: logout.php");
    exit;
}
$_SESSION['prev'] = "konkursai";
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9; text/html; charset=utf-8">
    <title>Konkursai</title>
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
    <br><br>
    <?php
    //pasibaige konkursai
    echo "<table class='center' border='1'>
        <tr>
            <td colspan='4'>
                <h3>
                    <center>Pasibaigę konkursai</center>
                </h3>
            </td>
        </tr>
        <tr>
            <td>Konkursas</td>
            <td>Aprašas</td>
            <td>Vertinimo pabaiga</td>
            <td>Veiksmai</td>
        </tr>";

    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (!$db) {
        echo "Klaida prisijungiant prie DB";
        exit;
    }
    $stmt = mysqli_prepare($db, "SELECT id, pavadinimas, aprasas, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " WHERE vertinimo_pabaiga <= NOW() ORDER BY vertinimo_pabaiga DESC");
    if (!$stmt) {
        echo "Klaida skaitant lentelę konkursai";
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "Klaida skaitant lentelę konkursai";
        exit;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $pavadinimas = $row['pavadinimas'];
        $aprasas = $row['aprasas'];
        $pabaiga = $row['vertinimo_pabaiga'];
        echo
            "<tr><td>" . $pavadinimas . "</td>
            <td>" . $aprasas . "</td>
            <td>" . $pabaiga . "</td>
            <td><a href=\"konkursai/perziureti_konkursa.php?id=" . $id . "\">Peržiūrėti</a><br> 
            <a href=\"konkursai/top3.php?id=" . $id . "\">Top3</a></td></tr>";
    }
    echo "</table>";
    mysqli_stmt_close($stmt);

    //konkursai vertinime
    echo "<br><br>
        <table class='center' border='1'>
        <tr>
            <td colspan='5'>
                <h3>
                    <center>Konkursai vertinime</center>
                </h3>
            </td>
        </tr>
        <tr>
            <td>Konkursas</td>
            <td>Aprašas</td>
            <td>Vertinimo pradžia</td>
            <td>Vertinimo pabaiga</td>";
    if ($_SESSION['ulevel'] == $user_roles["Vertintojas"]) {
        echo "<td>Veiksmai</td>";
    }
    echo "</tr>";

    $stmt = mysqli_prepare($db, "SELECT id, pavadinimas, aprasas, vertinimo_pradzia, vertinimo_pabaiga FROM " . TBL_KONKURSAS . " WHERE vertinimo_pabaiga > NOW() AND vertinimo_pradzia <= NOW() ORDER BY vertinimo_pabaiga DESC");
    if (!$stmt) {
        echo "Klaida skaitant lentelę konkursai";
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "Klaida skaitant lentelę konkursai";
        exit;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $pavadinimas = $row['pavadinimas'];
        $aprasas = $row['aprasas'];
        $pradzia = $row['vertinimo_pradzia'];
        $pabaiga = $row['vertinimo_pabaiga'];
        echo
            "<tr><td>" . $pavadinimas . "</td>
            <td>" . $aprasas . "</td>
            <td>" . $pradzia . "</td>
            <td>" . $pabaiga . "</td>";
        if ($_SESSION['ulevel'] == $user_roles["Vertintojas"]) {
            echo "<td><a href=\"konkursai/vertinti_konkursa.php?id=" . $id . "\">Vertinti</a><br></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    mysqli_stmt_close($stmt);

    //ikelime konkursai
    echo "<br><br>
        <table class='center' border='1'>
        <tr>
            <td colspan='5'>
                <h3>
                    <center>Konkursai įkėlime</center>
                </h3>
            </td>
        </tr>
        <tr>
            <td>Konkursas</td>
            <td>Aprašas</td>
            <td>Įkėlimo pradžia</td>
            <td>Įkėlimo pabaiga</td>
            <td>Veiksmai</td>";

    echo "</tr>";

    $stmt = mysqli_prepare($db, "SELECT id, pavadinimas, aprasas, ikelimo_pradzia, vertinimo_pradzia FROM " . TBL_KONKURSAS . " WHERE ikelimo_pradzia <= NOW() AND vertinimo_pradzia > NOW() ORDER BY ikelimo_pradzia ASC");
    if (!$stmt) {
        echo "Klaida skaitant lentelę konkursai";
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "Klaida skaitant lentelę konkursai";
        exit;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $pavadinimas = $row['pavadinimas'];
        $aprasas = $row['aprasas'];
        $ikelimo_pradzia = $row['ikelimo_pradzia'];
        $vertinimo_pradzia = $row['vertinimo_pradzia'];

        echo
            "<tr><td>" . $pavadinimas . "</td>
            <td>" . $aprasas . "</td>
            <td>" . $ikelimo_pradzia . "</td>
            <td>" . $vertinimo_pradzia . "</td>";
        echo "<td><a href=\"konkursai/perziureti_konkursa.php?id=" . $id . "\">Peržiūrėti</a><br>";
        if ($_SESSION['ulevel'] == $user_roles["Naudotojas"]) {
            echo "<a href=\"ikelimas.php?konkursas_id=" . $id . "\">Įkelti paveikslą</a></td></tr>";
        }
        echo "</tr>";
    }
    echo "</table>";
    mysqli_stmt_close($stmt);

    //neprasideje konkursai
    echo "<br><br>
        <table class='center' border='1'>
        <tr>
            <td colspan='3'>
                <h3>
                    <center>Neprasidėję konkursai</center>
                </h3>
            </td>
        </tr>
        <tr>
            <td>Konkursas</td>
            <td>Aprašas</td>
            <td>Įkėlimo pradžia</td>
        </tr>";

    $stmt = mysqli_prepare($db, "SELECT id, pavadinimas, aprasas, ikelimo_pradzia FROM " . TBL_KONKURSAS . " WHERE ikelimo_pradzia > NOW() ORDER BY ikelimo_pradzia ASC");
    if (!$stmt) {
        echo "Klaida skaitant lentelę konkursai";
        mysqli_close($db);
        exit;
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        mysqli_stmt_close($stmt);
        mysqli_close($db);
        echo "Klaida skaitant lentelę konkursai";
        exit;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        $pavadinimas = $row['pavadinimas'];
        $aprasas = $row['aprasas'];
        $ikelimo_pradzia = $row['ikelimo_pradzia'];

        echo
            "<tr><td>" . $pavadinimas . "</td>
            <td>" . $aprasas . "</td>
            <td>" . $ikelimo_pradzia . "</td>
            </tr>";
    }
    echo "</table>";

    mysqli_stmt_close($stmt);
    mysqli_close($db);
    ?>

</body>

</html>