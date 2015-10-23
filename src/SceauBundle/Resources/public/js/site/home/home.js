function chooseMode(id){
	$("#div_" + id).show();
	if(id=="login"){
		$("#div_inscription").hide();
	}else if(id=="login"){
		$("#div_inscription").hide();
                //$("div_inscription").hide();
	}else if(id=="inscription"){
			$("#div_logged").hide();
                        $("#div_login").hide();
			//$("div_login").hide();
	}
}