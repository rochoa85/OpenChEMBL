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
    		<h2>Substructure results</h2>
  
    	     <h3>Query details</h3>    			

             <p>
             
             <?
				if (!empty($_GET["chemical"])) {
					$query=$_GET["chemical"];
					$queryFormat=$_GET["format"];
					$match=$_GET["match"];
				}
			
				if (!empty($_FILES["datafile"])) {   			
   				$queryMol=$_FILES["datafile"];
   				$queryFormat=$_POST["format"];
   				$match=$_POST["match"];
				}
					
				if($match=="subs"){
					$searchOperator="@>";
					$molformat="qmol";
					$searchType="Substructure";
				}
				elseif($match=="exact") {
					$searchOperator="@=";
					$molformat="mol";
					$searchType="Exact";
				}
			
 			
			if($queryFormat=="MOL"){
				$molecule=file_get_contents($queryMol["tmp_name"]);
				$query=convertMOL($molecule);
				echo "<b>Query (MolFile):</b> ".$queryMol["name"]."<br/>";
				echo "<b>Search Type:</b> $searchType";
			}else{
				echo "<b>Query:</b> $query <br/>";
				echo "<b>Search Type:</b> $searchType";
			} 	
			?>		
             </p>
             
    			
    	     <h3>Results</h3>    			
             <p>
             <?
            $db = pg_connect("user=$db_user dbname=$db_name host=$db_host port=$db_port");
 			if (!$db) {die("Error in connection: " . pg_last_error());}
			
			//echo "<b>SUMMARY RESULTS</b> (max 10 results per page)<br/><br/>";
			// execute query
		$pagenum=$_GET["pagenum"];

		if (empty($pagenum)){ 
			$drop = "DROP TABLE IF EXISTS queryTemp";
			$resDrop = pg_query($db, $drop);
 			$sql = "CREATE TABLE queryTemp AS SELECT DISTINCT mr.molregno,mr.m,md.chembl_id FROM mols_rdkit mr, molecule_dictionary md WHERE mr.m $searchOperator '$query'::$molformat AND mr.molregno=md.molregno";
 			$result = pg_query($db, $sql);
 			if (!$result) {die("Error in SQL query: " . pg_last_error());}
			$pagenum = 1; 
		}

			$sqlCont = "SELECT count(*) FROM queryTemp";
			$resultCont = pg_query($db,$sqlCont); 
			if (!$resultCont) {die("Error in SQL query: " . pg_last_error());}      

			// Number of results
			$rowOne = pg_fetch_row($resultCont);
			$rows = $rowOne[0];
			$page_rows = 13;
			$last = ceil($rows/$page_rows);
			if ($pagenum < 1){ 
				$pagenum = 1; 
			}elseif ($pagenum > $last){ 
				$pagenum = $last; 
			}

			//This sets the range to display in our query 
			$max = 'LIMIT ' .$page_rows." OFFSET ".($pagenum-1)*$page_rows;
			
			$sql_p = "SELECT DISTINCT molregno,m,chembl_id FROM queryTemp $max";
 			$result_p = pg_query($db, $sql_p);
 			if (!$result_p) {die("Error in SQL query: " . pg_last_error());}     			
			
 			// iterate over result set
 			
 			
			/* 			
 			$campos=array("ChEMBL ID","Molecule");
 			 			
			echo "<table><tr>";
			foreach($campos as $campo){
				echo "<th>$campo</th>";	
			}
			
			echo "</tr>";*/
			$cont=1;
			echo "<table><tr>";			
			
			shell_exec("rm -f ".$basedir."compound_images/*.png");
			while ($row = pg_fetch_array($result_p)) {
 				if (empty($row[molregno])){
 					echo '<b>No Results, please search again</b>';
 					echo "</tr>";
 				}
				else{
					$sqlImage= "SELECT lo_export(mol_pictures.image,'".$basedir."compound_images/$row[molregno].png') from mol_pictures where molregno=$row[molregno]";										
					pg_query($db,$sqlImage);
					
					if ($cont <= 6){
					//echo "<td><a href='https://www.ebi.ac.uk/chembldb/compound/inspect/$row[chembl_id]'>$row[chembl_id]</a></td><td>
					//<img src='https://www.ebi.ac.uk/chembldb/compound/displayimage/$row[molregno]'/></td>";
						echo "<td><img src='".$app2base."compound_images/$row[molregno].png' width='150' height='150'/><br/>
						<a href='report.php?id=$row[chembl_id]'>$row[chembl_id]</a><br/></td>";
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
				echo " <a href='{$_SERVER['PHP_SELF']}?match=$match&chemical=$query&format=$queryFormat&pagenum=1' > <<-First</a> ";
				echo "    ";
				$previous = $pagenum-1;
				echo " <a href='{$_SERVER['PHP_SELF']}?match=$match&chemical=$query&format=$queryFormat&pagenum=$previous' > <-Previous</a> ";
			} 

			echo " ---- ";

			if ($pagenum == $last) {} 
			else {
				$query=uriConversion($query);
				$next = $pagenum+1;
				echo " <a href='{$_SERVER['PHP_SELF']}?match=$match&chemical=$query&format=$queryFormat&pagenum=$next' >Next -></a> ";
				echo "    ";
				echo " <a href='{$_SERVER['PHP_SELF']}?match=$match&chemical=$query&format=$queryFormat&pagenum=$last' >Last ->></a> ";
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

