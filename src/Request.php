<?php

namespace loisium;


class Request {
    private $timeout = 3000;
    private $connectTimeout = 3000;
    private $headers = [];
    private $cookies = [];
    private $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0';
    private $statusCode = 0;
    private $error = '';
    private $errorCode = 0;
    private $proxy = '';
    private $curl = null;

    public function __construct() {
        $this->init();
    }

    private function init() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
    }

    private function before_request($url) {
        /* 设置UserAgent */
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);
        /* 设置超时时间 */
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $this->timeout);
        /* 设置连接地址 */
        curl_setopt($this->curl, CURLOPT_URL, $url);
        /* 设置Cookie */
        if (empty($this->cookies)) {
            $cookieTmpArr = [];
            foreach ($this->cookies as $key => $value) {
                $cookieTmpArr[] = $key . "=" . $value;
            }
            $cookies = implode("; ", $cookieTmpArr);
            curl_setopt($this->curl, CURLOPT_COOKIE, $cookies);
        }

    }

    private function after_request() {

    }

    public function get($url) {
        $this->before_request($url);

        $this->after_request();
    }

    public function post($url) {
        $this->before_request($url);
        curl_setopt($this->curl, CURLOPT_POST, true);

        $this->after_request();
    }

    /**
     * @return int
     */
    public function getTimeout(): int {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout) {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): int {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     */
    public function setConnectTimeout(int $connectTimeout) {
        $this->connectTimeout = $connectTimeout;
    }

    /**
     * @return array
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getCookies(): array {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     */
    public function setCookies(array $cookies) {
        $this->cookies = $cookies;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent) {
        $this->userAgent = $userAgent;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode) {
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getError(): string {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error) {
        $this->error = $error;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode(int $errorCode) {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getProxy(): string {
        return $this->proxy;
    }

    /**
     * @param string $proxy
     */
    public function setProxy(string $proxy) {
        $this->proxy = $proxy;
    }

    /**
     * @return null
     */
    public function getCurl() {
        return $this->curl;
    }
}