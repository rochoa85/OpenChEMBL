/////////////////////////////////////////////////////////
// Some extra JavaScript functions
/////////////////////////////////////////////////////////

$(document).ready(function() {
	//$("input[name=chembl]:radio").attr("checked", false);
	//$("input[name=chemblSim]:radio").attr("checked", false);
	
	$("#structure-search-type option:first").attr('selected', 'selected' );
	$("#substructure-search-input option:first").attr('selected', 'selected' );
	$("#similarity-search-input option:first").attr('selected', 'selected' );
	
	$('#substructure-input-div').hide();
	$('#similarity-input-div').hide();
	
	$('#structure-search-type').change(function(){
		if(this.value === 'sub'){
			$('#substructure-input-div').show();
			$('#similarity-input-div').hide();
			document.getElementById("container").style.display="none";
			document.getElementById("marvin").style.display="none";			
			document.getElementById("marvinSim").style.display="none";			
		} else if(this.value === 'sim'){
			$('#substructure-input-div').hide();
			$('#similarity-input-div').show();
			document.getElementById("container").style.display="none";
			document.getElementById("marvin").style.display="none";			
			document.getElementById("marvinSim").style.display="none";
		}
	})

	$('#substructure-search-input').change(function(){
		if(this.value === 'substructure-draw'){
	  		document.getElementById("marvin").style.display="block";
	  		document.getElementById("entrada").style.display="none";
	  		document.getElementById("stringMOL").style.display="none";
			document.getElementById("stringSMARTS").style.display="none";
			document.getElementById("stringSMILES").style.display="none";
			document.getElementById("container").style.display="block";
			document.getElementById("marvinSim").style.display="none";
			
		} else if(this.value === 'substructure-upload'){
	  		document.getElementById("marvin").style.display="none";
	  		document.getElementById("entrada").style.display="block";
	  		document.getElementById("container").style.display="none";
	  		document.getElementById("marvinSim").style.display="none";
		}
	})
	
	$('#similarity-search-input').change(function(){
		if(this.value === 'similarity-draw'){
	  		document.getElementById("marvinSim").style.display="block";
	  		document.getElementById("entradaSim").style.display="none";
	  		document.getElementById("stringMOLSim").style.display="none";
			document.getElementById("stringSMARTSSim").style.display="none";
			document.getElementById("stringSMILESSim").style.display="none";
			document.getElementById("container").style.display="block";
			document.getElementById("marvin").style.display="none";				
		} else if(this.value === 'similarity-upload'){
	  		document.getElementById("marvinSim").style.display="none";
	  		document.getElementById("entradaSim").style.display="block";
	  		document.getElementById("container").style.display="none";
	  		document.getElementById("marvin").style.display="none";
		}
	})
	
	$("#property-input option:first").attr('selected', 'selected' );	
	
	$('#property-input').change(function(){
		if(this.value === 'property-draw'){
	  		document.getElementById("marvinPro").style.display="block";
	  		document.getElementById("entradaPro").style.display="none";
	  		document.getElementById("stringMOLPro").style.display="none";
			document.getElementById("stringSMARTSPro").style.display="none";
			document.getElementById("stringSMILESPro").style.display="none";
			document.getElementById("container").style.display="block";		
		} else if(this.value === 'property-upload'){
	  		document.getElementById("marvinPro").style.display="none";
	  		document.getElementById("entradaPro").style.display="block";
	  		document.getElementById("container").style.display="none";		
		}
	})
	
	$("#assayCliffsearch option:first").attr('selected', 'selected' );	
	
	$('#assayCliffsearch').change(function(){
		if(this.value === 'assayYes'){
	  		document.getElementById("assayCliffform").style.display="block";		
		} else if(this.value === 'assayNo'){
	  		document.getElementById("assayCliffform").style.display="none";		
		}
	})
	
	$("#targetCliffsearch option:first").attr('selected', 'selected' );	
	
	$('#targetCliffsearch').change(function(){
		if(this.value === 'targetYes'){
	  		document.getElementById("targetCliffform").style.display="block";		
		} else if(this.value === 'targetNo'){
	  		document.getElementById("targetCliffform").style.display="none";		
		}
	})
	
});


function despliegaInformacion(divinfo,pagina)
{
	var x=new XMLHttpRequest();
	div=document.getElementById(divinfo);
	x.onreadystatechange=function(){
		if(x.readyState==4){
			div.innerHTML=x.responseText;
		}else{
			div.innerHTML="<center>Waiting..</center>";
		}
	}
	x.open("GET",pagina,true);
	x.send();
}

function string(){
	if(document.getElementById("format").value=="SMILES"){
		document.getElementById("stringSMILES").style.display="block";
		document.getElementById("stringSMARTS").style.display="none";
		document.getElementById("stringMOL").style.display="none";
	}
	else if(document.getElementById("format").value=="ARTS"){	
		document.getElementById("stringSMARTS").style.display="block";
		document.getElementById("stringMOL").style.display="none";
		document.getElementById("stringSMILES").style.display="none";	
	}
	else if(document.getElementById("format").value=="MOL"){
		document.getElementById("stringMOL").style.display="block";
		document.getElementById("stringSMARTS").style.display="none";
		document.getElementById("stringSMILES").style.display="none";
	}
	else if(document.getElementById("format").value=="None"){
		document.getElementById("stringMOL").style.display="none";
		document.getElementById("stringSMARTS").style.display="none";
		document.getElementById("stringSMILES").style.display="none";
	}
}

function stringPro(){
	if(document.getElementById("format").value=="SMILES"){
		document.getElementById("stringSMILESPro").style.display="block";
		document.getElementById("stringSMARTSPro").style.display="none";
		document.getElementById("stringMOLPro").style.display="none";
	}
	else if(document.getElementById("format").value=="ARTS"){	
		document.getElementById("stringSMARTSPro").style.display="block";
		document.getElementById("stringMOLPro").style.display="none";
		document.getElementById("stringSMILESPro").style.display="none";	
	}
	else if(document.getElementById("format").value=="MOL"){
		document.getElementById("stringMOLPro").style.display="block";
		document.getElementById("stringSMARTSPro").style.display="none";
		document.getElementById("stringSMILESPro").style.display="none";
	}
	else if(document.getElementById("format").value=="None"){
		document.getElementById("stringMOLPro").style.display="none";
		document.getElementById("stringSMARTSPro").style.display="none";
		document.getElementById("stringSMILESPro").style.display="none";
	}
}


function Simstring(){
	if(document.getElementById("formatSim").value=="SMILESSim"){
		document.getElementById("stringSMILESSim").style.display="block";
		document.getElementById("stringSMARTSSim").style.display="none";
		document.getElementById("stringMOLSim").style.display="none";
	}
	else if(document.getElementById("formatSim").value=="ARTSSim"){	
		document.getElementById("stringSMARTSSim").style.display="block";
		document.getElementById("stringMOLSim").style.display="none";
		document.getElementById("stringSMILESSim").style.display="none";	
	}
	else if(document.getElementById("formatSim").value=="MOLSim"){
		document.getElementById("stringMOLSim").style.display="block";
		document.getElementById("stringSMARTSSim").style.display="none";
		document.getElementById("stringSMILESSim").style.display="none";
	}
	else if(document.getElementById("formatSim").value=="None"){
		document.getElementById("stringMOLSim").style.display="none";
		document.getElementById("stringSMARTSSim").style.display="none";
		document.getElementById("stringSMILESSim").style.display="none";
	}
}

function dataChembl(){
	if(document.getElementById("dataChem").value=="ntd"){
		document.getElementById("chemblData").style.display="none";
	}
	else if(document.getElementById("dataChem").value=="chembl"){	
		document.getElementById("chemblData").style.display="block";
	}
}

function python(){
	if(document.getElementById("pycode").style.display=="none"){
		document.getElementById("pycode").style.display="block";
	}else if (document.getElementById("pycode").style.display=="block"){
		document.getElementById("pycode").style.display="none";
	}
}

function load(){
	var Scr = new ActiveXObject("Scripting.FileSystemObject");
	var CTF = Scr.OpenTextFile("test.php", 1, true);
	data = CTF.ReadAll(); 
	alert(data);
	CTF.Close();
}

function GetSelectedItem() {
	chosen = "";
	len = document.setTest.chembl.length;
	for (i = 0; i < len; i++) {
		if (document.setTest.chembl[i].checked) {
			chosen = document.setTest.chembl[i].value;
		}
	}
	if(chosen="yes") {
  		document.getElementById("chemblSet").style.display="block";
	}else if(chosen="no") {
  		document.getElementById("chemblSet").style.display="none";
	}else{
		alert();	
	}
}
