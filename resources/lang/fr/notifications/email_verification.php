<?php
return [
    'subject' => ":app_name - Vérification de l'adresse mail",
    'body' => 
<<<PHP_STR
Vous recevez ce message parce qu'une vérification de l'adresse mail est requise pour votre compte.<br>
Code de vérification: :code<br>
Allez dans votre profil utilisateur pour y fournir ce code et nous permettre de valider votre adresse mail.<br>
Veuillez suivre le lien ci-dessous pour poursuivre le processus de vérification:<br>
<a href=":url">:url</a> 
PHP_STR
];
