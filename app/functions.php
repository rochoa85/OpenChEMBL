<?php
 /*
===============

(c) 2012 EMBL European Molecular Biology Laboratories

This code is licensed under Version 2.0 of the Open Source Initiative Apache License.
URL: http://www.opensource.org/licenses/apache2.0.php 

===============
 */
?>

<?
/////////////////////////////////////////////////////////
// Some extra PHP functions
/////////////////////////////////////////////////////////


function Conectar($host,$port,$user,$database)
{
	$db=pg_connect("host=$host port=$port user=$user dbname=$database");
	return $db;
}

function convertSMARTS($smarts)
{
	$db = pg_connect("user=user dbname=chembl_14 host=/var/run/postgresql");
 	if (!$db) {die("Error in connection: " . pg_last_error());}
	$sql = "SELECT mol_from_smarts('$smarts')";
 	$result = pg_query($db, $sql);
 	if (!$result) {die("Error in SQL query: " . pg_last_error());}       
	while ($row = pg_fetch_array($result)) {
 		if (empty($row[0])){
 			echo '<center><b>SMARTS no valid</b></center>';
 		}
 		else{
 			$mol=$row[0];
 		}
   } 				
	return $mol;
}

function convertMOL($molfile)
{
	$db = pg_connect("user=user dbname=chembl_14 host=/var/run/postgresql");
 	if (!$db) {die("Error in connection: " . pg_last_error());}
	$sql = "SELECT mol_from_ctab('$molfile'::cstring)";
 	$result = pg_query($db, $sql);
 	if (!$result) {die("Error in SQL query: " . pg_last_error());}       
	while ($row = pg_fetch_array($result)) {
 		if (empty($row[0])){
 			echo '<center><b>MOLFILE no valid</b></center>';
 		}
 		else{
 			$mol=$row[0];
 		}
   } 				
	return $mol;
}

function uriConversion($query){
	$charOriginal=array("!","#","$","&","'","(",")","*","+",",","/",":",";","=","?","@","[","]");
	$charChange=array("%21","%23","%24","%26","%27","%28","%29","%2A","%2B","%2C","%2F","%3A","%3B","%3D","%3F","%40","%5B","%5D");
	$newQuery=str_replace($charOriginal, $charChange, $query);
	return $newQuery;
}
?>
