// Déclaration de la fonction qui lance la recherche
function loadData(e, urlAbsolueAnnuaire) {
	if (e.keyCode != 38 &&(e.keyCode != 40 )) { // 38 : up arrow, 40 : down arrow (keycode : saisie au clavier)
    var req = new XMLHttpRequest();
		req.open("POST", "/annuaire/mdr3.php", true);
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		var termeRecherche = document.getElementById('foo').value;

		termeRecherche = termeRecherche.replace('&', ' ');		
		termeRecherche = termeRecherche.replace('-', ' ');		
		termeRecherche = termeRecherche.replace(' et ', ' ');
		termeRecherche = termeRecherche.replace(' and ', ' ');
		termeRecherche = termeRecherche.replace(' y ', ' '); /* espagnol */
		termeRecherche = termeRecherche.replace(' und ', ' '); /* allemand */
		termeRecherche = termeRecherche.replace(' e ', ' '); /* italien */		
		termeRecherche = termeRecherche.replace('   ', ' ');		
		termeRecherche = termeRecherche.replace('  ', ' ');		
		termeRecherche = termeRecherche.replace('  ', ' ');		
		termeRecherche = termeRecherche.replace("'", " ");		
		
		req.send("foo="+ termeRecherche);

		req.onreadystatechange = function (){

		   if (req.readyState == 4 && req.responseText!='')
		   {
		      var doc = eval('(' + req.responseText + ')');
		      idlight = 0;
			  var div = document.getElementById('autoCompletion_answer');

		      var output = '<div id="bloc_resultat_mdr" class="glossymenu"  style="display:block; background-color: white;" onMouseOut="cacherListe();" onMouseOver="afficherListe();"><div class="submenu" style="display:block; background-color: white;"><ul>';
		      var nbtot = doc.cat.length + doc.sscat.length +doc.site.length + doc.page.length  ;
		      if((doc.cat.length)>0 ){
		     		output +='<li><img src="http://www.fia-net.com/img/chevron_quest_table.png">&nbsp;<b>Catégories</b></li>';
		     	}

		     	var i =0;
		     	if (doc.cat.length > 0) {
		          // On définit la hauteur de la liste en fonction du nombre de resultats et de la hauteur de ligne
		          for (var j = 0; j < doc.cat.length; j++) {

		               var id = i + 1;
		               var texte = doc.cat[j].titre;
		               var urllink = urlAbsolueAnnuaire + doc.cat[j].urldest;
									output += '<li id="mot'+id+'" class="" ><a id="lien'+id+'" href="'+urllink+'" OnMouseOver="changeclass('+id+','+nbtot+')" >&nbsp;'+doc.cat[j].titre+'</a></li>';
		      				/*for(var t=1; t<4; t++){
		      					if(resultat.getAttribute('leveldown'+t)!=null){
		      						output += '<li >&nbsp;&nbsp;&nbsp;&nbsp;-'+resultat.getAttribute('leveldown'+t)+'</li>';

		      					}
		      				}*/

		          		var i = i+1;
		          }
		     }
		     // pour les SousCategorie
		     if (doc.sscat.length > 0) {
		          // On définit la hauteur de la liste en fonction du nombre de resultats et de la hauteur de ligne
		          for (var j = 0; j < doc.sscat.length; j++) {
		               var id = i + 1;
		               var texte = doc.sscat[j].titre;
		               var urllink = urlAbsolueAnnuaire + doc.sscat[j].urldest;
		              /*if(resultat.getAttribute('levelup')!=0){
		               output +='<li>&nbsp;&nbsp;&nbsp;'+resultat.getAttribute('levelup')+'</li>';
		              }*/

									output += '<li id="mot'+id+'" class="" ><a id="lien'+id+'" href="'+urllink+'" OnMouseOver="changeclass('+id+','+nbtot+')" >'+doc.sscat[j].levelup+'&nbsp;&nbsp;->&nbsp;'+doc.sscat[j].titre+'</a></li>';
		          		var i = i+1;
		          }
		     }


		     // pour les sites
	    	 if (doc.site.length > 0) {
	    	 	output +='<li><img src="http://www.fia-net.com/img/chevron_quest_table.png">&nbsp;<b>Sites</></li>';
	    	 	 for (var j = 0; j < doc.site.length ; j++) {
	               var id = i + 1;
	               var texte = doc.site[j].titre;
	               var urllink = urlAbsolueAnnuaire + doc.site[j].urldest;
				   	 // ajouté le 02/08/11 pour test : on remplace les "   " par "-"	(en général les "   " (3espaces) proviennent de la présence d'un "&")
					 var reg1=new RegExp('   ', 'gi');
					 urllink = urllink.replace(reg1,'-');
				   	 // ajouté le 02/08/11 pour test : on remplace les espaces par des tirets
					 var reg2=new RegExp(' ', 'gi');
					 urllink = urllink.replace(reg2,'-');

								output += '<li id="mot'+id+'" class="" ><a id="lien'+id+'" href="'+urllink+'" OnMouseOver="changeclass('+id+','+nbtot+')" >&nbsp;'+doc.site[j].titre+'</a></li>';
	          		var i = i+1;
	          }
	    	}
			
			
		     // pour les pages du site (exemple: la tombola)
		     if (doc.page.length > 0) {
		          // On définit la hauteur de la liste en fonction du nombre de resultats et de la hauteur de ligne
		          output +='<li><img src="http://www.fia-net.com/img/chevron_quest_table.png">&nbsp;<b>Rubriques</></li>';
				  for (var j = 0; j < doc.page.length; j++) {
		               var id = i + 1;
					   var texte = doc.page[j].titre;
					   var urllink = urlAbsolueAnnuaire + doc.page[j].urldest;
						output += '<li id="mot'+id+'" class="" ><a id="lien'+id+'" href="'+urllink+'" OnMouseOver="changeclass('+id+','+nbtot+')" >&nbsp;'+doc.page[j].titre.charAt(0).toUpperCase() + doc.page[j].titre.slice(1)+'</a></li>';
						var i = i+1;
						
		          }
		     }			
			

			 	output += '</ul></div></div>';
				div.innerHTML = output;
				div.style.display = 'block';
		   }

		};

		/*
		// ajouté le 02/08/11 pour test de req.send(null) qui semble poser problème :
		try {
			req.send(null); // la méthode send sert à envoyer le corps de la requête, on récupère le code de retour, s'il vaut 200 tout s'est bien passé
		}
		catch(e) {
			alert('Une erreur s\'est produite lors de l\'envoi du corps de la requête : \n\n'+ e);
		}
		*/
   }
}

function changeclass(j,nbtot){
	idlight = j;
	for(k=1;k <= nbtot;k++){
		(k==j)? document.getElementById("mot"+k).className="motselected" : document.getElementById("mot"+k).className="" ;
	}
}

function cacherListe() {
	// document.getElementById('bloc_resultat_mdr').style.color='red';
	document.getElementById('bloc_resultat_mdr').style.display='none';
}

function afficherListe() {
	// document.getElementById('bloc_resultat_mdr').style.color='green';
	document.getElementById('bloc_resultat_mdr').style.display='block';
}

