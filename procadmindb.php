<?php
// procadmindb.php   admino nurodytus pakeitimus padaro DB
// $_SESSION['ka_keisti'] kuriuos vartotojus, $_SESSION['pakeitimai'] į kokį userlevel

session_start();
// cia sesijos kontrole: tik is procadmin
if (!isset($_SESSION['prev']) || ($_SESSION['prev'] != "procadmin")) {
  header("Location: logout.php");
  exit;
}

include("include/nustatymai.php");
include("include/functions.php");
$_SESSION['prev'] = "procadmindb";

$db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
$i = 0;
$levels = $_SESSION['pakeitimai'];
foreach ($_SESSION['ka_keisti'] as $user) {
  $level = $levels[$i++];

  if ($level == -1) {

    $stmt = mysqli_prepare($db, "SELECT uid FROM " . TBL_USERS . " WHERE slapyvardis = ?");
    if (!$stmt) {
      echo " DB klaida gaunant vartotojo ID.";
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $userid);
    if (!mysqli_stmt_fetch($stmt)) {
      mysqli_stmt_close($stmt);
      echo " DB klaida gaunant vartotojo ID.";
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($db, "SELECT role FROM " . TBL_USERS . " WHERE slapyvardis = ?");
    if (!$stmt) {
      echo " DB klaida gaunant vartotojo įgaliojimus.";
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $role_row);
    $role_val = null;
    if (mysqli_stmt_fetch($stmt)) {
      $role_val = $role_row;
    }
    mysqli_stmt_close($stmt);

    if ($role_val == $user_roles[ADMIN_LEVEL]) {
      $stmt = mysqli_prepare($db, "SELECT id FROM " . TBL_KONKURSAS . " WHERE fk_Vartotojasuid = ?");
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $userid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
          while ($row = mysqli_fetch_assoc($res)) {
            $konkursas_id = $row['id'];
            $uploadDir = "./uploads/" . $konkursas_id;
            if (is_dir($uploadDir)) {
              $files = glob($uploadDir . '/*');
              if ($files !== false) {
                foreach ($files as $file) {
                  if (is_file($file)) {
                    @unlink($file);
                  }
                }
              }
              @rmdir($uploadDir);
            }
          }
        }
        mysqli_stmt_close($stmt);
      }
    }

    $stmt = mysqli_prepare($db, "DELETE FROM " . TBL_USERS . " WHERE slapyvardis = ?");
    if (!$stmt) {
      echo " DB klaida ruošiant šalinimo užklausą.";
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_bind_param($stmt, "s", $user);
    if (!mysqli_stmt_execute($stmt)) {
      echo " DB klaida šalinant vartotoją: " . mysqli_stmt_error($stmt);
      mysqli_stmt_close($stmt);
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_close($stmt);

  } else {

    $stmt = mysqli_prepare($db, "UPDATE " . TBL_USERS . " SET role = ? WHERE slapyvardis = ?");
    if (!$stmt) {
      echo " DB klaida ruošiant atnaujinimo užklausą.";
      mysqli_close($db);
      exit;
    }
    $level_int = (int) $level;
    mysqli_stmt_bind_param($stmt, "is", $level_int, $user);
    if (!mysqli_stmt_execute($stmt)) {
      echo " DB klaida keičiant vartotojo įgaliojimus: " . mysqli_stmt_error($stmt);
      mysqli_stmt_close($stmt);
      mysqli_close($db);
      exit;
    }
    mysqli_stmt_close($stmt);
  }
}
$_SESSION['message'] = "Pakeitimai atlikti sėkmingai";
header("Location:admin.php");
exit;
?>