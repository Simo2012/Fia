var $pays = $('#site_member_register_pays');
                    if ($('#site_member_register_pays option:selected').text() == 'France') {
                        showCoordonne();
                    } else
                    {
                       HideCoordonne();
                    }
                        $pays.change(function() {
                          if ($('#site_member_register_pays option:selected').text() == "France") {
                            showCoordonne();
                          }
                          else
                            {
                                HideCoordonne();
                            }
                          
                        });
                   function showCoordonne() {
                       $("#coordonnes").show();
                       $("#tombola-error").hide();
                       $("#btn-tombola").show();
                       
                   } 
                   function HideCoordonne()
                   {
                       $("#coordonnes").hide();
                       $("#tombola-error").show();
                       $("#btn-tombola").hide();
                   }