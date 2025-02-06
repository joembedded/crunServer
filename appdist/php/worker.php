<?PHP
/* worker.php fuer CrunServer (lokal)
Test: http://localhost/wrk/crunserver/appdev/php/worker.php?cmd=
*/
error_reporting(E_ALL);

try {
    //-------- MAIN -------------
    $cmd = @$_REQUEST['cmd'] ?? '';
    $now = time();

	$ores = [];	// Result Object
	switch($cmd){
	case 'clist': // List all .crun file in crun
        $clist = [];
        foreach (glob(__DIR__ . '/../crun/*.crun') as $cfile) {
            if ($cfile[0] == '.') continue;
            $clist[]=substr($cfile,strrpos($cfile,'/')+1);
        }
        $ores['clist'] = $clist;
		break;
	default:
		$ores['error'] = 'Cmd. Unknown';
	}
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($ores);
} catch (Exception $e) {
    header("Content-type: text/plain");
    echo "ERROR: " . $e->getMessage();
}
