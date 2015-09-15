<?php

//-----------------------------------------------------------------
/// @file
/// @brief translation classe:
/// - #translate: a class to translate text messates
//-----------------------------------------------------------------

// TODO: bouger les gros textes dans des fichiers externes,
// nommés selon la clé. dans la table associative, on met null pour indiquer ça

// TODO: le coup de mettre {3} ... ne me plait pas des masses
// ça me semble un peu fragile, et surtout hyper dur à maintenir
// lorsqu'on modifie le texte. il faut trouver une meilleure solution.

//-----------------------------------------------------------------
/// @class translate is used for getting translation
///                                                                               
//-----------------------------------------------------------------

class translate
{
   private static $sLanguage = 'fr';
   private static $bUseDefaultIfNeeded = true;

   //-----------------------------------------------------------------
   /// @name Language
   /// @{
   //-----------------------------------------------------------------   
   
   /// @brief Get the list of supported languages
   public static function getListLanguages($full = false)
   {
      if ($full)
      return array(  
         'fr'  => 'Français',
         'es'  => 'Español',
         'en'  => 'English',
         'lt' => 'Lithuanian',
         );
      else
      return array(  
         'fr'  => 'Français',
         'es'  => 'Español');
   }
   
   /// @brief Get current language
   static function getLanguage()
   {
      return self::$sLanguage;
   }

   /// @brief Set the translation language (if supported) for content translation
   static function setLanguage($sLanguage)
   {
      if (!self::isSupported($sLanguage))
         $sLanguage = self::$sLanguage;
      self::$sLanguage = $sLanguage;
   }
   
   /// @brief Ensure language is not empty
   public static function ensureLanguage(& $sLanguage)
   {
      if (empty($sLanguage))
         $sLanguage = self::getLanguage();
   }
   
   /// @brief Indicates whether or not a given language is suported
   static function isSupported($sLanguage)
   {
      return array_key_exists($sLanguage, self::getListLanguages(true));
   }
   
   /// @brief Get a supported language from the $_SERVER->HTTP_ACCEPT_LANGUAGE
   /// The string as he form "fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4"
   static function findLanguage($sLanguage)
   {
      // Decompose the string
      $Lang = explode(';',$sLanguage);
      for($i = 0 ; $i < count($Lang) ; $i++)
         $Lang[$i] = explode(',',$Lang[$i]);
      $Lang = flatten($Lang);
      // First the first one we support
      for($i = 0 ; $i < count($Lang) ; $i++) {
         $lang = strtolower(substr(trim($Lang[$i]),0,2));
         if (self::isSupported($lang))
            return $lang;
      }
      // Default value
      return self::$sLanguage;
   }
   
   /// @}
   
   //-----------------------------------------------------------------
   /// @name Various
   /// @{
   //-----------------------------------------------------------------   

   /// @brief Get the country name given the country code
   static function getCountryName($countryCode)
   {
      $aCountryNames = self::getCountryNames();
      return $aCountryNames[$countryCode];
   }
   
   /// @brief Get the list of sex choices
   static function getSexChoices() 
	{
      return array(
         "Male" => translate::t("Male"),
         "Female" => translate::t("Female"));
   }
   
   /// @}

   //-----------------------------------------------------------------
   /// @name Getting translations
   /// @{
   //-----------------------------------------------------------------   
   
   /// @brief Translate a string/file whose name is \p $key
	static function t($key, $bTest = false)
	{
		if (isset(self::$translations[self::$sLanguage][$key]))
         return self::$translations[self::$sLanguage][$key];
      if (isset(self::$translations['fr'][$key]))
         return self::$translations['fr'][$key];
      if ($bTest)
         return false;
      throw new ProcessException("Traduction manquante pour: -$key-");
	}

   /// @brief Translate and replace one parameter
   static function tParam($key, $value)
   {
      $str = self::t($key);
      $str = str_replace("{1}", $value, $str);
      return $str;
   }

   /// @brief Translate and replace parameters
   static function tParams($key, $values)
   {
      $str = self::t($key);
      $iParam = 1;
      foreach ($values as $value)
      {
         $str = str_replace("{".$iParam."}", $value, $str);
         $iParam++;
      }
      return $str;
   }

   /// @brief Indicates whether or not we sould use default text value when
   /// querying database
   public static function setUseDefault($bValue)
   {
      self::$bUseDefaultIfNeeded = $bValue;
   }

   /// @brief Get a language JOIN on table \p $stringsTable where it's field \p $joinFieldStrings 
   /// is equals to \p $joinFieldOther
   public static function getJoinStrings($stringsTable, $joinFieldStrings, $joinFieldOther)
   {
      $sJoin =  "LEFT JOIN ".$stringsTable." ON (".$joinFieldOther." = ".$stringsTable.".".$joinFieldStrings." AND ".$stringsTable.".sLanguage = '".self::$sLanguage."') ";
      if (self::$bUseDefaultIfNeeded)
         $sJoin .= "LEFT JOIN ".$stringsTable." AS ".$stringsTable."_default ON (".$joinFieldOther." = ".$stringsTable."_default.".$joinFieldStrings." AND ".$stringsTable."_default.sLanguage = 'fr') ";
      return $sJoin;
   }

   /// @brief Get a the translation for field \p $field of table \p $stringsTablelanguage 
   public static function getField($stringsTable, $field, $alias = "")
   {
      if (self::$bUseDefaultIfNeeded)
         $sField = "IFNULL(".$stringsTable.".".$field.", ".$stringsTable."_default.".$field.") ";
      else
         $sField = $stringsTable.".".$field."  ";
      if ($alias != "")
         $sField .= "AS ".$alias." ";
      return $sField;
   }
   
   /// @brief Get a the translation for fields \p $aFields of table \p $stringsTablelanguage 
   ///
   /// $aFields can be an array or an associative array (to use aliases) 
   /// ATTENTION : the aliases must be the keys, following the SQL convention
   public static function getFields($stringsTable, $aFields)
   {
      if (empty($aFields))
         return '';
      $aItems = array();
      foreach ($aFields as $sAlias => $sField) 
         $aItems[] = self::getField($stringsTable, $sField, (is_string($sAlias) ? $sAlias : $sField));
      return implode($aItems, ', ');
   }
   
   
   /// @}
   
   private static $translations = array(
      'fr' =>
   array(
      "TextAllowedSymbols" => "Les caractères suivants uniquement sont autorisés dans le nom d'utilisateur : lettres minuscules non accentuées, chiffres, '.', '_' et '-'. De plus, le nom doit faire entre 3 et 15 caractères.",
      "LoginAlreadyUsed" => "Ce nom d'utilisateur est déjà utilisé. Merci d'en choisir un autre ou de récupérer votre compte existant en cliquant sur récupération dans le menu.",
      "EmailAlreadyUsed" => "Cet email est déjà utilisé. Attention, vous n'avez strictement pas le droit d'avoir deux comptes sur le site France-ioi. Si vous avez déjà un compte, déconnectez-vous puis cliquez sur 'récupération' dans le menu de gauche.",
      "PasswordTooShort" => "Le mot de passe doit faire au moins {1} caractères",
       ),
      'es' =>
   array(
      "TextAllowedSymbols" => "Los caracteres siguiente estan permitidas solamente en el nombre del usuario: letras minúsculas sin acentos, números, '.', '_' et '-'. Ademas el nombre debe tener entre 3 y 10 caracteres.",
      "LoginAlreadyUsed" => "Este usuario ya existe.", 
      "EmailAlreadyUsed" => "Este e-mail ya fue utilizado. Cliquee sobre el link de usuarios perdidos para recuperar su contraseña.",
      "PasswordTooShort" => "La contraseña debe tener al menos {1} caracteres",
      ),
      'lt' =>
   array(
      "TextAllowedSymbols" => "Naudotojų varduose leidžiama naudoti tik šiuos simbolius: mažosios lotyniškos raidės, skaitmenys, taškas („.“), apatinis brūkšnys („_“) bei brūkšnelis („-“). Naudotojo vardą turi sudaryti nuo 3 iki 15 simbolių.",
      "LoginAlreadyUsed" => "Šis naudotojo vardas jau užimtas. Pasirinkite kitą arba pasinaudaudokite prisijungimo atstatymo galimybe.",
      "EmailAlreadyUsed" => "Naudotojas šiuo el. paštu jau užregistruotas. Pasinaudokite pamiršto slaptažodžio atgavimo funkcija.",
      "PasswordTooShort" => "Slaptažodį turi sudaryti ne mažiau kaip {1} simboliai",
),
   );

   static function getCountryNames()
   {
      return array(
"" => "--------",
"AF" => "AFGHANISTAN",
"ZA" => "AFRIQUE DU SUD",
"AX" => "ÅLAND, ÎLES",
"AL" => "ALBANIE",
"DZ" => "ALGÉRIE",
"DE" => "ALLEMAGNE",
"AD" => "ANDORRE",
"AO" => "ANGOLA",
"AI" => "ANGUILLA",
"AQ" => "ANTARCTIQUE",
"AG" => "ANTIGUA-ET-BARBUDA",
"AN" => "ANTILLES NÉERLANDAISES",
"SA" => "ARABIE SAOUDITE",
"AR" => "ARGENTINE",
"AM" => "ARMÉNIE",
"AW" => "ARUBA",
"AU" => "AUSTRALIE",
"AT" => "AUTRICHE",
"AZ" => "AZERBAÏDJAN",
"BS" => "BAHAMAS",
"BH" => "BAHREÏN",
"BD" => "BANGLADESH",
"BB" => "BARBADE",
"BY" => "BÉLARUS",
"BE" => "BELGIQUE",
"BZ" => "BELIZE",
"BJ" => "BÉNIN",
"BM" => "BERMUDES",
"BT" => "BHOUTAN",
"BO" => "BOLIVIE, l''ÉTAT PLURINATIONAL DE",
"BA" => "BOSNIE-HERZÉGOVINE",
"BW" => "BOTSWANA",
"BV" => "BOUVET, ÎLE",
"BR" => "BRÉSIL",
"BN" => "BRUNÉI DARUSSALAM",
"BG" => "BULGARIE",
"BF" => "BURKINA FASO",
"BI" => "BURUNDI",
"KY" => "CAÏMANES, ÎLES",
"KH" => "CAMBODGE",
"CM" => "CAMEROUN",
"CA" => "CANADA",
"CV" => "CAP-VERT",
"CF" => "CENTRAFRICAINE, RÉPUBLIQUE",
"CL" => "CHILI",
"CN" => "CHINE",
"CX" => "CHRISTMAS, ÎLE",
"CY" => "CHYPRE",
"CC" => "COCOS (KEELING), ÎLES",
"CO" => "COLOMBIE",
"KM" => "COMORES",
"CG" => "CONGO",
"CD" => "CONGO, LA RÉPUBLIQUE DÉMOCRATIQUE DU",
"CK" => "COOK, ÎLES",
"KR" => "CORÉE, RÉPUBLIQUE DE",
"KP" => "CORÉE, RÉPUBLIQUE POPULAIRE DÉMOCRATIQUE DE",
"CR" => "COSTA RICA",
"CI" => "CÔTE D''IVOIRE",
"HR" => "CROATIE",
"CU" => "CUBA",
"DK" => "DANEMARK",
"DJ" => "DJIBOUTI",
"DO" => "DOMINICAINE, RÉPUBLIQUE",
"DM" => "DOMINIQUE",
"EG" => "ÉGYPTE",
"SV" => "EL SALVADOR",
"AE" => "ÉMIRATS ARABES UNIS",
"EC" => "ÉQUATEUR",
"ER" => "ÉRYTHRÉE",
"ES" => "ESPAGNE",
"EE" => "ESTONIE",
"US" => "ÉTATS-UNIS",
"ET" => "ÉTHIOPIE",
"FK" => "FALKLAND, ÎLES (MALVINAS)",
"FO" => "FÉROÉ, ÎLES",
"FJ" => "FIDJI",
"FI" => "FINLANDE",
"FR" => "FRANCE",
"GA" => "GABON",
"GM" => "GAMBIE",
"GE" => "GÉORGIE",
"GS" => "GÉORGIE DU SUD ET LES ÎLES SANDWICH DU SUD",
"GH" => "GHANA",
"GI" => "GIBRALTAR",
"GR" => "GRÈCE",
"GD" => "GRENADE",
"GL" => "GROENLAND",
"GP" => "GUADELOUPE",
"GU" => "GUAM",
"GT" => "GUATEMALA",
"GG" => "GUERNESEY",
"GN" => "GUINÉE",
"GW" => "GUINÉE-BISSAU",
"GQ" => "GUINÉE ÉQUATORIALE",
"GY" => "GUYANA",
"GF" => "GUYANE FRANÇAISE",
"HT" => "HAÏTI",
"HM" => "HEARD, ÎLE ET MCDONALD, ÎLES",
"HN" => "HONDURAS",
"HK" => "HONG-KONG",
"HU" => "HONGRIE",
"IM" => "ÎLE DE MAN",
"UM" => "ÎLES MINEURES ÉLOIGNÉES DES ÉTATS-UNIS",
"VG" => "ÎLES VIERGES BRITANNIQUES",
"VI" => "ÎLES VIERGES DES ÉTATS-UNIS",
"IN" => "INDE",
"ID" => "INDONÉSIE",
"IR" => "IRAN, RÉPUBLIQUE ISLAMIQUE D''",
"IQ" => "IRAQ",
"IE" => "IRLANDE",
"IS" => "ISLANDE",
"IL" => "ISRAËL",
"IT" => "ITALIE",
"JM" => "JAMAÏQUE",
"JP" => "JAPON",
"JE" => "JERSEY",
"JO" => "JORDANIE",
"KZ" => "KAZAKHSTAN",
"KE" => "KENYA",
"KG" => "KIRGHIZISTAN",
"KI" => "KIRIBATI",
"KW" => "KOWEÏT",
"LA" => "LAO, RÉPUBLIQUE DÉMOCRATIQUE POPULAIRE",
"LS" => "LESOTHO",
"LV" => "LETTONIE",
"LB" => "LIBAN",
"LR" => "LIBÉRIA",
"LY" => "LIBYENNE, JAMAHIRIYA ARABE",
"LI" => "LIECHTENSTEIN",
"LT" => "LITUANIE",
"LU" => "LUXEMBOURG",
"MO" => "MACAO",
"MK" => "MACÉDOINE, L''EX-RÉPUBLIQUE YOUGOSLAVE DE",
"MG" => "MADAGASCAR",
"MY" => "MALAISIE",
"MW" => "MALAWI",
"MV" => "MALDIVES",
"ML" => "MALI",
"MT" => "MALTE",
"MP" => "MARIANNES DU NORD, ÎLES",
"MA" => "MAROC",
"MH" => "MARSHALL, ÎLES",
"MQ" => "MARTINIQUE",
"MU" => "MAURICE",
"MR" => "MAURITANIE",
"YT" => "MAYOTTE",
"MX" => "MEXIQUE",
"FM" => "MICRONÉSIE, ÉTATS FÉDÉRÉS DE",
"MD" => "MOLDOVA, RÉPUBLIQUE DE",
"MC" => "MONACO",
"MN" => "MONGOLIE",
"ME" => "MONTÉNÉGRO",
"MS" => "MONTSERRAT",
"MZ" => "MOZAMBIQUE",
"MM" => "MYANMAR",
"NA" => "NAMIBIE",
"NR" => "NAURU",
"NP" => "NÉPAL",
"NI" => "NICARAGUA",
"NE" => "NIGER",
"NG" => "NIGÉRIA",
"NU" => "NIUÉ",
"NF" => "NORFOLK, ÎLE",
"NO" => "NORVÈGE",
"NC" => "NOUVELLE-CALÉDONIE",
"NZ" => "NOUVELLE-ZÉLANDE",
"IO" => "OCÉAN INDIEN, TERRITOIRE BRITANNIQUE DE L''",
"OM" => "OMAN",
"UG" => "OUGANDA",
"UZ" => "OUZBÉKISTAN",
"PK" => "PAKISTAN",
"PW" => "PALAOS",
"PS" => "PALESTINIEN OCCUPÉ, TERRITOIRE",
"PA" => "PANAMA",
"PG" => "PAPOUASIE-NOUVELLE-GUINÉE",
"PY" => "PARAGUAY",
"NL" => "PAYS-BAS",
"PE" => "PÉROU",
"PH" => "PHILIPPINES",
"PN" => "PITCAIRN",
"PL" => "POLOGNE",
"PF" => "POLYNÉSIE FRANÇAISE",
"PR" => "PORTO RICO",
"PT" => "PORTUGAL",
"QA" => "QATAR",
"RE" => "RÉUNION",
"RO" => "ROUMANIE",
"GB" => "ROYAUME-UNI",
"RU" => "RUSSIE, FÉDÉRATION DE",
"RW" => "RWANDA",
"EH" => "SAHARA OCCIDENTAL",
"BL" => "SAINT-BARTHÉLEMY",
"SH" => "SAINTE-HÉLÈNE, ASCENSION ET TRISTAN DA CUNHA",
"LC" => "SAINTE-LUCIE",
"KN" => "SAINT-KITTS-ET-NEVIS",
"SM" => "SAINT-MARIN",
"MF" => "SAINT-MARTIN",
"PM" => "SAINT-PIERRE-ET-MIQUELON",
"VA" => "SAINT-SIÈGE (ÉTAT DE LA CITÉ DU VATICAN)",
"VC" => "SAINT-VINCENT-ET-LES GRENADINES",
"SB" => "SALOMON, ÎLES",
"WS" => "SAMOA",
"AS" => "SAMOA AMÉRICAINES",
"ST" => "SAO TOMÉ-ET-PRINCIPE",
"SN" => "SÉNÉGAL",
"RS" => "SERBIE",
"SC" => "SEYCHELLES",
"SL" => "SIERRA LEONE",
"SG" => "SINGAPOUR",
"SK" => "SLOVAQUIE",
"SI" => "SLOVÉNIE",
"SO" => "SOMALIE",
"SD" => "SOUDAN",
"LK" => "SRI LANKA",
"SE" => "SUÈDE",
"CH" => "SUISSE",
"SR" => "SURINAME",
"SJ" => "SVALBARD ET ÎLE JAN MAYEN",
"SZ" => "SWAZILAND",
"SY" => "SYRIENNE, RÉPUBLIQUE ARABE",
"TJ" => "TADJIKISTAN",
"TW" => "TAÏWAN, PROVINCE DE CHINE",
"TZ" => "TANZANIE, RÉPUBLIQUE-UNIE DE",
"TD" => "TCHAD",
"CZ" => "TCHÈQUE, RÉPUBLIQUE",
"TF" => "TERRES AUSTRALES FRANÇAISES",
"TH" => "THAÏLANDE",
"TL" => "TIMOR-LESTE",
"TG" => "TOGO",
"TK" => "TOKELAU",
"TO" => "TONGA",
"TT" => "TRINITÉ-ET-TOBAGO",
"TN" => "TUNISIE",
"TM" => "TURKMÉNISTAN",
"TC" => "TURKS ET CAÏQUES, ÎLES",
"TR" => "TURQUIE",
"TV" => "TUVALU",
"UA" => "UKRAINE",
"UY" => "URUGUAY",
"VU" => "VANUATU",
"VE" => "VENEZUELA, RÉPUBLIQUE BOLIVARIENNE DU",
"VN" => "VIET NAM",
"WF" => "WALLIS ET FUTUNA",
"YE" => "YÉMEN",
"ZM" => "ZAMBIE",
"ZW" => "ZIMBABWE");
   }
   
}

?>
