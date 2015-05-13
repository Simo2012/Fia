function creerPopupEmail() {
    var popupEmail =
        $("#email-type").dialog({
            modal: true,
            autoOpen: false,
            minWidth: 595
        });

    $("#lien-email-type").on("click", function () {
        popupEmail.dialog("open");
    });
}

function creerTooltipCheckbox(contenu) {
    $('#tooltip-checkbox').tooltipster({
        content: $(contenu),
        interactive: true,
        maxWidth: 350
    });
}

function paquetQuestionnairesSuivants(texteFin, nbQuestionnairesMax, langue_id) {

    $(document).ready(function () {

        var offset = nbQuestionnairesMax;

        $(window).data('ajaxready', true);

        $(window).scroll(function () {
            if ($(window).data('ajaxready') == false) return;

            if (($(window).scrollTop() + $(window).height()) == $(document).height()) {
                $(window).data('ajaxready', false);

                $('#chargement').fadeIn(400);

                $.post(Routing.generate('extranet_questionnaires_relance_ajax', null, true),
                    {
                        offset: offset,
                        langue_id: langue_id
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

function automatiserRelance(langue_id, activer, msgSuccess, msgErreur) {
    var autoMessage = $('#auto-message');
    autoMessage.removeClass().html('');

    $.post(Routing.generate('extranet_questionnaires_relance_auto', null, true),
        {
            langue_id: langue_id,
            activer: activer
        },
        function (success) {
            if (success) {
                autoMessage.addClass('succes').html(msgSuccess);

            } else {
                autoMessage.addClass('erreur').html(msgErreur);
            }
        }
    );
}