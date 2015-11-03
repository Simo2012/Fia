
var $pays = $('#site_member_update_coordonnee_pays');

                    if ($('#site_member_update_coordonnee_pays option:selected').text() == 'France') {
                        showCoordonne();
                    } else
                    {
                       HideCoordonne();
                    }
                        $pays.change(function() {
                          if ($('#site_member_update_coordonnee_pays option:selected').text() == "France") {
                            showCoordonne();
                          }
                          else
                            {
                                HideCoordonne();
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