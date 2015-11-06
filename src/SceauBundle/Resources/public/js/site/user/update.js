var $pays = $('#site_member_update_coordonnee_pays');
var $pays2 = $('#site_member_register_pays');

if ($('#site_member_update_coordonnee_pays option:selected').text() == 'France') {
    showCoordonne();
} else
{
    HideCoordonne();
}

if ($('#site_member_register_pays option:selected').text() == 'France') {
    showCoordonne2();
} else
{
    HideCoordonne2();
}

$pays.change(function () {
    if ($('#site_member_update_coordonnee_pays option:selected').text() == "France") {
        showCoordonne();
    }
    else
    {
        HideCoordonne();
    }

});

$pays2.change(function () {
    if ($('#site_member_register_pays option:selected').text() == "France") {
        showCoordonne2();
    }
    else
    {
        HideCoordonne2();
    }

});
function showCoordonne() {
    $("#coordonnes").show();
    $("#choose-avatar-update").removeClass('choose-avatar-hide');
    $("#choose-avatar-update").addClass('choose-avatar');
}
function HideCoordonne()
{
    $("#choose-avatar-update").addClass('choose-avatar-hide');
    $("#choose-avatar-update").removeClass('choose-avatar');
    $("#coordonnes").hide();
}

function showCoordonne2() {
    $("#coordonnes").show();
    $("#tombola-error").hide();
    $("#btn-tombola").show();

}
function HideCoordonne2()
{
    $("#coordonnes").hide();
    $("#tombola-error").show();
    $("#btn-tombola").hide();
}