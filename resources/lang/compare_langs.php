<?php

// Script to check for differences between two language folders

if($argc < 3) {
    die("Error : not enough arguments. Please specify two languages.\n");
}
if(!is_dir(__DIR__.'/'.$argv[1])) {
    die("Error : first argument is not a language folder.\n");
}
if(!is_dir(__DIR__.'/'.$argv[2])) {
    die("Error : second argument is not a language folder.\n");
}

function keysCompare($refArray, $targetArray, $prefix='') {
    if(!is_array($targetArray)) {
        echo ($prefix ? $prefix : '[root]')." is not an array in target language.\n";
        return;
    }
    foreach($refArray as $key => $val) {
        if(!array_key_exists($key, $targetArray)) {
            $refVal = is_array($val) ? '[array]' : '`'.$val.'`';
            echo $key." not found in target language (ref: ".$refVal.").\n";
            continue;
        }
        if(is_array($val)) {
            keysCompare($val, $targetArray[$key], $key.'.');
        }
    }
}

function compare($reference, $target, $subdir='') {
    foreach(scandir($reference.$subdir) as $f) {
        if($f == '.' || $f == '..') { continue; }
        if(is_dir($reference.$subdir.$f)) {
            compare($reference, $target, $subdir . $f . '/');
            continue;
        }
        echo "* Comparing `".$subdir.$f."`...\n";
        if(!is_file($target.$subdir.$f)) {
            echo "File not found in target language.\n";
            continue;
        }
        try {
            $refArray = include($reference.$subdir.$f);
            $targetArray = include($target.$subdir.$f);
            keysCompare($refArray, $targetArray);
        } catch(Exception $e) {
            echo "Error while comparing.\n";
        }
    }
}
compare(__DIR__.'/'.$argv[1].'/', __DIR__.'/'.$argv[2].'/');
