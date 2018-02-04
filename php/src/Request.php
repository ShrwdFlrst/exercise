<?php

namespace ShrwdFlrst;

/**
 * Class Request
 * @package ShrwdFlrst
 */
class Request
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $agent = 'Simple Request App';

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $url
     * @return string
     * @throws \Exception
     */
    public function get($url)
    {
        $ch = $this->getHandle();
        curl_setopt($ch, CURLOPT_URL, $url);
        $output = curl_exec($ch);

        if ($output === false) {
            $error = curl_error($ch);
        }

        curl_close($ch);

        if (!empty($error)) {
            throw new \Exception('Curl error: ' . $error);
        }

        return $output;
    }

    /**
     * @return resource
     */
    private function getHandle()
    {
        $ch = curl_init();

        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);

        return $ch;
    }
}