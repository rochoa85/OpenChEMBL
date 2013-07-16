<?php
 /*
===============

(c) 2012 EMBL European Molecular Biology Laboratories

This code is licensed under Version 2.0 of the Open Source Initiative Apache License.
URL: http://www.opensource.org/licenses/apache2.0.php 

===============
 */
?>
<? include "functions.php"; ?>
<? include "header.php"; ?> 
               
    <div id="content" role="main" class="grid_24 clearfix">
    		   
	   <section>
    		<h2>Property calculation</h2>
    		 <p>
			 In this section the user can calculate Molecular Properties, using as input SMILES strings, SMARTS queries or MOL files stored in your computer
    					
				<br/><br/>
            <b>Select one option:</b><br/> 
            
				<select id="property-input">
					<option value="ignore">Please choose input type...</option>
					<option value="property-draw">Draw structure</option>
					<option value="property-upload">Upload file</option>
				</select>				
				</p>
			
				<div style="display:none; text-align:center;" id="marvinPro">
		      	<?
						$category="property";
						jsmeDraw($category);
					?>
      		</div>
			
				<div style="display:none" id="entradaPro">
				<p>
				1. Please select one of the following formats for the input: 
				<select name="format" id="format" onclick="stringPro()">
					<option value="None" selected="selected" class="listheader">---</option>				
					<option value="SMILES" class="listheader">SMILES</option>
					<option value="MOL" class="listheader">MOL</option>
					<option value="ARTS" class="listheader">SMARTS</option>
				</select>
				</p>
				</div>
				
				<div style="display:none" id="stringSMILESPro">
				<form name="rdkit" method="get" action="property_results.php" class="formulario"> 
					2. Enter the string of characters (for <b>SMILES</b> queries): <input type="text" name="chemical" value="CC1=CC(C)(C)Nc2cc3oc(=O)cc(C(F)(F)F)c3cc21" size="50"/><br/><br/>
					<input type="hidden" name="format" value="SMILES"/>
					<input type="submit" name="accion" value="Run SMILES" id="runDisplay"/>
				</form>
				</div>				
				
				<div style="display:none" id="stringSMARTSPro">
				<form name="smarts" method="get" action="property_results.php" class="formulario">
					2. Enter the string of characters (for <b>SMARTS</b> queries): <input type="text" name="chemical" value="[#6;X4]-1-[#6](=[#8])-[#7]-[#7]-[#6]-1=[#8]" size="50"/><br/><br/>
					<input type="hidden" name="format" value="SMARTS"/>
					<input type="submit" name="accion" value="Run SMARTS" id="runDisplay"/>
				</form>
				</div>			
						
			<div style="display:none" id="stringMOLPro">
			<form name="molfile" method="post" action="property_results.php" enctype="multipart/form-data" class="formulario">		
				2. Select a <b>MOL</b> file from your computer: <input type="file" name="datafile" id="datafile" size="40"/><br/><br/>
				<input type="hidden" name="format" value="MOL"/>
				<input type="submit" name="accion" value="Run MOL" id="runDisplay"/>
			</form>
			</div>
		</section> 
			
    </div>
    
<? include "footer.php"; ?> 

