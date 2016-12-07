<?php

require_once __DIR__.'/../config.php';

function getStrings($language = null, $customStringsName = null) {
   global $config;
   if (!$language) $language = $config->shared->defaultLanguage;
   if (!$customStringsName) $customStringsName = $config->shared->customStringsName;
   $res = json_decode(file_get_contents(__DIR__.'/../i18n/'.$language.'/login.json'), true);
   if ($customStringsName) {
	   $customStrings = json_decode(file_get_contents(__DIR__.'/../i18n/'.$language.'/'.$customStringsName.'.json'), true);
	   $res = array_merge($normalStrings, $customStrings);
   }
   return $res;
}