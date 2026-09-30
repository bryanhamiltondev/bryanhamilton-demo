<?php
// one-shot: pull og-resumes.png from littlebird file link, verify md5, self-delete on match
function _get($u) {
    if (function_exists('curl_init')) {
        $ch = curl_init($u);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'
        ));
        $d = curl_exec($ch);
        curl_close($ch);
        return $d;
    }
    $ctx = stream_context_create(array('http' => array('header' => "User-Agent: Mozilla/5.0\r\n")));
    return @file_get_contents($u, false, $ctx);
}
$targets = array(
    'https://app.littlebird.ai/f/ad05aa9d-8fd2-41ff-a5b7-f39bcd333c5d'
);
$data = '';
foreach ($targets as $u) {
    $d = _get($u);
    if ($d && strlen($d) > 50000 && substr($d, 0, 4) === "\x89PNG") { $data = $d; break; }
    echo 'TARGET_FAIL len=' . ($d === false ? 'false' : strlen($d)) . '<br>';
}
if ($data === '') { http_response_code(502); die('FETCH_FAIL'); }
file_put_contents(__DIR__ . '/og-resumes.png', $data);
$md5 = md5_file(__DIR__ . '/og-resumes.png');
echo 'SAVED ' . strlen($data) . ' bytes, md5=' . $md5;
if ($md5 === '588f21fe8cea95be22c8fcf7d926b70e') {
    echo ' MATCH - self-deleting';
    unlink(__FILE__);
} else {
    echo ' MISMATCH - keeping script for retry';
}
