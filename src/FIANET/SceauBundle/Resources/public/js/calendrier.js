function afficherCalendrier(idInputDate)
{
    $('#' + idInputDate).datepicker({
        dateFormat: 'dd/mm/yy',
        firstDay: 1
    });
}