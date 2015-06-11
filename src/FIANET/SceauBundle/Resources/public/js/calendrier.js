function afficherCalendrier(idInputDate, locale, parametres)
{
    $.datepicker.setDefaults($.datepicker.regional[locale]);
    $('#' + idInputDate).datepicker(parametres);
}