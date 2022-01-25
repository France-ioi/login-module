<?php
return [

    'header' => 'Vérification',
    'not_required' => 'Aucune vérification requise pour utiliser cette plateforme.',
    'header_verifications' => 'Vérifications récentes',
    //'header_methods' => 'Méthodes de vérification disponibles',
    'header_recommended_methods' => 'Méthodes recommandées',
    'header_alternative_methods' => 'Méthodes pour les vérifications optionnelles',
    'header_optional_methods' => 'Autres méthodes disponibles',

    'btn_verify' => 'Vérifier',

    'unverified_attributes' => 'Afin de vous donner accès à :platform_name, nous devons vérifier les informations suivantes :',
    'profile_not_completed' => 'Certaines champs nécessitent une vérification avant que vous ne puissiez continuer.',
    'user_attributes' => 'Permet de vérifier',

    'approved_attributes' => 'Informations vérifiées',
    'rejected_attributes' => 'Informations rejetées',

    'methods' => [
        'email_code' => 'Vous envoyer un email contenant un code de validation',
        'email_domain' => 'Montrer que vous disposez d\'une adresse email académique',
        'id_upload' => 'Envoi d\'une photo de l\'utilisateur avec sa preuve d\'identité et un code',
        'classroom_upload' => 'Envoi d\'une photo de l\'enseignant dans sa classe avec quelques étudiants ou collègues, et tenant un code',
        'peer' => 'Faire vérifier votre statut par un collègue déjà vérifié',
        'imported_data' => 'Données importées'
    ],


    'states' => [
        'NOT_VERIFIED' => 'Non vérifié',
        'VERIFICATION_REQUIRED' => 'À vérifier',
        'IN_PROCESS' => 'Vérification en cours',
        'ACTION_REQUIRED' => 'Continuer la vérification',
        'REFRESH_REQUIRED' => 'Vérification à renouveller',
        'OBSOLETE' => 'Vérification obsolète',
        'VERIFIED' => 'Vérifié',
        'REJECTED' => 'Rejeté',
    ],

    'btn_profile' => 'Retour au profil',

    'email_code' => [
        'help' => 'Veuillez entrer le code reçu par mail. Si vous n\'avez jamais reçu le code, veuillez vous assurer que l\'adresse mail fournie est correcte, vérifier votre dossier de pourriels, et sinon contactez-nous à :email depuis cette adresse mail.',
        'email' => 'Adresse mail',
        'code' => 'Code',
        'error' => 'Mauvais code de vérification.',
        'no_emails' => 'Vous devez ajouter une adresse mail à votre profil avant de pouvoir utiliser cette méthode de vérification.'
    ],

    'email_domain' => [
        'step1_help' => 'Please provide as email from one of proposed domains. We will then send a code to this address, that you can then use to provide your status.',
        'wrong_code' => 'Wrong code',
        'account' => 'Compte',
        'domain' => 'Domaines acceptés',
        'send_code' => 'Send code',
        'step2_help' => 'Please check your email <a href="mailto::email">:email</a> and enter code here.',
        'validate' => 'Validate code'
    ],

    'imported_data' => 'Aucune action disponible',

    'id_upload' => [
        'help' => 'Ce formulaire vous permet d\'envoyer une photo de l\'utilisateur tenant une preuve d\'identité et un code.',
        'file' => 'Sélectionner le fichier',
        'list' => 'Liste des champs du profil que cette méthode permet de vérifier'
    ],

    'classroom_upload' => [
        'help' => 'Ce formulaire vous permet d\'envoyer une photo de l\'utilisateur dans sa classe avec quelques étudiants ou collègues, et tenant un code',
    ],

    'peer' => [
        'help' => 'Vérification par un collègue',
        'email' => 'Adresse mail ou nom d\'utilisateur',
        'user_not_found' => 'Utilisateur non trouvé',
        'code' => 'Code',
        'wrong_code' => 'Mauvais code',
        'link_code' => 'Entrer le code',
        'code_help' => 'Veuillez entrer le code.',
    ],

    'upload' => [
        'file' => 'Sélectionner le fichier',
        'file_size' => 'La taille de ce fichier ne doit pas excéder :size megabyte(s). Types de fichiers autorisés : .gif .jpg .png',
        'link_view_file' => 'Voir le fichier'
    ]

];
