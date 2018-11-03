<?php

return [
    'header' => 'Profil',
    'optional_fields_filter' => "Afficher seulement les champs requis ou recommandés",

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
        'student' => 'Élève',
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
    'picture_size_error' => "L'image de profil ne doit pas peser plus de :size mégaoctet(s).",
    'graduation_grade_range' => "Indiquez votre classe pour l'année :year_begin-:year_end",
    // TODO: check this translation
    'graduation_grade' => "Indiquez votre classe",
    'success' => 'Profil mis à jour.',

    'pms_redirect_msg' => "En tant qu'utilisateur PMS, vous devez éditer votre profil directement sur PMS. Veuillez ensuite utiliser le bouton \"Zurück zum JwInf\" afin de mettre à jour votre profil ici.",
    'pms_redirect_btn' => 'Continuer vers PMS',

    'teacher_domain_verified' => 'Avez-vous une adresse mail avec un nom de domaine reconnu ?',
    'teacher_domain_options' => [
        'yes' => "Oui, je vais l'enregistrer comme adresse mail primaire/secondaire",
        'no' => "Non, je n'en ai pas"
    ],
    'teacher_domain_alert' => "Veuillez contacter :email en expliquant pourquoi vous n'avez pas d'adresse mail avec un nom de domaine reconnu.",
    'login_change_limitations' => "Si vous changez d'identifiant, après une heure, vous ne pourrez plus le changer pendant une année.",
    'login_change_required' => 'Veuillez nous excuser, vous devez choisir un nouvel identifiant. Celui que vous avez choisi est déjà pris, ou bien ne respecte pas les règles. Seules les lettres minuscules, les chiffres et le tiret - sont autorisés.',

    'tooltips' => [
        'login' => 'Évitez d’inclure votre nom dans votre login, car il sera rendu public. Choisissez plutôt un pseudo dont vous pouvez facilement vous souvenir.',
        'first_name' => 'Knowing your name makes it possible for us to give you access to your account if you lose access, and didn’t provide an email address that you have access to. You may also decide to make it public if you want other users to know who you are (see field below). This field is required if you want to participate officially to contests.',
        'last_name' => 'Knowing your name makes it possible for us to give you access to your account if you lose access, and didn’t provide an email address that you have access to. You may also decide to make it public if you want other users to know who you are (see field below). This field is required if you want to participate officially to contests.',
        'grade' => 'Knowing your grade makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'graduation_year' => 'Knowing your graduation year makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'country_code' => 'Knowing your country of residence makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'role' => 'Knowing and checking your role, makes it possible to give you access to tools reserved for some types of users.',
        'primary_email' => 'Knowing your email address gives us a way to help you if you lost password, or contact you about events or content you might be interested in (if you accept, see below).',
        'secondary_email' => 'Knowing your email address gives us a way to help you if you lost password, or contact you about events or content you might be interested in (if you accept, see below).',
        'language' => 'This information will be used to determine in what language the platform and content should be displayed.',
        'birthday' => 'Knowing your birthday makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'gender' => 'Knowing your genre will help us publish some general statistics about who uses our platform, as well as analyze if some content should be better adapted to different genders.',
        'presentation' => 'This is information about you you decide to make public to all users.',
        'website' => 'This is information about you you decide to make public to all users.',
        'student_id' => 'Only provide this information if required by your school. This will make it possible for your teachers to check who you are.'
    ]
];
