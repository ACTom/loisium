<?php

namespace loisium;


class Response implements IResponse {
    private $url = '';
    private $body = '';
    private $contentType = '';
    private $status = 0;
    private $rawHeader = '';
    private $headers = [];

    public function __construct($curl) {
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $this->url = $info['url'];
        if ($response !== false) {
            $headerSize = $info['header_size'];
            $this->rawHeader = substr($response, 0, $headerSize);
            $this->body = substr($response, $headerSize);
            $this->contentType = $info['content_type'];
            $this->status = $info['http_code'];

            $headerTmp = explode("\r\n", $this->rawHeader);
            foreach ($headerTmp as $item) {
                $itemArray = explode(':', $item, 2);
                if (count($itemArray) === 2) {
                    $this->headers[$itemArray[0]] = ltrim($itemArray[1]);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getUrl(): string {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getContentType(): string {
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRawHeader(): string {
        return $this->rawHeader;
    }

    /**
     * @return array
     */
    public function getHeaders(): array {
        return $this->headers;
    }

    public function getHeader(string $key): string {
        return $this->headers[$key];
    }

}