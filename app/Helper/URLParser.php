<?php

namespace App\Helper;

use App\Exceptions\GeneralException;

abstract class URLParser
{
    /**
     * @param string $uri
     * @param string $service
     * @param array $params
     *
     * @return string
     *
     * @throws GeneralException
     */
    public static function parse(string $uri, string $service, array $params = []): string
    {
        $keys = array_keys($params);
        $values = array_values($params);

        foreach ($keys as $i => $key) {
            $keys[$i] = '{' . $key . '}';
        }

        $isCompleteUrl = preg_match('/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $uri);

        if (!$isCompleteUrl && !isset($_ENV[$service])) {
            throw new GeneralException("Incorrect service URL");
        }

        return preg_replace('/({.+?})/', '', $_ENV[$service] . str_replace($keys, $values, $uri));
    }
}
