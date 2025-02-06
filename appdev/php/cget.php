<?PHP
/* cget.php fuer CrunServer
Test: http://localhost/wrk/crunserver/appdev/php/cget.php?cf=orbcomm_init_1hMP.crun
*/
error_reporting(E_ALL);

try {
    //-------- MAIN -------------
    header('Access-Control-Allow-Origin: *');    // CORS enabler
    header("Content-type: text/plain");
    $file = @$_REQUEST['cf'] ?? '';
    if ( strpos($file, '.') < 1 ) throw new Exception("No Access"); // Nur echte Filenames mit Extension
    $lpath = __DIR__ . "/../crun/$file";
    $cont = @file_get_contents($lpath);
    if ($cont === false) throw new Exception("File not found");
    echo $cont;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
