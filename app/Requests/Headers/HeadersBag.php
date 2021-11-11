<?php

namespace App\Requests\Headers;

class HeadersBag
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->headers;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param string $key
     * @return string
     */
    public function get(string $key): string
    {
        return $this->headers[$key];
    }

    /**
     * @param string $key
     */
    public function remove(string $key): void
    {
        unset($this->headers[$key]);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->headers);
    }

    /**
     * Flush all headers after request
     */
    public function flush()
    {
        $this->headers = [];
    }

}
