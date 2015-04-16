function paquetQuestionnairesSuivants(texteFin, nbQuestionnairesMax, dateDebut, dateFin, tri) {

    $(document).ready(function () {

        var offset = nbQuestionnairesMax;

        $(window).data('ajaxready', true);

        $(window).scroll(function () {
            if ($(window).data('ajaxready') == false) return;

            if (($(window).scrollTop() + $(window).height()) == $(document).height()) {
                $(window).data('ajaxready', false);

                $('#chargement').fadeIn(400);

                $.post(Routing.generate('extranet_questionnaires_questionnaires', null, true),
                    {offset: offset, dateDebut : dateDebut, dateFin : dateFin, tri: tri},
                    function (data) {
                        if (data != '') {
                            $('#listeQuestionnaires').find('tr:last').after(data);

                            offset += nbQuestionnairesMax;

                            $(window).data('ajaxready', true);
                            $('#chargement').fadeOut(400);
                        }
                        else {
                            $('#chargement').hide().after(texteFin);
                        }
                    }
                );
            }
        });
    });
}

function selectTriQuestionnaires(numTri)
{
    $('#questionnaires_liste_tri').val(numTri);
    $('form').submit();
}