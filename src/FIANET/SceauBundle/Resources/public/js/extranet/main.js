$(document).ready(function() {
    $('nav ul li:has(li.active)').addClass('active');
});

function recupDate() {
    var OjDate = new Date();
    var tabDay = ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'];
    var tabMois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    var recupDate = tabDay[OjDate.getDay()] + ' ' + OjDate.getDate() + ' ' + tabMois[OjDate.getMonth()] + ' ' + OjDate.getFullYear();
    document.getElementById('DIVdate').innerHTML = recupDate;
}
window.onload = recupDate;

function selectionListeDeroulante(action, id)
{
    $('#masque').show();

    $.get(Routing.generate(action, {id: id}, true),
        null,
        function (data) {
            location.reload(true);
        }
    );
}

function changerSite(id)
{
    selectionListeDeroulante('extranet_commun_site_selectionne', id);
}

function changerQuestionnaireType(id)
{
    selectionListeDeroulante('extranet_commun_questionnaire_type_selectionne', id);
}