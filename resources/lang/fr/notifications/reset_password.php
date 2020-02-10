<?php
return [
    'subject' => ":app_name - Réinitialisation du mot de passe",
    'body' => "Bonjour,
Une demande de récupération du compte :login sur la plateforme :app_name,
qui est associé à votre adresse email, vient d'être effectuée.
Si vous êtes bien l'auteur de cette demande, voici le code à coller dans
l'interface de récupération depuis laquelle vous avez effectué cette demande :
:token
Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce message.
Bien cordialement,
-- 
L'équipe technique"
];
