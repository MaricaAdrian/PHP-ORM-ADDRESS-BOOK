<?php

declare(strict_types=1);
namespace Service;

class UrlService {
    private string $requestMethod;
    private string $uri;
    private array $segments;

    public function __construct() {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $this->segments = explode('/', $this->uri);
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string[]
     */
    public function getSegments(): array
    {
        return $this->segments;
    }


}