var posX;
var posY;
function ChangePremiumFirst(){
	if(document.getElementById("checkpremiumfirst").checked==true){
		document.forms.changement.premiumfirst.value = 1;
	}else{
		document.forms.changement.premiumfirst.value = 0;
	}
	document.forms.changement.submit();
}
function ChangeElement(id,NewValue){
	var form = document.getElementById("changement");
	var ElementToChange = form.elements[id];
	ElementToChange.value = NewValue;
	/*if (id!="onglet" && id!="page"){
		form.elements["page"].value=1;
	}
	if (id=="order"){
		form.elements["periode"].value=3;
	}*/
	form.submit();
}
function ChangePeriode(neworder,NewValue){
	var form = document.getElementById("changement");
	var ElementToChange = form.elements["periode"]
	ElementToChange.value = NewValue;
	form.elements["order"].value=neworder;
	form.elements["page"].value=1;
	form.submit();
}
function ShowInfoBulle(num){
   if (!document.getElementById("InfoBulle"+num)) return false;
	
	//AJOUT PLACEMENT
	var espacement = '';
	
	var defaultX = 'right';
	var defaultY = 'bottom';
	var margeX = 10;
	var margeY = 10;
	
	if(defaultX == null || (defaultX!='left' && defaultX!='right')) defaultX = 'right';
	if(defaultY == null || (defaultY!='top' && defaultY!='bottom')) defaultY = 'top';
	if(margeX == null) margeX = 0;
	if(margeY == null) margeY = 0;

	
	//var element	 = document.getElementById(elementid);
	var element = document.getElementById("InfoBulle"+num);
	
	element.style.display = "block";

	var elementY = 0;
	var elementX = 0;

	//récupération des scrolls
	
	var scrollY = (navigator.appName == "Microsoft Internet Explorer") ? document.documentElement.scrollTop 	: window.pageYOffset;
	var scrollX = (navigator.appName == "Microsoft Internet Explorer") ? document.documentElement.scrollLeft : window.pageXOffset;
	
	//récupération des dimensions de la fenetre
	if(window.innerHeight){
	//Navigateurs sauf Explorer
		var docHeight = window.innerHeight;
		var docWidth = window.innerWidth;
	}else if (document.documentElement && document.documentElement.clientHeight) {
		//Internet Explorer mode Strict
		var docHeight = document.documentElement.clientHeight;
		var docWidth = document.documentElement.clientWidth;
	}else if (document.body && document.body.clientHeight){
		//Autres Internet Explorer
		var docHeight = document.body.clientHeight;
		var docWidth = document.body.clientWidth;
	}

	//dimensions de l'element à afficher (marges comprises)
	var elementHeight	= element.offsetHeight + margeY;
	var elementWidth	= element.offsetWidth + margeX;
	
	//emplacement souris relatif au scroll
	var relativePosX = PosX - scrollX;
	var relativePosY = PosY - scrollY;
	
	//calcul des places disponibles en haut, droite, gauche et bas
	spaceLeft	= relativePosX;
	spaceRight	= docWidth	- relativePosX;
	spaceTop		= relativePosY;
	spaceBottom	= docHeight - relativePosY;
	
	//définition des emplacements possibles			
	var possibleTop		= (elementHeight	<= spaceTop) 	? true : false;
	var possibleBottom	= (elementHeight	<= spaceBottom)? true : false;
	var possibleRight 	= (elementWidth	<= spaceRight) ? true : false;
	var possibleLeft 		= (elementWidth	<= spaceLeft) 	? true : false;
	//positionnement vertical
	if(possibleTop && (defaultY == 'top' || !possibleBottom)){ //on place en haut
		elementY = PosY - elementHeight - margeY;
	}else if(possibleBottom){ //on place en bas
		elementY = PosY + margeY;
	}else{ //on fait au mieux en fonction de la position par défaut
		//elementY = (defaultY == 'top') ? scrollY : scrollY + docHeight - elementHeight;
		elementY = scrollY;
	}
	

	//positionnement horizontal
	if(possibleRight && (defaultX == 'right' || !possibleLeft)){ //on peut placer à droite
		elementX = PosX + margeX;
	}else if(possibleLeft){ // on place à gauche
		elementX = PosX - margeX - elementWidth;
	}else{ //on fait en fonction du défaut
		//elementX = (defaultX == 'left') ? scrollX : scrollX + docWidth - elementWidth;
		elementX = scrollX;
	}

	
	element.style.left	= elementX + "px";
	element.style.top		= elementY + "px";
	element.style.visibility = "visible";
	

}
function HideInfoBulle(num){
	if (!document.getElementById("InfoBulle"+num)) return false;
	document.getElementById("InfoBulle"+num).style.visibility="hidden";
}
function emplacementSouris(e){
	var o3_frame = self;
	var docRoot = 'document.body';
	if (document.all){//IE
		/*PosX = eval('event.clientX+o3_frame.'+docRoot+'.scrollLeft');
		PosY = eval('event.clientY+o3_frame.'+docRoot+'.scrollTop');*/
		PosX = event.clientX + document.documentElement.scrollLeft;
		PosY = event.clientY + document.documentElement.scrollTop;
	}else{//Les autres
		PosX = e.pageX;
		PosY = e.pageY;
	}
}
function openPremium(){
	var win = window.open('/annuaire/fonctionnement_premium.php','fonctionnement_premium','scrollbars=1');
}

function openNotes(){
	var win= window.open('/annuaire/fonctionnement_notes.php','fonctionnement_notes','scrollbars=1');
}

function openTendance(url){
	var win= window.open(url,'tendance','height=400,width=700,scrollbars=1');
}

function openLitigesHorsNote(){
	var win = window.open('/annuaire/litiges_en_cours_hors_note.php','litiges_en_cours_hors_note','scrollbars=1,resizable=1,width=780,height=300');
}

function openNormeAfnor(){
	var win = window.open('/annuaire/norme_afnor.php','norme_afnor','scrollbars=1,height=620,width=780');
}

function changePage(newpage){
	document.forms.changement.page.value = newpage;
	document.forms.changement.submit();
}

function recherche(rechtype){
	var form = document.getElementById("choose");
	var SiteID = form.SiteID.options[form.SiteID.selectedIndex].value;
	var searchcategid = form.searchcategid.options[form.searchcategid.selectedIndex].value;
	var order = form.order.options[form.order.selectedIndex].value;
	if (rechtype==1 && SiteID!=0){//changement de site
		window.location.replace(SiteID);
	}else{//clic sur bouton rechercher
		if (searchcategid==0){//pas de cat sélectionnée
			alert("Veuillez sélectionner au moins une catégorie.");
		}else if(order==0){//pas d'order sélectionné
			//alert(searchcategid);
			window.location.replace(searchcategid);
		}else{
			//alert(searchcategid + "liste_1-"+ order +"-130.html");
			window.location.replace(searchcategid + "liste_1-"+ order +"-30.html");
		}
	}
}

function ChangeFiltreComm(FiltreComm){
	document.location.replace(FiltreComm);
	//document.getElementById("Refresh").NbCommentaires.value=10;
	//document.getElementById("Refresh").FiltreComm.value=FiltreComm;
	//document.getElementById("Refresh").submit();
}

function ValideForm(){
	var form_err = '';
	if (document.forms.log_user.login.value == 'login' ||  document.forms.log_user.login.value == ''){
		form_err += "Le login est manquant.";
	}
	if(form_err != ''){
		alert(form_err);
		return false;
	}else return true
}

document.onmousemove = emplacementSouris;


var nbBoules=0;
function addlot(nb){
	var input = document.forms.loterie.Combinaison;
	if (nbBoules < 5){
		if (nbBoules != 0){
			input.value+= ';';
		}
		input.value+= nb;
		nbBoules+=1;
	}
}
function resetCombinaison(){
	document.forms.loterie.Combinaison.value="";
	nbBoules=0;
}
function jouer(){
	var theForm = document.forms.loterie;
	if(nbBoules!=5){
		alert("Vous devez sélectionner 5 numéros");
	}else if(theForm.Email.value==""){
		alert("Votre e-mail est obligatoire pour que nous puissions vous recontacter en cas de victoire");
	}else{
		theForm.submit();
	}
}
function reglement(){
	var win=window.open('/questionnaire/reglement.php','reglement','height=500,width=500,menubar=no,location=no,resizable=no,scrollbars=yes,toolbar=no');
}


/******* CAPTCHA *******/
function changeCaseClass(nb){
	var btn = document.getElementById(nb);
	btn.className= "";
}


var nbCode=0;
function addCode(nb){
	var input_bad = document.forms.form_tombola.code;
	var input_good = document.forms.form_tombola.coord;
	if (nbCode < 8){
		if (nbCode != 0){
			input_good.value+= ';';
		}
		input_good.value+= nb;
		nbCode+=1;
		input_bad.value+= 'X';
	}
	var btn = document.getElementById(nb);
	btn.className= "selectionne";
	setTimeout('changeCaseClass("' + nb + '");', 300);
}

function razCode(){
   nbCode=0;
   var input_bad = document.forms.form_tombola.code;
	var input_good = document.forms.form_tombola.coord;

	input_bad.value="";
	input_good.value="";
}

function openoldlogin(){
	var win= window.open('/membre/oldlogin.php','Ancien_login','height=250,width=545,scrollbars=1');
}

function CheckFields(page){
	var atPos;
	switch(page){
	   case 1: //Nouveau membre ou Update d'un membre ACTIF
			with (document.form_tombola){
				if ( qualite.value == '---' ){
					alert("Veuillez préciser votre qualité");
					qualite.focus();
					return false;
				}
		      if ( nom.value.length == 0 ){
					alert("Veuillez saisir votre nom");
					nom.focus();
					return false;
				}
				if ( prenom.value.length == 0 ){
					alert("Veuillez saisir votre prénom");
					prenom.focus();
					return false;
				}
				if ( email.value.length == 0 ){
					alert("Veuillez saisir votre Email");
					email.focus();
					return false;
				}else{
					atPos = email.value.indexOf('@');
					if (atPos < 1 || atPos == (email.value.length - 1)){
						alert("Veuillez saisir un Email valide");
						email.select();
						email.focus();
						return false;
					}
				}
				if ( adresse.value.length == 0 ){
					alert("Veuillez saisir votre adresse");
					adresse.focus();
					return false;
				}
				if ( cp.value.length == 0 ){
					alert("Veuillez saisir votre code postal");
					cp.focus();
					return false;
				}
				if ( ville.value.length == 0 ){
					alert("Veuillez saisir votre ville");
					ville.focus();
					return false;
				}
				if ( pays.value.length == 0 ){
					alert("Veuillez saisir votre pays");
					pays.focus();
					return false;
				}
				if ( pays.value.length == 0 ){
					alert("Veuillez saisir votre pays");
					pays.focus();
					return false;
				}
				if ( questsecret.value == 0 ){
					alert("Veuillez préciser votre question secrète");
					questsecret.focus();
					return false;
				}
				if ( repsecret.value.length == 0 ){
					alert("Veuillez préciser une réponse à votre question secrète");
					repsecret.focus();
					return false;
				}
				if ( identsupp.value == 0 ){
					alert("Veuillez préciser votre identifiant supplémentaire");
					identsupp.focus();
					return false;
				}
				if ( repidentsupp.value.length == 0 ){
					alert("Veuillez préciser une réponse à votre identifiant supplémentaire");
					repidentsupp.focus();
					return false;
				}
				
				submit();
			}
		break;
	}
}

function nopnumeric(){
	document.forms.form_tombola.modecode.value = 2;
	document.getElementById("pnumeric").style.display = "none";
	document.getElementById("textpnumeric").style.display = "none";
	document.getElementById("codetextbox").style.display = "";
}


function activebandeauAFNOR() {
	$("div#footer_certification_afnor_bandeau").animate({width: 'toggle'}, "fast");
	$("div.footer_certification_afnor_action").toggleClass("footer_certification_afnor_btn_on");
	return false;
}

function activebandeauAFNORhome() {
	$("div#footer_certification_afnor_bandeau").css('display','block');
	$("div.footer_certification_afnor_action").addClass("footer_certification_afnor_btn_on");
	return false;
}



