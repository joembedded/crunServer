<?PHP
/* auth.php
CrunServer Authentifizierung via x Zeichen String 
Test: http://localhost/wrk/crunserver/appdev/auth.php

Create Token and delete old auth / dyndata

***OnlyAsFragment, CurrentlyUnused****

*/
error_reporting(E_ALL);
define("MAX_AUTH_AGE", 3600); // Dynamische Daten per Default 1h liegen lassen


header("Content-type: plain/text"); // DBG

try {
    // Returns a string of len random characters
    function cz_string($len)
    {
        $pool = "0123456789abcdefghijklmnopqrstuvwxyz";
        $res = '';
        while ($len--) $res .= $pool[random_int(0, strlen($pool) - 1)];
        return $res;
    }

    //-------- MAIN -------------
    $key = @$_REQUEST['k'] ?? '';
    $authstr = cz_string(10);
    $now = time();

    if (!file_exists(__DIR__ . '/auth')) mkdir(__DIR__ . '/auth');
    if (!file_exists(__DIR__ . '/dyndata')) mkdir(__DIR__ . '/dyndata');

    // Remove old Tokens
    foreach (glob(__DIR__ . '/auth/*') as $sfile) {
        if ($sfile[0] == '.') continue;
        $age = $now - filemtime($sfile);
        if ($age > MAX_AUTH_AGE) unlink($sfile);
    }
    // Remove old Data
    foreach (glob(__DIR__ . '/dyndata/*') as $sfile) {
        if ($sfile[0] == '.') continue;
        $age = $now - filemtime($sfile);
        if ($age > MAX_AUTH_AGE) unlink($sfile);
    }


    // Store new token (plain)
    file_put_contents(__DIR__ . "/auth/$authstr", $key);

    // Store some testdata
    // file_put_contents(__DIR__ . "/dyndata/$authstr.crun", "Hello World");

    header("Content-type: application/json; charset=utf-8");
    header('Access-Control-Allow-Origin: *');    // CORS enabler
    echo json_encode(['auth' => $authstr]);
} catch (Exception $e) {
    header("Content-type: text/plain");
    echo "ERROR: " . $e->getMessage();
}
