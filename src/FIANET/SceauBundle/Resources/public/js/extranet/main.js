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

function changerSite(id)
{
    $('#masque').show();

    $.get(Routing.generate('extranet_commun_site_selectionne', {id: id}, true),
        null,
        function (data) {
            location.reload(true);
        }
    );
}