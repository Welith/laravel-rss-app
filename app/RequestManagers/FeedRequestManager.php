<?php

namespace App\RequestManagers;

use App\Exceptions\GeneralException;
use App\Requests\AbstractHttpRequest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use HttpException;
use Psr\Http\Message\ResponseInterface;

class FeedRequestManager extends AbstractHttpRequest
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
     * @param array $urls
     * @return mixed
     * @throws GeneralException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getFeeds(array $urls): mixed
    {
        return $this->post('/v1/feeds', $urls);
    }


    /**
     * @param ResponseInterface $response
     * @return mixed
     * @throws HttpException
     * @throws \JsonException
     */
    protected function handle(ResponseInterface $response): mixed
    {
        $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($content['items'])) {

            return $content['items'];

        }

        throw new HttpException(500);
    }
}
