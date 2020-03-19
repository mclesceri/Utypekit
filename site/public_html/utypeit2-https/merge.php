<?php

require_once('src/globals.php');
require_once(SERVICES.'BaseService.php');

$nb = new BaseService();

$query = "SELECT * FROM Order_Content_copy";
$res = $nb->sendAndGetMany($query);

foreach($res AS $r) {
	$search = "SELECT id FROM Order_Content WHERE id='".$r->id."'";
	$get = $nb->sendAndGetOne($search);
	if(!$get) {
		$put = "INSERT INTO Order_Content (";
		foreach($r AS $key=>$val) {
			$put .= $key.',';
		}
		$put = substr($put,0,-1);
		$put .= ") VALUES (";
		foreach($r AS $key=>$val) {
			$put .= "'".$val."',";
		}
		$put = substr($put,0,-1);
		$put .= ')';
		$nb->insertAndGetOne($put);
	}
}

$query = "SELECT * FROM Content_copy";
$res = $nb->sendAndGetMany($query);

foreach($res AS $r) {
	$search = "SELECT id FROM Content WHERE id='".$r->id."'";
	$get = $nb->sendAndGetOne($search);
	if(!$get) {
		$put = "INSERT INTO Content (";
		foreach($r AS $key=>$val) {
			$put .= $key.',';
		}
		$put = substr($put,0,-1);
		$put .= ") VALUES (";
		foreach($r AS $key=>$val) {
			$put .= "'".$val."',";
		}
		$put = substr($put,0,-1);
		$put .= ')';
		$nb->insertAndGetOne($put);
	}
}

?>