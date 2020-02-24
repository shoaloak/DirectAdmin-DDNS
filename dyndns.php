<?php

include 'httpsocket.php';

if (file_exists("credentials.json")) {
    $credentials = json_decode(file_get_contents("credentials.json"), true);
} else {
    exit("No credentials.json found! aborting...");
}
$server_login = $credentials['username'];
$server_pass = $credentials['password'];
$server_host = $credentials['hostname'];
$domain = $credentials['domain'];
$subdomain = $credentials['subdomain'];

$storage = "ddns.dat";
$server_port = 2222;
$server_ssl = "Y";

$url = 'http://ipecho.net/plain';
$ch = curl_init(); // undefined ? "apt install php-curl" : "yay ^^";
$timeout = 5;

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

$publicip = curl_exec($ch);

echo ("Public IPV4: " . $publicip . "\n");

curl_close($ch);

$data = file_exists($storage) ? json_decode(file_get_contents($storage), true) : False;

if ($data["publicip"] === $publicip) {
    echo ("Public IP has not changed, no update needed.\n");
} else {
    echo ("Public IP has changed, updating DNS to $publicip...\n");

    $sock = new HTTPSocket;
    if ($server_ssl == 'Y') {
        $sock->connect("ssl://" . $server_host, $server_port);
    } else {
        //$sock->connect($server_host, $server_port);
        exit("Cant connect using SSL/TLS, aborting...");
    }

    $sock->set_login($server_login, $server_pass);

    $sock->query(
        '/CMD_API_DNS_CONTROL',
        array(
            'domain' => $domain,
            'action' => 'add',
            'type' => 'A',
            'name' => $subdomain . '.',
            'value'    => $publicip,
        )
    );

    $result = $sock->fetch_parsed_body();

    $data = array();
    $data["publicip"] = $publicip;
    file_put_contents($storage, json_encode($data));
}
