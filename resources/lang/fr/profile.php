<?php

return [
    'header' => 'Profil',
    'optional_fields_filter' => "Afficher seulement les champs requis ou recommandés",
    'not_completed' => 'Afin de vous connecter, veuillez remplir les champs requis:',

    'login' => 'Identifiant',
    'first_name' => 'Prénom',
    'last_name' => 'Nom de famille',
    'real_name_visible' => 'Afficher mon nom réel sur mon profil public',
    'primary_email'  => 'Adresse mail principale',
    'secondary_email'  => 'Adresse mail secondaire',
    'language' => 'Langue',
    'nationality' => 'Nationalité',
    'country_code' => 'Pays',
    'address' => 'Adresse',
    'city' => 'Ville',
    'zipcode' => 'Code postal',
    'timezone' => 'Fuseau horaire',
    'primary_phone' => 'Numéro de téléphone principal',
    'secondary_phone' => 'Numéro de téléphone secondaire',
    'subscription_results' => 'Je donne mon accord pour recevoir des courriels à propos de ma qualification et mes résultats aux concours auxquels je participe.',
    'subscription_news' => 'Je donne mon accord pour recevoir des courriels très occasionnels à propos de nouveaux contenus ou événements qui pourraient m\'intéresser.',
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
    'grade_or_graduation_year' => 'Classe ou année du bac',
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

    'primary_email_verification_code' => "Code de vérification de l'adresse mail principale",
    'secondary_email_verification_code' => "Code de vérification de l'adresse mail secondaire",
    'email_verification_help' => "Veuillez entrer le code reçu par mail. Si vous ne l'avez pas reçu, veuillez vérifier que l'adresse mail est correcte, que le courrier n'est pas dans votre boîte spam, puis contactez-nous à :email depuis cette adresse mail.",
    'email_verified' => 'Vérifiée',
    'email_verification_code_error' => 'Mauvais code de vérification.',

    'pms_redirect_msg' => "En tant qu'utilisateur PMS, vous devez éditer votre profil directement sur PMS. Veuillez ensuite utiliser le bouton \"Zurück zum JwInf\" afin de mettre à jour votre profil ici.",
    'pms_redirect_btn' => 'Continuer vers PMS',

    'provided_other_sections' => 'Informations fournies dans les autres sections :',

    'teacher_domain_verified' => 'Avez-vous une adresse mail avec un nom de domaine reconnu ?',
    'teacher_domain_options' => [
        'yes' => "Oui, je vais l'enregistrer comme adresse mail primaire/secondaire",
        'no' => "Non, je n'en ai pas"
    ],
    'teacher_domain_alert' => "Veuillez contacter :email en expliquant pourquoi vous n'avez pas d'adresse mail avec un nom de domaine reconnu.",
    'login_change_limitations' => "Si vous changez d'identifiant, après une heure, vous ne pourrez plus le changer pendant une année.",
    'login_change_required' => 'Veuillez nous excuser, vous devez choisir un nouvel identifiant. Celui que vous avez choisi est déjà pris, ou bien ne respecte pas les règles. Seules les lettres minuscules, les chiffres et le tiret - sont autorisés.',

    'verification_alert_p1' => "Cette information a été vérifiée. Si vous la modifiez, vous devrez refaire le processus de vérification ou vous pourriez perdre l'accès à certaines plateformes ou fonctionnalités : ",
    'verification_alert_p2' => 'Voulez-vous vraiment les changer ?',

    'email_verification_alert' => 'Adresse mail non vérifiée',

    'profile_filter' => 'Pour accéder à :platform_name, vous devez modifier les informations suivantes :',

    'required_fields_explanation' => 'Seuls les champs marqués avec (*) sont requis',
    'collected_data' => 'Gérer ou effacer les données collectées sur moi',

    'tooltips' => [
        'login' => 'Évitez d’inclure votre nom dans votre login, car il sera rendu public. Choisissez plutôt un pseudo dont vous pouvez facilement vous souvenir.',
        'first_name' => "Connaître votre nom nous permettra de vous réouvrir l'accès à votre compte si vous avez perdu cet accès, et que vous n'avez pas ou plus d'email valide rattaché à votre compte. Vous pourrez également choisir de rendre votre nom visible dans votre profil public (voir option ci-dessous). Ce champ est aussi utile si vous souhaitez participer officiellement à des concours.",
        'last_name' => "Connaître votre nom nous permettra de vous réouvrir l'accès à votre compte si vous avez perdu cet accès, et que vous n'avez pas ou plus d'email valide rattaché à votre compte. Vous pourrez également choisir de rendre votre nom visible dans votre profil public (voir option ci-dessous). Ce champ est aussi utile si vous souhaitez participer officiellement à des concours.",
        'grade' => 'Knowing your grade makes it possible for us to determine what events you can participate in (training camps we organize, for example), or to rank you in the correct category when you participate in online contests.',
        'graduation_grade' => "Connaître votre classe ou année du bac nous permet de déterminer à quels événements vous pouvez participer, comme les stages d'entraînement que nous organisons. Cela nous permet également de vous classer dans la bonne catégorie lorsque vous participer aux concours en ligne.",
        'nationality' => "Connaître votre nationalité nous permet de déterminer à quels événements vous pouvez participer, comme les stages d'entraînement que nous organisons. Cela nous permet également de vous classer dans la bonne catégorie lorsque vous participer aux concours en ligne.",
        'country_code' => "Connaître votre pays de résidence nous permet de déterminer à quels événements vous pouvez participer, comme les stages d'entraînement que nous organisons. Cela nous permet également de vous classer dans la bonne catégorie lorsque vous participer aux concours en ligne.",
        'role' => "Connaître et vérifier votre rôle nous permet de vous donner accès à des outils réservés à certains types d'utilisateurs.",
        'primary_email' => "Connaître votre adresse email nous permet de vous aider si vous perdez votre mot de passe, ou de vous contacter au sujet d'événements ou de contenus qui pourraient vous intéresser.",
        'secondary_email' => "Connaître votre adresse email nous permet de vous aider si vous perdez votre mot de passe, ou de vous contacter au sujet d'événements ou de contenus qui pourraient vous intéresser.",
        'language' => 'Cette information sera utilisée pour déterminer en quelle langue la plateforme et son contenu doivent être affichées.',
        'timezone' => 'Cette information sera utilisée pour déterminer comment vous présenter les dates et heures.',
        'birthday' => "Connaître votre date de naissance nous permet de déterminer à quels événements vous pouvez participer, comme les stages d'entraînement que nous organisons. Cela nous permet également de vous classer dans la bonne catégorie lorsque vous participer aux concours en ligne.",
        'gender' => "Connaître votre genre nous permettra d'effectuer des statistiques sur l'utilisation de notre plateforme, ainsi que de déterminer si certains de nos contenus doivent être mieux adaptés aux différents genres.",
        'presentation' => "Cette information publique est destinée à être rendue visible par l'ensemble des utilisateurs.",
        'website' => "Cette information publique est destinée à être rendue visible par l'ensemble des utilisateurs.",
        'student_id' => 'Only provide this information if required by your school. This will make it possible for your teachers to check who you are.',
        'picture' => "Il s'agit d'une image qui sera rendue visible aux autres utilisateurs lorsqu'ils regarderont votre profil ou liront vos messages lors de discussions privées ou publiques."
    ],

    'sections' => [
        'personal' => 'Informations personnelles',
        'school' => 'Informations sur les études',
        'contact' => 'Informations de contact',
        'public' => 'Informations publiques',
        'settings' => 'Paramètres',
    ],
    'section_descriptions' => [
        'public' => 'Les informations ci-dessous seront affichées sur votre profil public, et donc peuvent être vues par quiconque.',
    ],
    'icons' => [
        'personal' => 'user',
        'school' => 'graduation-cap',
        'contact' => 'envelope',
        'public' => 'eye',
        'settings' => 'flag'
    ],
    'quick_menu' => 'Menu rapide'
];
