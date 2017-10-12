<?php

return [
    'header' => 'Profil',
    'display_only_required_fields' => "Afficher seulement les champs requis",

    'login' => 'Identifiant',
    'first_name' => 'Prénom',
    'last_name' => 'Nom de famille',
    'real_name_visible' => 'Afficher mon nom réel sur mon profil public',
    'primary_email'  => 'Adresse mail principale',
    'secondary_email'  => 'Adresse mail secondaire',
    'language' => 'Langue',
    'country_code' => 'Pays',
    'address' => 'Addresse',
    'city' => 'Ville',
    'zipcode' => 'Code postal',
    'timezone' => 'Fuseau horaire',
    'primary_phone' => 'Numéro de téléphone principal',
    'secondary_phone' => 'Numéro de téléphone secondaire',
    'role' => 'Rôle',
    'roles' => [
        'student' => 'Étudiant',
        'teacher' => 'Enseignant',
        'other' => 'Autre'
    ],
    'ministry_of_education' => "De quel pays dépend le ministère de l'éducation de rattachement de votre école ?",
    'ministry_of_education_fr' => "Dans une école publique ou privée sous contrat avec le Ministère français de l'Éducation",
    'school_grade' => 'Classe',
    'student_id' => "Numéro d'étudiant",
    'graduation_year' => 'Année du bac',
    'birthday'  => "Date d'anniversaire",
    'gender' => 'Genre',
    'genders' => [
        'm' => 'Homme',
        'f' => 'Femme'
    ],
    'presentation'  => 'Présentation',
    'website' => 'Site Internet',
    'picture' => 'Image de profil',
    'picture_size_error' => "L'image de profil ne doit pas peser plus de :size megabyte(s).",
    'graduation_grade' => "L'année du bac doit être comprise entre :year_begin et :year_end",
    'success' => 'Profil mis à jour.',

    'primary_email_verification_code' => "Code de vérification de l'adresse mail principale",
    'secondary_email_verification_code' => "Code de vérification de l'adresse mail secondaire",
    'email_verification_help' => "Entrez le code reçu par mail. Si vous ne l'avez jamais reçu, vérifiez que l'adresse mail fournie est correcte, vérifiez vos courriers indésirables, sinon contactez-nonus à :email depuis cette adresse mail",
    'email_verified' => 'Vérifiée',
    'email_verification_code_error' => "Mauvais code de vérification d'adresse mail",

    'pms_redirect_msg' => "En tant qu'utilisateur PMS, vous devez éditer votre profil directement sur PMS. Veuillez ensuite utiliser le bouton \"Zurück zum JwInf\" afin de mettre à jour votre profil ici.",
    'pms_redirect_btn' => 'Continuer vers PMS',

    'teacher_domain_verified' => 'Avez-vous une adresse mail avec un nom de domaine reconnu ?',
    'teacher_domain_options' => [
        'yes' => "Oui, je vais l'enregistrer comme adresse mail primaire/secondaire",
        'no' => "Non, je n'en ai pas"
    ],
    'teacher_domain_alert' => "Veuillez contacter :email en expliquant pourquoi vous n'avez pas d'adresse mail avec un nom de domaine reconnu."
];
