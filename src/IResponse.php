<?php

namespace loisium;


interface IResponse {
    public function getUrl(): string;
    public function getBody(): string;
    public function getContentType(): string;
    public function getStatus(): int;
    public function getRawHeader(): string;
    public function getHeaders(): array;
    public function getHeader(string $key): string;
}