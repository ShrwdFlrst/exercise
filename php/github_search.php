<?php

$token = $argv[1];
$terms = $argv[2];
$terms = explode(" ", $terms);

$url = "https://api.github.com/search/repositories";

// Make sure we search for each term using AND
$query = array_reduce($terms, function ($carry, $term) {
    $carry .= sprintf('%s in:description ', $term);

    return $carry;
});

$query = str_replace(' ', '+', trim($query));
$url = $url . '?q=' . $query;
$results = [];
// The limit is 100 results per page: https://developer.github.com/v3/guides/traversing-with-pagination/
$perPage = 100;
// We want this many
$maxResults = 1000;
$total = 0;

for ($p = 1; $p <= ceil($maxResults / $perPage); $p++) {
    $urlWithPagination = sprintf("%s&per_page=%s&page=%s", $url, $perPage, $p);
    $result = makeSearchRequest($urlWithPagination, $token);

    if (count($result['items']) > 0) {
        $results = array_merge($results, $result['items']);
    }

    if ($p * $perPage >= $result['total_count']) {
        $total = $result['total_count'];
        break;
    }
}

// #3 Filter out any repos with an empty "language" (null or empty String - and assuming 0/false value)
$results = array_filter($results, function ($item) {
    return !empty($item['language']);
});

// #4 Group the remaining list of repos by "language", and count the number of occurrences for each
$languages = [];
foreach ($results as $result) {
    $languages[$result['language']] = isset($languages[$result['language']]) ? $languages[$result['language']] + 1 : 1;
}

// #5 Sort the languages by occurrence descending
arsort($languages);

foreach ($languages as $language => $count) {
    // #6 Output a line for each result, in the {language}: {count} format
    echo sprintf("%s: %s%s", $language, $count, PHP_EOL);
}

// #7 After the results, on a separate line, output the total number of search results in the format: => {total_count} total result(s) found
//echo sprintf("%s=> %s total result(s) found%s", PHP_EOL, array_sum($languages), PHP_EOL);

// This is the total including the filtered out blank results which matches the example from the instructions
// The commented out line above is the total sum of the languages displayed only, excluding blanks.
echo sprintf("%s=> %s total result(s) found%s", PHP_EOL, $total, PHP_EOL);



function makeSearchRequest($url, $token)
{
    $agent = 'Exercise App';
    $header = array();
    $header[] = 'Authorization: token ' . $token;
    $header[] = 'Accept: application/vnd.github.mercy-preview+json';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);

    $output = curl_exec($ch);

    if ($output === false) {
        $error = curl_error($ch);
    }

    curl_close($ch);

    if (!empty($error)) {
        throw new \Exception('Curl error: ' . $error);
    }

    $result = json_decode($output, true);

    if (empty($result)) {
        throw new \Exception('Invalid JSON: ' . $output);
    }


    return $result;
}
