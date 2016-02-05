var translatedStrings = {
   "fr": {
      "login_failed": "Échec d'authentification",
      "passwords_different": "Les deux mots de passe sont différents",
      "logout_from_provider":  "Voulez vous aussi vous déconnecter de {1} ?",
   },
   "en": {
      "login_failed": "Login failed",
      "passwords_different": "The two passwords are different",
      "logout_from_provider":  "Do you also want to disconnect from {1}?",
   }
};

function translate(key, params) {
   var translation =  translatedStrings.fr[key];
   if (params !== undefined) {
      for (var iParam = 0; iParam < params.length; iParam++) {
         translation = translation.replace("{" + (iParam + 1) + "}", params[iParam]);
      }
   }
   return translation;
}
