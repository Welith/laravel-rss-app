<?php

namespace App\RequestManagers;

use App\Exceptions\GeneralException;
use App\Helper\URLParser;
use App\Requests\AbstractApiHttpRequest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthRequestManagerApi extends AbstractApiHttpRequest
{
    public const SERVICE = "GOLANG_SERVICE";

    /**
     * ApiHttpRequestService constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);

        $this->_headers->add('Content-Type', 'application/json');
    }

    /**
     * @param array $data
     * @return mixed
     * @throws GeneralException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function login(array $data): mixed
    {
        return $this->post('/login', ['username' => $data['username'], 'password' => $data['password']]);
    }

    /**
     * @param string $token
     * @return mixed
     * @throws GeneralException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function logout(string $token): mixed
    {
        $this->_headers->add('Authorization', "Bearer " . $token);
        return $this->post('/logout');
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @param $data
     * @param bool $json
     * @param array $options
     * @return PromiseInterface|mixed|HttpException|void|null
     * @throws GuzzleException
     * @throws \JsonException
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

        try {

            $response = $this->client->request($method, URLParser::parse($uri, static::SERVICE, $params), $options);
        } catch (\Exception $exception) {

            if ($exception->getCode() === 400) {

                return null;
            }

            return null;
        }

        $this->_headers->flush();

        return $this->handle($response);
    }

    /**
     * @param ResponseInterface $response
     * @return mixed|HttpException|void|null
     * @throws \JsonException
     */
    protected function handle(ResponseInterface $response)
    {
        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($content['access_token'])) {

            return $content['access_token'];
        }

        return null;
    }
}
