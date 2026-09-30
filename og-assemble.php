<?php
// one-shot: stitch binary chunks into og-resumes.png, md5-verify, clean up
$parts = glob(__DIR__ . '/ogpart_*');
sort($parts);
if (count($parts) < 4) { http_response_code(500); die('PARTS_FAIL count=' . count($parts)); }
$data = '';
foreach ($parts as $p) { $data .= file_get_contents($p); }
file_put_contents(__DIR__ . '/og-resumes.png', $data);
$md5 = md5_file(__DIR__ . '/og-resumes.png');
echo 'TOTAL ' . strlen($data) . ' bytes, md5=' . $md5;
if ($md5 === '588f21fe8cea95be22c8fcf7d926b70e') {
    foreach ($parts as $p) { unlink($p); }
    @unlink(__DIR__ . '/og-fetch.php');
    unlink(__FILE__);
    echo ' MATCH - cleaned up';
} else {
    echo ' MISMATCH - parts kept for retry';
}
