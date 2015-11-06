var $pays = $('#site_member_register_pays');
alert($('#site_member_register_pays option:selected').text());
                    if ($('#site_member_register_pays option:selected').text() == 'France') {
                        alert('show');
                        show();
                    } else
                    {
                        alert('hide');
                       Hide();
                    }
                        $pays.change(function() {
                          if ($('#site_member_register_pays option:selected').text() == "France") {
                               alert('show');
                            show();
                          }
                          else
                            {
                                alert('hide');
                                Hide();
                            }
                          
                        });
                   function show() {
                        alert('show');
                       $("#coordonnes").show();
                       $("#tombola-error").hide();
                       $("#btn-tombola").show();
                       
                   } 
                   function Hide()
                   {
                        Hide();
                       $("#coordonnes").hide();
                       $("#tombola-error").show();
                       $("#btn-tombola").hide();
                   }