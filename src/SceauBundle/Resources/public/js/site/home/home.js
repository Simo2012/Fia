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
function SuprimerEmail(id) {
    var path = $("#deleteEmail").attr("data-path");
     $.ajax({
            url: path,
            type: 'POST',
            data: { idEmail: id },
            success: function(data){ // quand la réponse de la requete arrive
              // $( "#contenu-espace-membre-avec-menu" ).load('{{ path("site_member_update") }}');
               window.location.reload();
              //window.location.
              console.log(data);
            }
          });
}

function checkPassword() {
    var pwd = $("#password_actuel").val();
     var path = $("#password_actuel").attr("data-path");
     $.ajax({
            url: path,
            type: 'POST',
            data: { pwd: pwd },
            success: function(data){ // quand la réponse de la requete arrive
              //window.location.
            if(data.error === false){
                 $("#password_actuel").val('Paswword Incorrect');
            }
              //alert(data);
              //$("#password_actuel").html(data);
            }
          });
}