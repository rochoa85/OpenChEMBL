/////////////////////////////////////////////////////////
// Some extra JavaScript functions
/////////////////////////////////////////////////////////

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

function set(){
	if(document.getElementById("chembl1").checked==true) {
  		document.getElementById("marvin").style.display="block";
  		document.getElementById("entrada").style.display="none";
  		document.getElementById("stringMOL").style.display="none";
		document.getElementById("stringSMARTS").style.display="none";
		document.getElementById("stringSMILES").style.display="none";
	}else if(document.getElementById("chembl2").checked==true) {
  		document.getElementById("marvin").style.display="none";
  		document.getElementById("entrada").style.display="block";
	}
}

function Sim(){
	if(document.getElementById("chemblSim1").checked==true) {
  		document.getElementById("marvinSim").style.display="block";
  		document.getElementById("entradaSim").style.display="none";
  		document.getElementById("stringMOLSim").style.display="none";
		document.getElementById("stringSMARTSSim").style.display="none";
		document.getElementById("stringSMILESSim").style.display="none";
	}else if(document.getElementById("chemblSim2").checked==true) {
  		document.getElementById("marvinSim").style.display="none";
  		document.getElementById("entradaSim").style.display="block";
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
