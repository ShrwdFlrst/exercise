<?php

namespace ShrwdFlrst;

/**
 * Class GithubSearch
 */
class GithubSearch
{
    const BASE_URL = "https://api.github.com/search/repositories";
    const PER_PAGE = 100;
    const MAX_RESULTS = 1000;

    /**
     * @var Request
     */
    private $request;

    /**
     * GithubSearch constructor.
     * @param Request $request
     * @param string $token
     */
    public function __construct($request, $token)
    {
        $headers = array();
        $headers[] = 'Authorization: token ' . $token;
        $headers[] = 'Accept: application/vnd.github.mercy-preview+json';
        $request->setHeaders($headers);
        $this->request = $request;
    }

    /**
     * @param string $phrase
     * @return array
     * @throws \Exception
     */
    public function searchDescription($phrase)
    {
        // Search for the full exact phrase, not just all the words individually
        // in any order by using quotes and encoding the query value.
        $query = sprintf("\"%s\" in:description", $phrase);
        $url = self::BASE_URL . '?q=' . urlencode($query);
        $results = [];

        for ($p = 1; $p <= ceil(self::MAX_RESULTS / self::PER_PAGE); $p++) {
            $urlWithPagination = sprintf(
                "%s&per_page=%s&page=%s",
                $url,
                self::PER_PAGE,
                $p
            );
            $result = $this->getResult($urlWithPagination);

            if (!empty($result['items'])) {
                $results = array_merge($results, $result['items']);
            }

            if ($p * self::PER_PAGE >= $result['total_count']) {
                break;
            }
        }

        return $results;
    }

    /**
     * @param string $url
     * @return array of decoded json
     * @throws \Exception
     */
    private function getResult($url)
    {
        $output = $this->request->get($url);
        $result = json_decode($output, true);

        if (empty($result)) {
            throw new \Exception('Invalid JSON: ' . $output);
        }

        return $result;
    }
}