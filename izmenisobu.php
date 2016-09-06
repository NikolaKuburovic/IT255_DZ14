<?php
header('Access-Control-Allow-Methods: GET, POST');

include("functions.php");

if(isset($_POST['ime']) && isset($_POST['sprat']) && isset($_POST['kreveti']) &&
 isset($_POST['m2']) && isset($_POST['tv']) && isset($_POST['internet']) && 
 isset($_POST['djakuzi']) && isset($_POST['rezervacija']) && isset($_POST['id'])){
   
    
    $ime = $_POST['ime'];
    $sprat = intval($_POST['sprat']);
    $kreveti = intval($_POST['kreveti']);
    $m2 = intval($_POST['m2']);
    $tv = $_POST['tv'];
    $internet = $_POST['internet'];
    $djakuzi = $_POST['djakuzi'];
    $rezervacija = $_POST['rezervacija'];
    $id = intval($_POST['id']);
    
    
    echo izmeniSobu($ime, $sprat, $kreveti, $m2, $tv, $internet, $djakuzi, $rezervacija, $id);
}
?>
