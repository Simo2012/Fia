function chooseMode(id){
    alert('je suis la');
	document.getElementById("div_" + id).style.display = "block";
	document.forms.form_tombola.mode.value = id;
	if(id=="logged"){
		document.getElementById("div_inscription").style.display = "none";
	}else if(id=="login"){
		document.getElementById("div_inscription").style.display = "none";
	}else if(id=="inscription"){
			document.getElementById("div_logged").style.display = "none";
			document.getElementById("div_login").style.display = "none";
	}
}