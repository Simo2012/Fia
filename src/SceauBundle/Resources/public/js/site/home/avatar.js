(function(){
    "use strict";

    var popupAvatar = "";
    SelectAvatar($('#AvatarID').val());
    function showPopupAvatar() {
        $(function() {
            popupAvatar = $( "#avatar-popup" ).dialog({width:330});
        });
    }

    function SelectAvatar(num) {
        var tab = $('#avatar_id_' + num).attr('coords').split(',');
        $('#img_avatar').css('background-position','-' + tab[0] + 'px -' + tab[1] + 'px');
        $('#AvatarID').val(num);
        if (popupAvatar) {
            popupAvatar.dialog( "close" );
        }
    }
});
