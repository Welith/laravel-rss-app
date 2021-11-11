<?php

namespace App\Requests;

use App\Exceptions\GeneralException;
use Psr\Http\Message\ResponseInterface;

interface HttpRequestInterface
{
    /**
     * @param string $uri
     * @param array $params
     * @param array $options
     *
     * @return mixed|ResponseInterface
     *
     * @throws GeneralException
     *
     */
    public function get(string $uri, array $params = [], array $options = []);

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param bool $json
     * @param array $options
     *
     * @return mixed|ResponseInterface
     *
     */
    public function post(string $uri, $data = null, array $params = [], bool $json = true, array $options = []);

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param bool $json
     * @param array $options
     *
     * @return mixed|ResponseInterface
     *
     */
    public function put(string $uri, $data = null, array $params = [], bool $json = true, array $options = []);

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param array $options
     *
     * @return mixed|ResponseInterface
     *
     * @throws GeneralException
     *
     */
    public function delete(string $uri, $data = null, array $params = [], array $options = []);

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @param $data
     * @param bool $json
     * @param array $options
     *
     * @return mixed|ResponseInterface
     *
     */
    public function request(string $method, string $uri, array $params, $data,bool $json = true, array $options = []);
}
