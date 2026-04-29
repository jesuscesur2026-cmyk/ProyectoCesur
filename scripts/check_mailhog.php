<?php
echo "Checking MailHog connectivity\n";
$host = 'mailhog';
echo "gethostbyname($host): ";
$ip = gethostbyname($host);
echo $ip . "\n";

echo "Trying TCP connect to $host:1025... ";
$errno = 0; $errstr = '';
$s = @fsockopen($host, 1025, $errno, $errstr, 2);
if ($s) {
    echo "ok\n";
    fclose($s);
} else {
    echo "err: " . ($errstr ?: $errno) . "\n";
}

echo "Trying connect to 127.0.0.1:1025 (host) ... ";
$s2 = @fsockopen('127.0.0.1', 1025, $e2, $m2, 2);
if ($s2) {
    echo "ok\n";
    fclose($s2);
} else {
    echo "err: " . ($m2 ?: $e2) . "\n";
}

echo "Done.\n";
