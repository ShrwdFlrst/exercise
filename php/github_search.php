<?php

$token = $argv[1];
$terms = $argv[2];
$terms = explode(" ", $terms);

$agent = 'Exercise App';
$url = "https://api.github.com/search/repositories";

$query = array_reduce($terms, function ($carry, $term) {
    $carry .= sprintf('%s in:description ', $term);

    return $carry;
});
$query = str_replace(' ', '+', trim($query));
$url = $url . '?q=' . $query;
$header = array();
$header[] = 'Authorization: token ' . $token;
$header[] = 'Accept: application/vnd.github.mercy-preview+json';

var_dump($url);
//die;
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);

$output = curl_exec($ch);

if ($output === false) {
     throw new \Exception('Curl error: ' . curl_error($ch));
}
$result = json_decode($output, true);
curl_close($ch);
var_dump($result);