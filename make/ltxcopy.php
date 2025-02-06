<?php
/* LTX Distribution Copy Tool
* Call: ltxcopy.php SRCDIR DESTDIR [-OPTIONS]
*
* OPTIONS:
* -s: run silent
* -tTARGET: list of concatedenated Strings that OR must be included in '.ltxrules':
*   '.ltxrules' is always ignored and contains 1-n target(s) per line, e.g. 'STATIC\nDISTRI\nTEST'
*   Only if one target is contained in TARGET, this directory will be copied.
*   e.g. here '-txxtSTATICxxBASICxxDISTRI' or '-tTEST#STATIC' would include the directory, whereas '-txxDISsTRI' would NOT
*
* Versions
* 1.01  Checked for PHP8
* 1.02	No Copy of Directories starting with '.' (e.g. '.git')

* 1.03  Scan for Version: ***WINDOWS***
* 		in css: --version: 'V1.01 / 31.01.2025';
*		in JS: const VERSION = 'V0.16 / 12.01.2025'
* 1.04	in PHP define("VERSION", "V1.00 / 03.02.2025");
*
*       WICHTIG:
*		Benennung der Version EXAKT nach obigem System Vx.yy / tt.mm.yyyy		
*		Varianten in ..../jolib/ archivieren!
*       Geht Nur auf WINDOWS
* 		Ziel ist immer 1. Unterverzeichnis von PHP -> jolib
*
*/


define("VERSION", "V1.04 / 03.02.2025");


// File zu JoLib addieren wenn neuer oder laenger
// Wichtig: 
function jolib($fname,$version,&$cont){
	$nversion = str_replace(array(' ','/','.'),'_',$version);
	$pfad_info = pathinfo($fname);	
	$name = $pfad_info['filename'];
	$endung = $pfad_info['extension']; 
	$vname=str_pad($name,16,'_').$nversion.'.'.$endung;

	$mdir = explode('\\', __DIR__); // ***WINDOWS***

	$libdir = $mdir[0].'/'.$mdir[1].'/jolib';
	if(!file_exists($libdir)) mkdir($libdir);
	
	$sizeinlib = @filesize($libdir.'/'.$vname);
	$ocnt = strlen($cont);
	if($sizeinlib != $ocnt){
		// 2 Vorversionen behalten
		@unlink($libdir.'/__'.$vname);
		@rename($libdir.'/_'.$vname,$libdir.'/__'.$vname);
		@rename($libdir.'/'.$vname,$libdir.'/_'.$vname);
		file_put_contents($libdir.'/'.$vname,$cont);
		echo "\n\n\n--------------------------------------------------------------------\n";
		echo "                         Update Lib '$vname'\n";
		echo "--------------------------------------------------------------------\n\n\n";
	}
	
}

function lvcopy($srcd,$destd,$fname){
	//echo "Copy $srcd => $destd: File $fname\n";

	$scont=file_get_contents("$srcd/$fname");
	if($scont === false) return false;

	$verp = (strpos($scont,"const VERSION = "));
	if($verp !== false){
		$sl = strlen("const VERSION = ");
		$trenner = $scont[$verp+$sl];
		$verpe=strpos($scont,$trenner,$verp+$sl+1);
		$version=substr($scont,$verp+$sl+1,$verpe-$verp-$sl-1);
		jolib($fname,$version,$scont);
	}
	$verp = (strpos($scont,"--version: "));
	if($verp !== false){
		$sl = strlen("--version: ");
		$trenner = $scont[$verp+$sl];
		$verpe=strpos($scont,$trenner,$verp+$sl+1);
		$version=substr($scont,$verp+$sl+1,$verpe-$verp-$sl-1);
		jolib($fname,$version,$scont);
	}
	$verp = (strpos($scont,'define("VERSION", '));
	if($verp !== false){
		$sl = strlen('define("VERSION", ');
		$trenner = $scont[$verp+$sl];
		$verpe=strpos($scont,$trenner,$verp+$sl+1);
		$version=substr($scont,$verp+$sl+1,$verpe-$verp-$sl-1);
		jolib($fname,$version,$scont);
	}


	$res = file_put_contents("$destd/$fname",$scont);
	return $res;
}


echo "--- ltxcopy.php (C)JoEmbedded.de V1.03 ---\n";
$recurse = true;
$silent = false;
$file_cnt = 0;
$file_mem = 0;
$dir_cnt = 0;


for ($i = 1; $i < count($argv); $i++) {
	$arg = $argv[$i];
	if ($arg[0] == '-') {
		$opt = substr($arg, 1);
		switch ($opt[0]) {
			case 't':	// e.g. -tHELLO or HELLO:STATIC as target
				$target = substr($opt, 1);
				break;
			case 's':
				$silent = true;
				break;
			default:
				echo "ERROR: Unknown Option '-$opt'\n";
				exit(-1);
		}
	} else if (isset($dest_dir)) {
		echo "ERROR: Unknown Argument '$dir'\n";
		exit(-1);
	} else if (isset($src_dir)) {
		$dest_dir = $arg;
	} else {
		$src_dir = $arg;
	}
}

if (!isset($src_dir) || !isset($dest_dir)) {
	echo "ERROR: Arguments SRC_DIR DEST_DIR [-OPTIONS]\n";
	exit(-1);
}

if (isset($target)) {
	echo "Target: '$target'\n";
} else {
	$target = "";
	echo "Target: '<none>'\n";
}

// Fkts
function copy_dir($src_workdir, $dest_workdir, $ind)
{
	global $silent, $target;
	global $file_cnt, $file_mem, $dir_cnt;
	//echo "$ind"."Directory '$src_workdir':\n";
	if (file_exists($src_workdir)) {
		// Check rules
		if (file_exists("$src_workdir/.ltxrules")) {
			$ltxrules = file("$src_workdir/.ltxrules", FILE_IGNORE_NEW_LINES);
			//echo "-> Found ltxrules:\n";
			$found = false;
			foreach ($ltxrules as $rule) {
				if (!strlen($rule)) continue;	// No Check of empty rules
				if (strpos($target, $rule) !== false) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				if (!$silent) {
					echo "Directory '$src_workdir' skipped (Rule '$rule' not set)\n";
				}
				return 1;	// Skip this 
			}
		}

		if (!file_exists($dest_workdir)) {
			// echo "Makedir: [$dest_workdir]\n";
			if (!mkdir($dest_workdir)) {
				echo "ERROR: Failed to Make Directory '$dest_workdir'\n";
				exit(-1);
			}
		} // else echo "Dir. exists: [$dest_workdir]\n";

		$list = scandir($src_workdir);
		if (count($list)) {
			for ($path = 0; $path < 2; $path++) {
				foreach ($list as $file) {
					if ($file == '.') continue;
					if ($file == '..') continue;
					if (is_dir("$src_workdir/$file")) {
						if ($path == 0) {	// Only Directories not starting with '.'!
							if($file[0] !== '.'){
								$dir_cnt++;
								if (!$silent) echo "$ind" . "[$file]\n";
								$res = copy_dir("$src_workdir/$file", "$dest_workdir/$file", $ind . "   ");
							}
						}
					} else {
						if ($path == 1) {
							if ($file == '.ltxrules') continue;	// Ignore rules in copy
							$file_cnt++;
							$ds = filesize("$src_workdir/$file");
							$file_mem += $ds;
							if (!$silent) {
								$dts = filemtime("$src_workdir/$file");
								$ddate = date("d.m.Y_H:i:s", $dts);
								echo "$ind" . str_pad($file, 24, ' ') . "$ds $ddate\n";
							}
							// echo "Copy: [$src_workdir/$file] -> [$dest_workdir/$file]\n";
							if(!lvcopy("$src_workdir","$dest_workdir", $file)){
								echo "ERROR: Failed to Copy '$src_workdir/$file' to '$dest_workdir/$file'\n";
								exit(-1);
							}
						}
					}
				}
			}
		}
	} else {
		echo "ERROR: Directory '$src_workdir' not found\n";
	}
	return 0;
}

copy_dir($src_dir, $dest_dir, "");

echo intval($file_mem / 1024) . " kB, $file_cnt Files, $dir_cnt Directories\n";
