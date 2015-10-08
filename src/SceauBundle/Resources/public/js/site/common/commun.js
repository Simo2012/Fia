// Sert à l'annuaire FM partie commentaires + idem Extranet + Back Office FM
function affDetails(details) {
	$("#"+details+"").slideToggle("slow");
	affDetailsTitle(details);
}

function affDetailsTitle(lien) {	
	if ($("."+lien+"").html() == "[+]") {
	  $("."+lien+"").html("[-]");
	}else{
	  $("."+lien+"").html("[+]");		
	};	
	return false;
}	


function affCommentaireAvtLivraison(details,lien) {
	$("#"+details+"").slideToggle("slow");
	affCommentaireAvtLivraisonTitle(lien);
}

function affCommentaireAvtLivraisonTitle(lien) {	
	if ($("#"+lien+"").html() == "Voir l'avis avant livraison") {
	  $("#"+lien+"").html("Fermer l'avis avant livraison");
	  $("#"+lien+"").attr("title","Fermer l'avis avant livraison");

	}else{
	  $("#"+lien+"").html("Voir l'avis avant livraison");
	  $("#"+lien+"").attr("title","Voir l'avis avant livraison");  
	};	
	return false;
}	

function fmAffAllReviews(urlPopupReviews) {
	var win = window.open(urlPopupReviews,'tous_les_avis','scrollbars=1,resizable=1,width=1020');
}

function openCSVcheckSiteFraudeur(id)
{
	var url_csv = 'extractCheckSiteFraudeur.php?siteID=' + id;
	window.open(url_csv, 'extract_csv', 'width=1, height=1');
}

