function submitForm(oForm){
	if(oForm.isSubmitted){
		//alert('Votre formulaire est en cours d\'envoi... merci de bien vouloir patienter quelques instants.');
		return false;
	}else{
		oForm.isSubmitted = true;
		oForm.submit();
		return false;
	}
}
function AddEtape(id){
	//var win = window.open('','AddEtape','height=300,width=500,menubar=no,location=no,resizable=no,scrollbars=yes,toolbar=no');
	document.forms.AddEtape.EtapeID.value = id;
	submitForm(document.forms.AddEtape);
}
function ShowDetail(id)
{
	var Table = document.getElementById('Table'+id);

	if (Table.style.display == 'none')
	{
		document.getElementById('Table'+id).style.display = 'table';
	}
	else
	{
		document.getElementById('Table'+id).style.display = 'none';
	}
}
function myEscape(text){
	text = escape(text);
	text = text.replace(/%0D%0A/g,"aa");
	text = text.replace(/%0A/g,"aa");
	return unescape(text);
}
function CountCaracteres(id,maxlen){
	var objTextarea = document.getElementById(id);
	var objTD = document.getElementById("td"+id);
	//alert (escape(objTextarea.value));
	var nb = myEscape(objTextarea.value).length;
	if (nb > maxlen){
		objTD.innerHTML = "<font color='red'>Nombre de caract&egrave;res en trop : "+-(maxlen-nb)+"</font>";
	}
	else{
		objTD.innerHTML = "<font color='#575757'>Nombre de caract&egrave;res disponibles : "+(maxlen-nb)+"</font>";
	}
}
function SubmitForm(){
	var liste = document.forms.SiteChoice.SiteID;
	if (liste.options[liste.selectedIndex].value==0){
		alert ("Veuillez sélectionner un site dans la liste");
	}
	else{
		submitForm(document.forms.SiteChoice);
	}
}
function DeclareTo(el){
	var choix = el.options[el.selectedIndex].value;

	if(el.id=="DeclareToMarchand"){
		switch(choix){
			case "oui": //show le oui et hide le impossible
				document.getElementById("DeclareToMarchandDetail").style.display = 'table';
				document.getElementById("DeclareToMarchandImpossible").style.display = "none";
				break;
			case "impossible": //show le impossible et hide le oui
				document.getElementById("DeclareToMarchandImpossible").style.display = 'table';
				document.getElementById("DeclareToMarchandDetail").style.display = "none";
				break;
			default: //hide les 2
				document.getElementById("DeclareToMarchandDetail").style.display = "none";
				document.getElementById("DeclareToMarchandImpossible").style.display = "none";
				break;
		}
	}else{
		if(choix=="oui"){
			document.getElementById("DeclareToOrganismeDetail").style.display = 'table';
		}else{
			document.getElementById("DeclareToOrganismeDetail").style.display = "none";
		}
	}
}

function CheckFieldsInfoMembre(){
var atPos;

	with (document.forms.modifmembre){
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

	}
	submitForm (document.forms.modifmembre);
	return true;
}

function CheckMail(){
	var atPos;
	if ( document.frmpass.Email.value.length == 0 ){
		alert("Veuillez saisir votre Email");
		document.frmpass.Email.focus();
		return false;
	}

	submitForm(document.frmpass);
	return true;
}

function CheckFields(page){
	var atPos;
	switch(page){
	   case 1: //Nouveau membre ou Update d'un membre ACTIF
			with (document.forms.insmembre){
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
				if(typeof(email) != "undefined"){
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

			}
			submitForm(document.forms.insmembre);
			break;

		case 2: //Modification des infos générales
		   with (document.modifmembre){
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
			}
			submitForm(document.modifmembre);
		break;

		case 3: //Modification Mot de passe
		   with (document.modiflogin){
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

				if ( emailconfirm.value.length == 0 ){
					alert("Veuillez saisir votre Email de confirmation");
					emailconfirm.focus();
					return false;
				}else{
					atPos = emailconfirm.value.indexOf('@');
					if (atPos < 1 || atPos == (emailconfirm.value.length - 1)){
						alert("Veuillez saisir un Email de confirmation valide");
						emailconfirm.select();
						emailconfirm.focus();
						return false;
					}
				}

				if ( email.value != emailconfirm.value ){
					alert("L'email de confirmation ne correspond pas");
					email.select();
					email.focus();
					return false;
				}
			}
			submitForm(document.modiflogin);
		break;

		case 4:
			with (document.forgotlogin){
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
			}
			submitForm(document.forgotlogin);
		break;

		case 5:
			with (document.insmembre){
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

			}
			submitForm(document.insmembre);
		break;

		case 6: //Modification des infos abonnements
			submitForm(document.gestabonewsletter);
		break;

		case 7: //Raison de désabonnement
			submitForm(document.desabototalnewsletter);
		break;


		case 8: //Inscription à la newsletter
			with (document.forms.form_newsletter)
			{
				if (email.value.length == 0 )
				{
					alert("Veuillez saisir votre Email.");
					email.focus();
					return false;
				}
				else
				{
					atPos = email.value.indexOf('@');
					if(atPos < 1 || atPos == (email.value.length - 1))
					{
						alert("Veuillez saisir un Email valide.");
						email.focus();
						return false;
					}
				}

				var civiliteOK = false;
			    /* Parcours des boutons radio pour voir si au moins un des boutons est coché */
				for(var i=0; (i<civilite.length) && (!civiliteOK); i++)
				{
					civiliteOK = civilite[i].checked;
				}
				if(!civiliteOK)
				{
					alert("Veuillez saisir votre civilité.");
					return false;
				}

		      	if(nom.value.length == 0)
		      	{
					alert("Veuillez saisir votre nom.");
					nom.focus();
					return false;
				}

				if(prenom.value.length == 0)
				{
					alert("Veuillez saisir votre prénom.");
					prenom.focus();
					return false;
				}

				if(activitepro.value == -1)
				{
					alert("Veuillez saisir votre activité professionnelle.");
					return false;
				}

				if(situationfam.value == -1)
				{
					alert("Veuillez saisir votre situation familiale.");
					return false;
				}

				if(age.value == -1)
				{
					alert("Veuillez saisir votre tranche d'âge.");
					return false;
				}

				if(captcha.value.length == 0)
				{
					alert("Veuillez saisir le code de l'image.");
					captcha.focus();
					return false;
				}
			}
		submitForm(document.forms.form_newsletter);
		break;
	}
	return true;
}

function CheckFieldsModif(){
	var atPos;

	return true;
}

function wait(){
   for(var i=1; i<5; i++){
		document.getElementById('A'+i).className= "";
		document.getElementById('B'+i).className= "";
		document.getElementById('C'+i).className= "";
		document.getElementById('D'+i).className= "";
	}
}

var nbCode=0;
function addCode(nb){
	var input_bad = document.forms.logger.code;
	var input_good = document.forms.logger.coord;
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
	setTimeout('wait();',1000);
}

function razCode(){
   nbCode=0;
   var input_bad = document.forms.logger.code;
	var input_good = document.forms.logger.coord;

	input_bad.value="";
	input_good.value="";
}



function DivStatus( nom, numero ){
	var divID = nom + numero;
	if ( document.getElementById && document.getElementById( divID ) ){ // Pour les navigateurs récents
		Pdiv = document.getElementById( divID );
		PcH = true;
	}else if( document.all && document.all[ divID ] ){ // Pour les veilles versions
		Pdiv = document.all[ divID ];
		PcH = true;
	}else if ( document.layers && document.layers[ divID ] ){ // Pour les très veilles versions
		Pdiv = document.layers[ divID ];
		PcH = true;
	}else{
		PcH = false;
	}

	if ( PcH ){
		Pdiv.className = ( Pdiv.className == 'cachediv' ) ? '' : 'cachediv';
	}
}

function openoldlogin(){
	var win= window.open('/membre/oldlogin.php','Ancien_login','height=250,width=545,scrollbars=1');
}

var nbCaptcha = 0;
function reloadCaptcha(sessionkey){
	nbCaptcha++;
	if(document.getElementById){
		//document.getElementById("captcha").src = "http://donald/cbrun/captcha/captcha.php?SK=" + sessionkey + '&num=' + nbCaptcha;
		document.getElementById("captcha").src = "/membre/captcha.php?SK=" + sessionkey + '&num=' + nbCaptcha;
	}else{
		alert("Désolé, une erreur est apparue lors du rechargement");
	}
}

function nopnumeric(){
	document.forms.logger.modecode.value = 2;
	document.getElementById("pnumeric").style.display = "none";
	document.getElementById("textpnumeric").style.display = "none";
	document.getElementById("codetextbox").style.display = "";
}

function supprimMembre(){
	if(document.forms.supmembre.checksupmembre.checked == true){
		if(confirm('Etes-vous sûr de vouloir supprimer vos données personnelles de notre site?\n '
		+'(La suppression vous déconnectera de l\'espace membre)')){
			document.supmembre.submit();
		}
	}else{
		alert('Vous devez cocher la case avant de cliquer sur le bouton valider');
	}
}

/* FONCTION POUR DYNCALENDAR */
function Callback_Gen(obj, date, month, year){
	if (String(month).length == 1)month = '0' + month;
if (String(date).length == 1)date = '0' + date;
obj.value = date + '/' + month + '/' + year;
}

function Callback_DateDebut(date, month, year){
	var obj = document.gestabonewsletter.datedebut;
	Callback_Gen(obj, date, month, year);
}

function Callback_DateFin(date, month, year){
	var obj = document.gestabonewsletter.datefin;
	Callback_Gen(obj, date, month, year);
}