<?php

namespace loisium;


class Request implements IRequest {
    private $timeout = 3000;
    private $connectTimeout = 3000;
    private $headers = [];
    private $cookies = [];
    private $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0';
    private $statusCode = 0;
    private $error = '';
    private $errorCode = 0;
    private $proxy = '';
    private $url = '';
    private $parameters = [];
    private $lastResponse = null;
    private $curl = null;

    private function init() {
        /* 删除上一次的结果 */
        $this->lastResponse = null;
        /* 初始化CURL */
        $this->curl = curl_init();
        /* 设置由curl_exec返回结果 */
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        /* 设置输出Header */
        curl_setopt($this->curl, CURLOPT_HEADER, true);
    }

    private function beforeRequest() {
        $this->init();
        /* 设置UserAgent */
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->userAgent);
        /* 设置超时时间 */
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT_MS, $this->timeout);
        /* 设置连接地址 */
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
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

    private function afterRequest() {
        if ($this->lastResponse->getStatus() === 0) {
            $this->error = curl_error($this->curl);
            $this->errorCode = curl_errno($this->curl);
        }
        /* 关闭CURL资源 */
        curl_close($this->curl);
    }

    private function initParameter(string $url, array $parameters) {
        $this->setUrl($url, true);
        $this->setParameters($parameters, true);
    }

    public function get(string $url = '', array $parameters = []) {
        if ($parameters !== []) {
            $this->url = $this->url . (strpos($url,"?") === false ? '?' : '&') . http_build_query($parameters);
        }
        $this->initParameter($url, $parameters);
        $this->beforeRequest();
        $this->lastResponse = new Response($this->curl);
        $this->afterRequest();
        return $this->lastResponse;
    }

    public function post(string $url = '', array $parameters = []) {
        $this->initParameter($url, $parameters);
        $this->beforeRequest();
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);
        $this->lastResponse = new Response($this->curl);
        $this->afterRequest();
        return $this->lastResponse;
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
     * @param $key
     * @return string
     */
    public function getHeader($key): string {
        return $this->headers[$key];
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value) {
        $this->headers[$key] = $value;
    }

    /**
     * @return array
     */
    public function getCookies(): array {
        return $this->cookies;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getCookie(string $key): string {
        return $this->cookies[$key];
    }

    /**
     * @param array $cookies
     */
    public function setCookies(array $cookies) {
        $this->cookies = $cookies;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setCookie(string $key, string $value) {
        $this->cookies[$key] = $value;
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
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @param string $url
     * @param bool $checkEmpty
     */
    public function setUrl(string $url, bool $checkEmpty = false) {
        if ($checkEmpty && $url === '') {
            return ;
        }
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getParameters(): array {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @param bool $checkEmpty
     */
    public function setParameters(array $parameters, bool $checkEmpty = false) {
        if ($checkEmpty && $parameters === []) {
            return ;
        }
        $this->parameters = $parameters;
    }

    /**
     * @return IResponse
     */
    public function getLastResponse(): IResponse {
        return $this->lastResponse;
    }

}