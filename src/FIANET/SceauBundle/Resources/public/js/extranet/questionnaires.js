function creerTooltipLivraison(contenu) {
    $('#tooltip').tooltipster({
            contentAsHTML: true,
            content: $(contenu),
            interactive: true,
            maxWidth: 350
        }
    );
}

function paquetQuestionnairesSuivants(texteFin, nbQuestionnairesMax, dateDebut, dateFin, tri, recherche, indicateurs, livraison) {

    $(document).ready(function () {

        var offset = nbQuestionnairesMax;

        $(window).data('ajaxready', true);

        $(window).scroll(function () {
            if ($(window).data('ajaxready') == false) return;

            if (($(window).scrollTop() + $(window).height()) == $(document).height()) {
                $(window).data('ajaxready', false);

                $('#chargement').fadeIn(400);

                $.post(Routing.generate('extranet_questionnaires_questionnaires_ajax', null, true),
                    {
                        offset: offset,
                        dateDebut: dateDebut,
                        dateFin: dateFin,
                        tri: tri,
                        recherche: recherche,
                        indicateurs: indicateurs.split('-'),
                        livraison: livraison
                    },
                    function (data) {
                        if (data != '') {
                            $('#listeQuestionnaires').find('tr:last').after(data);

                            offset += nbQuestionnairesMax;

                            $(window).data('ajaxready', true);
                            $('#chargement').fadeOut(400);
                        }
                        else {
                            $('#chargement').hide().after('<p>' + texteFin + '</p>');
                        }
                    }
                );
            }
        });
    });
}

function selectTriQuestionnaires(numTri) {
    $('#questionnaires_liste_tri').val(numTri);
    $('form').submit();
}

function selectIndicateur(numIndicateur, imageIndicateur) {
    var checkbox = document.getElementById('questionnaires_liste_indicateurs_' + numIndicateur);

    if (checkbox.checked) {
        imageIndicateur.className = 'indicateur-non-selectionne';
        checkbox.checked = false;
    } else {
        imageIndicateur.className = 'indicateur-selectionne';
        checkbox.checked = true;
    }
}

function decocherRetenir() {
    document.getElementById('questionnaires_liste_retenir').checked = false;
}