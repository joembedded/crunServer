<?php
/* LTX Distribution TextReplacmenet Tool
* Call: ltxreplace.php SRCFILE Old New
*
*
* Versions
* 1.00  Checked for PHP8
*/

echo "--- ltxreplace.php (C)JoEmbedded.de V1.02 ---\n";

$srcfile;
$old_str;
$new_str;


for ($i = 1; $i < count($argv); $i++) {
	$arg = $argv[$i];
	if (isset($new_str)) {
		echo "ERROR: Unknown Argument '$arg'\n";
		exit(-1);
	} else if (isset($old_str)) {
		$new_str = $arg;
	} else if (isset($srcfile)) {
		$old_str = $arg;
	} else {
		$srcfile = $arg;
	}
}

if (!isset($srcfile) || !isset($old_str) || !isset($new_str)) {
	echo "ERROR: Arguments SRCFILE OLD_STR NEW_STR\n";
	exit(-1);
}

echo "File: '$srcfile', replace '$old_str' by '$new_str'\n";

if(!file_exists($srcfile)){
	echo "ERROR: File '$srcfile' not found!\n";
	exit(-1);
}

$inp = file_get_contents($srcfile);

$rep = str_replace($old_str, $new_str, $inp);

$res = file_put_contents($srcfile,$rep);
if( $res === false ){
	echo "ERROR: File '$srcfile' Write Error!\n";
	exit(-1);
}

echo "OK";

