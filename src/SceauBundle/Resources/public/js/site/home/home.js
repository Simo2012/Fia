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
function showFormulaire(type)
{
    $('#bouton_' + type).hide();
    $('#div_form_' + type).slideToggle('slow');
}
function addClassByClick(button){
    button.addClass("on")
}