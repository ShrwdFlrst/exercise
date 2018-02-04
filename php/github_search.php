<?php

if (empty($argv[1]) || empty($argv[2])) {
    echo "USAGE: github_search.php GITHUB_TOKEN \"search_term1 search_term2...\" ".PHP_EOL.PHP_EOL;
    exit;
}

require_once __DIR__.'/src/GithubSearch.php';
require_once __DIR__.'/src/Request.php';

$token = $argv[1];
$phrase = $argv[2];


// Make a search request
$request = new \ShrwdFlrst\Request();
$githubSearch = new \ShrwdFlrst\GithubSearch($request, $token);
$results = $githubSearch->searchDescription($phrase);
$total = count($results);

//foreach ($results as $r) {
//    var_dump($r['description']);
//}

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
