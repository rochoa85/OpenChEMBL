<?php
 /*
===============

(c) 2012 EMBL European Molecular Biology Laboratories

This code is licensed under Version 2.0 of the Open Source Initiative Apache License.
URL: http://www.opensource.org/licenses/apache2.0.php 

===============
 */

function convertSMARTS($smarts)
{
	include("../config/config.php");
        $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port"); 	
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
	include("../config/config.php");
        $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port");	
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


function sss($searchOperator, $query, $molformat){
    include("../config/config.php");
    $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port");
    if (!$db) {die("Error in connection: " . pg_last_error());}
    
    $qry_md5 = md5($searchOperator.$query.$molformat);
    $table   = "octmp_sss_".$qry_md5;
    
    // If table exists return name
    $sql_table_exists = "SELECT  relname FROM pg_class r JOIN pg_namespace n ON (relnamespace = n.oid) WHERE relkind = 'r' AND n.nspname = 'public' and relname like '".$table."';";
    $qry_table_exists = pg_query($db, $sql_table_exists);
    $row = pg_fetch_row($qry_table_exists);
    
    // Results already exists
    if($row){
        // Extend life of the table
        $sql_update_tmp_table = "UPDATE octmp_summary SET table_created = now() WHERE table_name = '$row[0]'";
        pg_query($db, $sql_update_tmp_table);
        return $row[0];
    }
    
    // Create entry in octmp_summary
    $sql_load_octmp_summary = "INSERT INTO octmp_summary (table_name, table_created, query_md5) VALUES('$table', now(), '$qry_md5')";
    $qry_load_octmp_summary = pg_query($db, $sql_load_octmp_summary);
    if (!$qry_load_octmp_summary) {die("Error in SQL query: " . pg_last_error());}
    
    // Run sss command
    $sql = "CREATE TABLE $table AS SELECT DISTINCT mr.molregno,mr.m,md.chembl_id FROM mols_rdkit mr, molecule_dictionary md WHERE mr.m $searchOperator '$query'::$molformat AND mr.molregno=md.molregno";
    $result = pg_query($db, $sql);
    if (!$result) {die("Error in SQL query: " . pg_last_error());}

    // Return table name
    return $table;

}

function sim($fingerprint,$similarity, $query){
	include("../config/config.php");
	$db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port");
	if (!$db) {die("Error in connection: " . pg_last_error());}

	$qry_md5 = md5($fingerprint.$similarity.$query);
	$table   = "octmp_sim_".$qry_md5;

	// If table exists return name
	$sql_table_exists = "SELECT  relname FROM pg_class r JOIN pg_namespace n ON (relnamespace = n.oid) WHERE relkind = 'r' AND n.nspname = 'public' and relname like '".$table."';";
	$qry_table_exists = pg_query($db, $sql_table_exists);
	$row = pg_fetch_row($qry_table_exists);

	// Results already exists
	if($row){
		// Extend life of the table
		$sql_update_tmp_table = "UPDATE octmp_summary SET table_created = now() WHERE table_name = '$row[0]'";
		pg_query($db, $sql_update_tmp_table);
		return $row[0];
	}

	// Create entry in octmp_summary
	$sql_load_octmp_summary = "INSERT INTO octmp_summary (table_name, table_created, query_md5) VALUES('$table', now(), '$qry_md5')";
	$qry_load_octmp_summary = pg_query($db, $sql_load_octmp_summary);
	if (!$qry_load_octmp_summary) {die("Error in SQL query: " . pg_last_error());}
    
	// Define query parameters
	$sim_query          = false;
	$fingerprint_method = false;
	$fingerprint_cols   = false;
	
	if($similarity=="Tanimoto") {
		$sim_query="tanimoto_sml";
		$sim_thres="%";
	}elseif($similarity=="Dice") {
		$sim_query="dice_sml";
		$sim_thres="#";
	}
		
	// execute query
	if($fingerprint=="Morgan") {
		$fingerprint_method = "morganbv_fp";
		$fingerprint_cols   = "mfp2";		
	}elseif($fingerprint=="MorganFeat") {
		$fingerprint_method = "featmorganbv_fp";
		$fingerprint_cols   = "ffp2";
	}elseif($fingerprint=="Torsion") {
		$fingerprint_method = "torsionbv_fp";
		$fingerprint_cols   = "torsionbv";
	}elseif($fingerprint=="Atom") {
		$fingerprint_method = "atompairbv_fp";
		$fingerprint_cols   = "atombv";
	}elseif($fingerprint=="RDKit") {
		$fingerprint_method = "rdkit_fp";
		$fingerprint_cols   = "rdkfp";
	}elseif($fingerprint=="Layered") {
		$fingerprint_method = "layered_fp";
		$fingerprint_cols   = "layeredfp";
	}elseif($fingerprint=="MACCS") {
		$fingerprint_method = "maccs_fp";
		$fingerprint_cols   = "maccsfp";
	}
		
	// Run sss command
	$sql = "CREATE TABLE $table AS 
	        SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.$fingerprint_cols,$fingerprint_method('$query'::mol)) sim, md.chembl_id 
	          FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 		     WHERE fr.$fingerprint_cols$sim_thres$fingerprint_method('$query'::mol) 
	           AND mr.molregno=md.molregno 
	           AND mr.molregno=fr.molregno";
		
	$result = pg_query($db, $sql);
	if (!$result) {die("Error in SQL query: " . pg_last_error());}

	// Return table name
	return $table;

}

function deleteTmpTables(){
    include("../config/config.php");
    $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port"); 
    if (!$db) {die("Error in connection: " . pg_last_error());}

    // If table exists return name
    $sql_old_table = "SELECT table_name FROM octmp_summary WHERE table_created < ( now() - interval '20 minutes' );";
    $qry_old_table = pg_query($db, $sql_old_table);
    
    while ($row = pg_fetch_array($qry_old_table)) {
        $sql_delete_old_table = "DELETE FROM octmp_summary WHERE table_name like '$row[0]'";
        pg_query($db, $sql_delete_old_table);

        $sql_drop_old_table = "DROP TABLE $row[0]";
        pg_query($db, $sql_drop_old_table);
    }
}

function jsme(){
	include("../config/config.php");
	
	echo "
	<script type='text/javascript' language='javascript' src='$app2baseapplets/JSME/jsme/jsme.nocache.js'></script>
	<script type='text/javascript'>
		
		//this function will be called after the JavaScriptApplet code has been loaded.
		function jsmeOnLoad() {
			//Instantiate a new JSME:
			//arguments: HTML id, width, height (must be string not number!)
			jsmeApplet = new JSApplet.JSME('container', '380px', '340px', {
				//optional parameters
				'options' : 'query,hydrogens'
			});
			document.JME = jsmeApplet;
		}
	
		//this function will store the molecule in a SMILES format
		function getSmilesSub() {
			var data = document.JME.smiles();
			if (data){
				document.getElementById('jsmeSmartsSub').value = data;
			}	
		}
		
		//this function will store the molecule in a SMILES format
		function getSmilesSim() {
			var data = document.JME.smiles();
			if (data){
				document.getElementById('jsmeSmartsSim').value = data;
			}	
		}
	</script>	
	
	
	<!-- div containg the sketcher -->			

		<div style='display: none;' id='container'></div>";
}

function jsmeDraw($structureCategory){
	include("../config/config.php");
	
	echo "
	<script type='text/javascript' language='javascript' src='$app2baseapplets/JSME/jsme/jsme.nocache.js'></script>
	<script type='text/javascript'>
		
		//this function will be called after the JavaScriptApplet code has been loaded.
		function jsmeOnLoad() {
			//Instantiate a new JSME:
			//arguments: HTML id, width, height (must be string not number!)
			jsmeApplet = new JSApplet.JSME('container', '380px', '340px', {
				//optional parameters
				'options' : 'query,hydrogens'
			});
			document.JME = jsmeApplet;
		}
	
		//this function will store the molecule in a SMILES format
		function getSmilesSub() {
			var data = document.JME.smiles();
			if (data){
				document.getElementById('jsmeSmartsSub').value = data;
			}	
		}
		
		function getSmilesSim() {
			var data = document.JME.smiles();
			if (data){
				document.getElementById('jsmeSmartsSim').value = data;
			}
		}
		
		function getSmilesPro() {
			var data = document.JME.smiles();
			if (data){
				document.getElementById('jsmeSmartsPro').value = data;
			}
		}
	</script>	
	
	
	<!-- div containg the sketcher -->			

		<div id='container'></div>";
		
	if ($structureCategory=="substructure"){
		echo "
			<form name='jsmeSketchSub' method='get' action='substructure_results.php' class='formulario'>
				<input type='text' style='display: none' id='jsmeSmartsSub' name='chemical' size='50'>
				<br/>1. Select one kind of search: 
				<input type='radio' name='match' checked='checked' id='subSub' value='subs' /> Substructure
				<input type='radio' name='match' id='exSub' value='exact' /> Exact <br/> <br/>
				<input type='hidden' name='format' value='SMILES'/>
				<input TYPE='submit' align='left' VALUE='Search' onClick='getSmilesSub();'>
			</form>";
	} elseif ($structureCategory=="similarity"){
		echo "
			<form name='jsmeSketchSim' method='get' action='similarity_results.php' class='formulario'>
					<input type='text' style='display: none' id='jsmeSmartsSim' name='chemical' size='50'>
					<br/>1. Select one kind of fingerprints (Morgan (ECFP-like) by default): 
					<select
						name='fingerprint' id='fingerprint'>
						<option value='Morgan' selected='selected' class='listheader'>Morgan</option>
						<option value='MorganFeat' class='listheader'>Morgan features</option>
						<option value='Torsion' class='listheader'>Topological-Torsion</option>
						<option value='Atom' class='listheader'>Atom-Pair</option>
						<option value='MACCS' class='listheader'>MACCS</option>
					</select>
					<br/><br/> 2. Select one similarity coefficient (Tanimoto by default): 
					<select name='similarity' id='similarity'>
						<option value='Tanimoto' selected='selected' class='listheader'>Tanimoto</option>
						<option value='Dice' class='listheader'>Dice</option>
					</select>
					<input type='hidden' name='format' value='SMILES'/>
					<br/><br/><input TYPE='submit' align='left' VALUE='Search' onClick='getSmilesSim();'>
				</form>";
	} elseif($structureCategory=="property") {
		echo "
		<form name='jsmeSketchPro' method='get' action='property_results.php' class='formulario'>
			<input type='text' style='display:none' id='jsmeSmartsPro' name='chemical' size='50'><br/><br/>
			<input type='hidden' name='format' value='SMILES'/>				
			<input TYPE='submit' align='left' VALUE='Calculate' onClick='getSmilesPro();'>
		</form>";
	}
	
}

function marvinDrawSub(){
	include("../config/config.php");
	
	echo "
		<script type='text/javascript' src='".$app2base."applets/marvin/marvin.js'></script>
				<script type='text/javascript'>
        			msketch_name='MSketchSub';
					msketch_begin('".$app2base."applets/marvin', 540, 480);
					msketch_end();
					
					function exportMolSub(format) {
	               	if(document.MSketchSub != null) {
	               		if(document.getElementById('exSub').checked==true){
	               			
									format='smiles:u';
									document.getElementById('formSpecialSub').value = 'SMILES';               		
	               		}
	                  	var s = document.MSketchSub.getMol(format);
	                     // Convert to local line separator
	                   
	                    	s = unix2local(s);
	                    	document.getElementById('marSmartsSub').value = s;
	                	}else {
	                		alert('Cannot import molecule:\n'+'no JavaScript to Java communication'+'in your browser.\n');
	                	}
	        		}
									
				</script>
				<br/> <a href='http://www.chemaxon.com/'>
				<img src='".$app2base."static/images/app/freeweb-150.gif' width='138 height='30' border='0' style='vertical-align: top'/></a>
				
			
				<form name='marSketchSub' method='get' action='substructure_results.php' class='formulario'>
					<input type='text' style='display: none' id='marSmartsSub' name='chemical' size='50'>
					<br/><br/> 1. Select one kind of search: 
					<input type='radio' name='match' checked='checked' id='subSub' value='subs'/> Substructure <input type='radio'
						name='match' id='exSub' value='exact' />Exact
						<br/><br/> <input type='hidden' name='format' id='formSpecialSub' value='SMARTS' />
					<input TYPE='submit' align='left' VALUE='Search' onClick='exportMolSub('smarts:u');'>
				</form>";
}


function marvinDrawSim(){
	include("../config/config.php");
	
	echo "
		<script type='text/javascript' src='".$app2base."applets/marvin/marvin.js'></script>
				<script type='text/javascript'>
        			msketch_name='MSketch';
					msketch_begin('".$app2base."applets/marvin', 540, 480);
					msketch_end();
					
					function exportMol(format) {
               	if(document.MSketch != null) {
                  	var s = document.MSketch.getMol(format);
                     
                    	s = unix2local(s);
                    	document.getElementById('marSmartsSim').value = s;
                	}else {
                		alert('Cannot import molecule:\n'+'no JavaScript to Java communication'+'in your browser.\n');
           			}
      			}
      		</script>
		<br/> <a href='http://www.chemaxon.com/'>
		<img src='".$app2base."static/images/app/freeweb-150.gif' width='138 height='30' border='0' style='vertical-align: top'/></a>
				
				<form name='marSketchSim' method='get' action='similarity_results.php' class='formulario'>
					<input type='text' style='display:none' id='marSmartsSim' name='chemical' size='50'>
					<br/><br/> 1. Select one kind of fingerprints (Morgan (ECFP-like) by default): 
					<select
						name='fingerprint' id='fingerprint'>
						<option value='Morgan' selected='selected' class='listheader'>Morgan</option>
						<option value='MorganFeat' class='listheader'>Morgan features</option>
						<option value='Torsion' class='listheader'>Topological-Torsion</option>
						<option value='Atom' class='listheader'>Atom-Pair</option>
						<option value='MACCS' class='listheader'>MACCS</option>
					</select>
					<br/><br/> 2. Select one similarity coefficient
					(Tanimoto by default): <select name='similarity' id='similarity'>
						<option value='Tanimoto' selected='selected' class='listheader'>Tanimoto</option>
						<option value='Dice' class='listheader'>Dice</option>
					</select><br/><br/> <input type='hidden' name='format' value='SMARTS'/>
					<input TYPE='submit' align='left' VALUE='Search' onClick='exportMol('smarts:u');'>
				</form>";
}
?>
