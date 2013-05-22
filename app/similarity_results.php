<?php
 /*
===============

(c) 2012 EMBL European Molecular Biology Laboratories

This code is licensed under Version 2.0 of the Open Source Initiative Apache License.
URL: http://www.opensource.org/licenses/apache2.0.php 

===============
 */
?>

<? include_once("functions.php"); ?>

<? include "header.php"; ?> 
               
    <div id="content" role="main" class="grid_24 clearfix">
    		   
	   <section>
    		<h2>Similarity results</h2>
    		
    	     <h3>Query details</h3>    			

             <p>
             
             <?
				if (!empty($_GET["chemical"])) {
					$query=$_GET["chemical"];
					$fingerprint=$_GET["fingerprint"];
					$similarity=$_GET["similarity"];
					$queryFormat=$_GET["format"];
				}
			
				if (!empty($_FILES["datafile"])) {   			
   				$queryMol=$_FILES["datafile"];
   				$fingerprint=$_POST["fingerprint"];
   				$similarity=$_POST["similarity"];
   				$queryFormat=$_POST["format"];
				}
			
 			   if($queryFormat=="SMILES"){
					echo "<b>Query:</b> $query <br/>";
					echo "<b>Fingerprint:</b> $fingerprint <br/>";
					echo "<b>Similarity Coefficient:</b> $similarity <br/>";
				}
				if($queryFormat=="SMARTS"){
					echo "<b>Query</b> $query <br/>";
					echo "<b>Fingerprint:</b> $fingerprint <br/>";
					echo "<b>Similarity Coefficient:</b> $similarity <br/>";
					$query=convertSMARTS($query);	
				}
 			
				if($queryFormat=="MOL"){
					$molecule=file_get_contents($queryMol["tmp_name"]);
					$query=convertMOL($molecule);
					echo "<b>QUERY DETAILS</b><br/><br/>";
					echo "<b>Query (MolFile):</b> ".$queryMol["name"]."<br/>";
					echo "<b>Fingerprint:</b> $fingerprint <br/>";
					echo "<b>Similarity Coefficient:</b> $similarity <br/>";
				}
			?>		
             </p>
             
    			
    	     <h3>Results</h3>    			
             <p>
             <?
         if($similarity=="Tanimoto") {
				$sim_query="tanimoto_sml";
			}elseif($similarity=="Dice") {
				$sim_query="dice_sml";
			}
				
            $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port");
			if (!$db) {die("Error in connection: " . pg_last_error());}
 			
			//echo "<b>SUMMARY RESULTS</b> (max 5 results per page)<br/><br/>";
			 // execute query
			if($fingerprint=="Morgan") {			
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.mfp2,morganbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.mfp2%morganbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}elseif($fingerprint=="MorganFeat") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.ffp2,featmorganbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.ffp2%featmorganbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}elseif($fingerprint=="Torsion") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.torsionbv,torsionbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.torsionbv%torsionbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
			}elseif($fingerprint=="Atom") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.atombv,atompairbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.atombv%atompairbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}elseif($fingerprint=="RDKit") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.rdkfp,rdkit_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.rdkfp%rdkit_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}elseif($fingerprint=="Layered") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.layeredfp,layered_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.layeredfp%layered_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}elseif($fingerprint=="MACCS") {
 				$sql = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.maccsfp,maccs_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md 
 				where fr.maccsfp%maccs_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC";
 			}
 			
 			
 			$result = pg_query($db, $sql);
 			if (!$result) {die("Error in SQL query: " . pg_last_error());}       

			$pagenum=$_GET["pagenum"];
			 //This checks to see if there is a page number. If not, it will set it to page 1 
			if (empty($pagenum)){ 
				$pagenum = 1; 
			}
			// Number of results
			$rows = pg_num_rows($result); 
			$page_rows = 5;
			$last = ceil($rows/$page_rows);
			if ($pagenum < 1){ 
				$pagenum = 1; 
			}elseif ($pagenum > $last){ 
				$pagenum = $last; 
			} 

			//This sets the range to display in our query 
			$max = 'LIMIT ' .$page_rows." OFFSET ".($pagenum-1)*$page_rows;
			 // execute query again
			if($fingerprint=="Morgan") {			
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.mfp2,morganbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.mfp2%morganbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}elseif($fingerprint=="MorganFeat") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.ffp2,featmorganbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.ffp2%featmorganbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}elseif($fingerprint=="Torsion") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.torsionbv,torsionbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.torsionbv%torsionbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
			}elseif($fingerprint=="Atom") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.atombv,atompairbv_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.atombv%atompairbv_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}elseif($fingerprint=="RDKit") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.rdkfp,rdkit_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.rdkfp%rdkit_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}elseif($fingerprint=="Layered") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.layeredfp,layered_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.layeredfp%layered_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}elseif($fingerprint=="MACCS") {
 				$sql_p = "SELECT DISTINCT mr.molregno,mr.m,$sim_query(fr.maccsfp,maccs_fp('$query'::mol)) sim, md.chembl_id FROM mols_rdkit mr, fps_rdkit fr, molecule_dictionary md  
 				where fr.maccsfp%maccs_fp('$query'::mol) and mr.molregno=md.molregno and mr.molregno=fr.molregno ORDER BY sim DESC $max";
 			}
			
			$result_p = pg_query($db, $sql_p);			
			if (!$result_p) {die("Error in SQL query: " . pg_last_error());}
 						
			/* 			
 			$campos=array("ChEMBL ID","Molecule","Similarity");
 			 			
			echo "<table class='tableResult'><tr>";
			foreach($campos as $campo){
				echo "<th>$campo</th>";	
			}
			echo "</tr>";
			*/
			
			$cont=1;
			echo "<table><tr>";
			
			shell_exec("rm -f ".$basedir."compound_images/*.png");
			while ($row = pg_fetch_array($result_p)) {
 				if (empty($row[molregno])){
 					echo '<b>No Results, please search again</b>';
 					echo "</tr>";
 				}
				else{
					
					if ($cont <= 5){
						$simScore=round($row[sim],2);
						$sqlImage= "SELECT lo_export(mol_pictures.image,'".$basedir."compound_images/$row[molregno].png') from mol_pictures where molregno=$row[molregno]";										
						pg_query($db,$sqlImage);
						echo "<td><img src='".$app2base."compound_images/$row[molregno].png' width='150' height='150'/><br/>
						<a href='report.php?id=$row[chembl_id]'>$row[chembl_id]</a><br/>
						Similarity: $simScore<br/></td>";
					}
					else {
						echo "</tr><tr>";
							$cont=0;
					}
					$cont=$cont+1;
 				}
			} 			
			echo "</table>";
			echo "</p><p>";
			echo "Page <b>$pagenum</b> of <b>$last</b> <br/>";

			if ($pagenum == 1) {} 
			else {
				$query=uriConversion($query);
				echo " <a href='{$_SERVER['PHP_SELF']}?fingerprint=$fingerprint&similarity=$similarity&chemical=$query&format=$queryFormat&pagenum=1' > <<-First</a> ";
				echo "    ";
				$previous = $pagenum-1;
				echo " <a href='{$_SERVER['PHP_SELF']}?fingerprint=$fingerprint&similarity=$similarity&chemical=$query&format=$queryFormat&pagenum=$previous' > <-Previous</a> ";
			} 
			
			echo " ---- ";
			
			if ($pagenum == $last) {} 
			else {
				$query=uriConversion($query);
				$next = $pagenum+1;
				echo " <a href='{$_SERVER['PHP_SELF']}?fingerprint=$fingerprint&similarity=$similarity&chemical=$query&format=$queryFormat&pagenum=$next' >Next -></a> ";
				echo "    ";
				echo " <a href='{$_SERVER['PHP_SELF']}?fingerprint=$fingerprint&similarity=$similarity&chemical=$query&format=$queryFormat&pagenum=$last' >Last ->></a> ";
			}
			// free memory
 			pg_free_result($result);			
			
			// close connection
 			pg_close($db);
             ?> 
             </p>    			
		</section> 
			
    </div>
    
<? include "footer.php"; ?> 
    
