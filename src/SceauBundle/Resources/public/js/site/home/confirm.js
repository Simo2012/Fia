var email = document.getElementById("email_principale")
  , confirm_email = document.getElementById("conf_email_principale");
  
var pwd = document.getElementById("myPassword")
  , confirm_pwd = document.getElementById("conf_myPassword");

function validateEmail(){
  if(email.value != confirm_email.value) {
    confirm_email.setCustomValidity("Les e-mails ne correspondent pas");
  } else {
    confirm_email.setCustomValidity('');
  }
}

function validatepwd(){
  if(pwd.value != confirm_pwd.value) {
    confirm_pwd.setCustomValidity("Les nouveaux mot de passe ne correspondent pas");
  } else {
    confirm_pwd.setCustomValidity('');
  }
}

email.onchange = validateEmail;
confirm_email.onkeyup = validateEmail;


pwd.onchange = validatepwd;
confirm_pwd.onkeyup = validatepwd;