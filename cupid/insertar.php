<?php
include 'models.php'; 

	$a = Track::find($_POST['id']);
	$a->Name = $_POST['name'];
	$a->Album = intval($_POST['album']);
	$a->Composer = $_POST['composer'];
	$a->Milliseconds = $_POST['milliseconds'];
	$a->Bytes = $_POST['bytes'];
	$a->UnitPrice = $_POST['unitprice'];
	if($a->save()){
		header("Location: index.php");
}
else{
	$a = Track::create($_POST['name'],$_POST['composer'],$_POST['milliseconds'],$_POST['bytes'],$_POST['unitprice'],intval($_POST['album']));
	if($a->save()){
		header("Location: index.php");
	}
}
?>

