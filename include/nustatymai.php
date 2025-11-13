<?php
//nustatymai.php
define("DB_SERVER", "localhost");
define("DB_USER", "stud");
define("DB_PASS", "stud");
define("DB_NAME", "piesiniai");
define("TBL_USERS", "vartotojas");
define("TBL_KONKURSAS", "konkursas");
define("TBL_PAVEIKSLAS", "paveikslas");
define("TBL_VERTINIMAS", "vertinimas");
define("TBL_KOMENTARAS", "komentaras");


// Vartotojų profiliai
$user_roles=array(      // vartotojų rolių vardai ir  atitinkamos userlevel reikšmės
	"Svecias"=>"0",
	"Admin"=>"20",
	"Naudotojas"=>"5",
	"Vertintojas"=>"10",);   
// automatiškai galioja ir vartotojas "guest",rolė "Svečias",  userlevel=0
//   jam irgi galima nurodyti leidžiamas operacijas

define("DEFAULT_LEVEL","Naudotojas");  // kokia rolė priskiriama kai registruojasi
define("ADMIN_LEVEL","Admin");  // jis turi vartotojų valdymo teisę per "Administratoriaus sąsaja"
define("UZBLOKUOTAS","255");      // vartotojas negali prisijungti kol administratorius nepakeis rolės
$uregister="both";  // kaip registruojami vartotojai:
					// self - pats registruojasi, admin - tik ADMIN_LEVEL, both - abu atvejai

// Operacijų meniu
// Automatiškai rodomi punktai "Redaguoti paskyrą" ir "Atsijungti", 
//  							o Administratoriui dar "Administratoriaus sąsaja"
// Kitų operacijų meniu aprašomas kintamuoju $usermenu:
// operacijos pavadinimas
// kokioms rolėms rodoma
// operacijos modulis

$usermenu=array(
    ["Konkursų valdymas",[20],"konkursu_valdymas.php"],
	["Konkursai",[0,5,10,20],"konkursai.php"],
	["Nuotraukų įkėlimas",[5],"ikelimas.php"]
			  ); 

// karkaso vaizdavimą paredaguokite savo stiliumi keisdami top.png (pradinis yra 1027x122, read teisė visiems)
// ir styles.css.
// top.png pageidautina matyti sistemos pavadinimą ir autorių.

