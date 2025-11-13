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


    $sql = "SELECT uid FROM " . TBL_USERS . " WHERE slapyvardis='$user'";
    if (!mysqli_query($db, $sql)) {
      echo " DB klaida gaunant vartotojo ID: " . $sql . "<br>" . mysqli_error($db);
      exit;
    }
    $row = mysqli_fetch_assoc(mysqli_query($db, $sql));
    $userid = $row['uid'];
    $sql = "SELECT role FROM " . TBL_USERS . " WHERE slapyvardis='$user'";
    if (!mysqli_query($db, $sql)) {
      echo " DB klaida gaunant vartotojo įgaliojimus: " . $sql . "<br>" . mysqli_error($db);
      exit;
    }
    $row = mysqli_fetch_assoc(mysqli_query($db, $sql));
    if ($row['role'] == $user_roles[ADMIN_LEVEL]) {
      $sql = "SELECT id FROM " . TBL_KONKURSAS . " WHERE fk_Vartotojasuid='$userid'";
      $result = mysqli_query($db, $sql);
      while ($row = mysqli_fetch_assoc($result)) {
        $konkursas_id = $row['id'];
        $uploadDir = "./uploads/" . $konkursas_id;
        if (is_dir($uploadDir)) {
          $files = glob($uploadDir . '/*');
          foreach ($files as $file) {
            if (is_file($file)) {
              unlink($file);
            }
          }
          rmdir($uploadDir);
        }
      }
    }
    $sql = "DELETE FROM " . TBL_USERS . "  WHERE  slapyvardis='$user'";
    if (!mysqli_query($db, $sql)) {
      echo " DB klaida šalinant vartotoją: " . $sql . "<br>" . mysqli_error($db);
      exit;
    }

  } else {
    $sql = "UPDATE " . TBL_USERS . " SET role='$level' WHERE  slapyvardis='$user'";
    if (!mysqli_query($db, $sql)) {
      echo " DB klaida keičiant vartotojo įgaliojimus: " . $sql . "<br>" . mysqli_error($db);
      exit;
    }
  }
}
$_SESSION['message'] = "Pakeitimai atlikti sėkmingai";
header("Location:admin.php");
exit;
