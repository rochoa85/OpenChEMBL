<?php
 /*
===============

(c) 2012 EMBL European Molecular Biology Laboratories

This code is licensed under Version 2.0 of the Open Source Initiative Apache License.
URL: http://www.opensource.org/licenses/apache2.0.php 

===============
 */
?>

<? include "header.php"; ?> 
               
    <div id="content" role="main" class="grid_24 clearfix">
    		   
	   <section>
    		<h2>Property calculation</h2>
    		 <p>
			 In this section the user can calculate Molecular Properties, using as input SMILES strings, SMARTS queries or MOL files stored in your computer
    					
				<br/><br/>
            <b>Select one option:</b><br/> 
				<input type="radio" name="chembl" id="chembl1" value="Yes" onchange="set();"/> Draw your structure <br/>
				<input type="radio" name="chembl" id="chembl2" value="No" onchange="set();"/> Input an string or a molfile stored in your computer
				</p>
			
				<div style="display:none; text-align:center;" id="marvin">
		        <script type="text/javascript" SRC="<?=$app2base?>applets/marvin/marvin.js"></script>
					<script type="text/javascript">
	        			msketch_name="MSketch";
						msketch_begin("<?=$app2base?>/applets/marvin", 540, 480);
						msketch_end();
						function exportMol(format) {
	               	if(document.MSketch != null) {
	                  	var s = document.MSketch.getMol(format);
	                     // Convert "\n" to local line separator
	                    	s = unix2local(s);
	                    	document.getElementById("marSmarts").value = s;
	                	}else {
	                		alert("Cannot import molecule:\n"+"no JavaScript to Java communication"+"in your browser.\n");
	                	}
	        			}
										
					</script>
					<br/><a href="http://www.chemaxon.com/"><img src="<?=$app2base?>/static/images/app/freeweb-150.gif" width="138" height="30" border="0" style="vertical-align:top" /></a>
					<form name="marSketch" method="get" action="property_results.php" class="formulario">
						<input type="text" style="display:none" id="marSmarts" name="chemical" size="50"><br/><br/>
						<input type="hidden" name="format" value="SMARTS"/>					
						<input TYPE="submit" align="left" VALUE="Calculate" onClick="exportMol('smarts:u');">
					</form>
      		</div>
			
				<div style="display:none" id="entrada">
				<p>
				1. Please select one of the following formats for the input: 
				<select name="format" id="format" onclick="string()">
					<option value="None" selected="selected" class="listheader">---</option>				
					<option value="SMILES" class="listheader">SMILES</option>
					<option value="MOL" class="listheader">MOL</option>
					<option value="ARTS" class="listheader">SMARTS</option>
				</select>
				</p>
				</div>
				
				<div style="display:none" id="stringSMILES">
				<form name="rdkit" method="get" action="property_results.php" class="formulario"> 
					2. Enter the string of characters (for <b>SMILES</b> queries): <input type="text" name="chemical" value="CC1=CC(C)(C)Nc2cc3oc(=O)cc(C(F)(F)F)c3cc21" size="50"/><br/><br/>
					<input type="hidden" name="format" value="SMILES"/>
					<input type="submit" name="accion" value="Run SMILES" id="runDisplay"/>
				</form>
				</div>				
				
				<div style="display:none" id="stringSMARTS">
				<form name="smarts" method="get" action="property_results.php" class="formulario">
					2. Enter the string of characters (for <b>SMARTS</b> queries): <input type="text" name="chemical" value="[#6;X4]-1-[#6](=[#8])-[#7]-[#7]-[#6]-1=[#8]" size="50"/><br/><br/>
					<input type="hidden" name="format" value="SMARTS"/>
					<input type="submit" name="accion" value="Run SMARTS" id="runDisplay"/>
				</form>
				</div>			
						
			<div style="display:none" id="stringMOL">
			<form name="molfile" method="post" action="property_results.php" enctype="multipart/form-data" class="formulario">		
				2. Select a <b>MOL</b> file from your computer: <input type="file" name="datafile" id="datafile" size="40"/><br/><br/>
				<input type="hidden" name="format" value="MOL"/>
				<input type="submit" name="accion" value="Run MOL" id="runDisplay"/>
			</form>
			</div>
		</section> 
			
    </div>
    
<? include "footer.php"; ?> 

