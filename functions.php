<?php

include("config.php");
 
function checkIfLoggedIn(){
	global $conn;
	if(!empty($_SERVER['HTTP_TOKEN'])){
		$token = $_SERVER['HTTP_TOKEN'];
		$result = $conn->prepare("SELECT * FROM korisnik WHERE token=?");
		$result->bind_param("s",$token);
		$result->execute();
		$result->store_result();

		$num_rows = $result->num_rows;
		if($num_rows > 0)
		{
			return true;
		}
		else{
			return false;
		}
		}
	else{
		return false;
	}
	}

//-------------------------------------------------------------
//POVEZANO SA LOGIN.PHP
//-------------------------------------------------------------
function login($username, $password){
	global $conn;
	$rarray = array();
	if(checkLogin($username,$password)){
		$id = sha1(uniqid());
		$result2 = $conn->prepare("UPDATE korisnik SET token=? WHERE username=?");
		$result2->bind_param("ss",$id,$username);
		$result2->execute();

		$rarray['token'] = $id;
	} else{
		header('HTTP/1.1 401 Unauthorized');
		$rarray['error'] = "Pogresan username/password";
	}
	return json_encode($rarray);
}

//-------------------------------------------------------------
//POVEZANO SA REGSOBA.PHP
//-------------------------------------------------------------
function addRoom($ime, $sprat, $kreveti, $m2, $tv, $internet, $djakuzi, $rezervacija){
	global $conn;
	$rarray = array();
	$stmt = $conn->prepare("INSERT INTO soba (ime, sprat, kreveti, m2, tv, internet, djakuzi, rezervacija) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssssss",$ime, $sprat, $kreveti, $m2, $tv, $internet, $djakuzi, $rezervacija);
	if($stmt->execute()){
		$rarray['sucess'] = "ok";
	}else{
		$rarray['error'] = "Database connection error";
	}
	return json_encode($rarray);
}
//-------------------------------------------------------------
//POVEZANO SA REGHOTEL.PHP
//-------------------------------------------------------------
function addHotel($naziv, $brojSoba, $kategorija, $adresa, $lokacija){
	global $conn;
	$rarray = array();
	$stmt = $conn->prepare("INSERT INTO hotel (naziv, brojSoba, kategorija, adresa, lokacija) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("sssss",$naziv, $brojSoba, $kategorija, $adresa, $lokacija);
	if($stmt->execute()){
		$rarray['sucess'] = "ok";
	}else{
		$rarray['error'] = "Database connection error";
	}
	return json_encode($rarray);
}
//-------------------------------------------------------------
function checkLogin($username, $password){
	global $conn;
	$username = mysqli_real_escape_string($conn,$username);
	$password = md5(mysqli_real_escape_string($conn,$password));
	$result = $conn->prepare("SELECT * FROM korisnik WHERE username=? AND password=?");
	$result->bind_param("ss",$username,$password);
	$result->execute();
	$result->store_result();
	
	$num_rows = $result->num_rows;

	if($num_rows > 0)
	{
		return true;
	}
	else{
		return false;
	}
}

//-------------------------------------------------------------
//POVEZANO SA REGISTER.PHP
//-------------------------------------------------------------
function register($username, $password, $ime, $prezime){
	global $conn;
	$rarray = array();
	$errors = "";
	if(checkIfUserExists($username)){
		$errors .= "Korisnicko ime vec postoji\r\n";
	}
	if(strlen($username) < 5){
		$errors .= "Korisnicko ime mora da ima najmanje 5 karaktera\r\n";
	}
	if(strlen($password) < 5){
		$errors .= "Lozinka mora da ima najmanje 5 karaktera\r\n";
	}
	if(strlen($ime) < 3){
		$errors .= "Ime mora da ima najmanje 3 karaktera\r\n";
	}
	if(strlen($prezime) < 3){
		$errors .= "Prezime mora da ima najmanje 3 karaktera\r\n";
	}
	if($errors == ""){
		$stmt = $conn->prepare("INSERT INTO korisnik (ime, prezime, username, password) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $ime, $prezime, $username, md5($password));
		if($stmt->execute()){
			$id = sha1(uniqid());
			$result2 = $conn->prepare("UPDATE korisnik SET token=? WHERE username=?");
			$result2->bind_param("ss",$id,$username);
			$result2->execute();

			$rarray['token'] = $id;
		}else{
			header('HTTP/1.1 400 Bad request');
			$rarray['error'] = "Database connection error";
		}
	} else{
		header('HTTP/1.1 400 Bad request');
		$rarray['error'] = json_encode($errors);
	}
	return json_encode($rarray);
}
//-------------------------------------------------------------
function checkIfUserExists($username){
	global $conn;
	$result = $conn->prepare("SELECT * FROM korisnik WHERE username=?");
	$result->bind_param("s",$username);
	$result->execute();
	$result->store_result();

	$num_rows = $result->num_rows;
	if($num_rows > 0)
	{
		return true;
	}
	else{
		return false;
	}
}
//-------------------------------------------------------------
function checkId($id){
	global $conn;
	$id = mysqli_real_escape_string($conn,$id);
	$result = $conn->prepare("SELECT * FROM soba WHERE id=?");
	$result->bind_param("i",$id);
	$result->execute();
	$result->store_result();
	
	$num_rows = $result->num_rows;

	if($num_rows > 0)
	{
		
		return true;
	}
	else{
		echo "Result: " + $id;
		return false;
	}
}
//-------------------------------------------------------------
//POVEZANO SA OBRISISOBU.PHP
//-------------------------------------------------------------

function obrisiSobu($id){
	global $conn;
	$rarray = array();
	if(checkIfLoggedIn()){
		
		if(checkId($id)){
		$result = $conn->prepare("DELETE FROM soba WHERE id=?");
		$result->bind_param("i",$id);
		$result->execute();
		$rarray['success'] = "Uspesno obrisana soba";
		}
		else{
			$rarray['error'] = "Ne postoji broj sobe koji ste uneli";
		}
	} else{
		$rarray['error'] = "Please log in";
		header('HTTP/1.1 401 Unauthorized');
	}
	return json_encode($rarray);
}

//-------------------------------------------------------------
//POVEZANO SA IZMENISOBU.PHP
//-------------------------------------------------------------

function izmeniSobu($ime, $sprat, $kreveti, $m2, $tv, $internet, $djakuzi, $rezervacija, $id){
	global $conn;
	$rarray = array();
	if(checkIfLoggedIn()){
		$stmt = $conn->prepare("UPDATE soba SET ime=?, sprat=?, kreveti=?, m2=?, tv=?, internet=?, 
		djakuzi=?, rezervacija=? WHERE id=?");
		$stmt->bind_param("siiissssi", $ime, $sprat, $kreveti, $m2, $tv, $internet, $djakuzi, $rezervacija, $id);
		if($stmt->execute()){
			$rarray['success'] = "updated";
		}else{
			$rarray['error'] = "Database connection error";
		}
	} 
	else{
		$rarray['error'] = "Please log in";	
		header('HTTP/1.1 401 Unauthorized');
	}
	return json_encode($rarray);
}

?>