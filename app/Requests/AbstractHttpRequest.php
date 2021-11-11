<?php

namespace App\Requests;

use App\Constants\RequestConstants;
use App\Exceptions\GeneralException;
use App\Requests\Headers\HeadersBag;
use GuzzleHttp\Promise\PromiseInterface;
use JsonException;
use App\Helper\URLParser;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AbstractHttpRequest
 * @package Paynetics\HttpRequest
 *
 * @property HeadersBag headers
 */
abstract class AbstractHttpRequest implements HttpRequestInterface
{

    /**
     *  .env file variable with url to destination
     */
    protected const SERVICE = '';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var HeadersBag
     */
    protected $_headers;

    /**
     * RequestService constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {

        $this->client = $client;
        $this->_headers = new HeadersBag();
    }

    /**
     * @param string $uri
     * @param array $params
     * @param array $query
     * @param array $options
     *
     * @return mixed
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function get(string $uri, array $params = [], array $query = [], array &$options = []): mixed
    {
        $options[RequestOptions::QUERY] = $query;

        return $this->request(RequestConstants::GET, $uri, $params, null, true);
    }

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param bool $json
     * @param array $options
     *
     * @return mixed
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function post(string $uri, $data = null, array $params = [], bool $json = true, array $options = []): mixed
    {
        return $this->request(RequestConstants::POST, $uri, $params, $data, $json, $options);
    }

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param bool $json
     * @param array $options
     *
     * @return mixed
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function put(string $uri, $data = null, array $params = [], bool $json = true, array $options = []): mixed
    {
        return $this->request(RequestConstants::PUT, $uri, $params, $data, $json, $options);
    }

    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param bool $json
     * @param array $options
     *
     * @return mixed
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function patch(string $uri, $data = null, array $params = [], bool $json = true, array $options = []): mixed
    {
        return $this->request(RequestConstants::PATCH, $uri, $params, $data, $json, $options);
    }


    /**
     * @param string $uri
     * @param null $data
     * @param array $params
     * @param array $options
     *
     * @return mixed
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function delete(string $uri, $data = null, array $params = [], array $options = []): mixed
    {
        return $this->request(RequestConstants::DELETE, $uri, $params, $data, true);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @param $data
     * @param bool $json
     * @param array $options
     *
     * @param bool $async
     * @return PromiseInterface|HttpException|void
     *
     * @throws GeneralException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function request(string $method, string $uri, array $params, $data, bool $json = true, array $options = [])
    {
        if ($json) {

            if ($data && !is_string($data)) {
                $data = json_encode($data);
            }

            if (is_array($data)) {
                $data = json_encode($data, JSON_THROW_ON_ERROR, 512);
            }

            $data = [RequestOptions::BODY => $data];
        } else {
            $data = [RequestOptions::FORM_PARAMS => $data];
        }

        $this->preRequest($options);

        $options = array_merge($data, [RequestOptions::HEADERS => $this->_headers->all()], $options);

        $response = $this->client->request($method, URLParser::parse($uri, static::SERVICE, $params), $options);

        $this->_headers->flush();

        return $this->handle($response);


    }

    /**
     * Pre Request Hook
     * @param array $options
     */
    public function preRequest(array &$options): void
    {

    }

    /**
     * @param ResponseInterface $response
     *
     * @return HttpException|void
     *
     * @throws GeneralException
     */
    abstract protected function handle(ResponseInterface $response);
}
