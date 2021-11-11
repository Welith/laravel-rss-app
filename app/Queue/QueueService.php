<?php

namespace App\Queue;

use GuzzleHttp\Psr7\Exception\MalformedUriException;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueService
{
    /**
     * @param $message
     * @param string $queue
     * @param string|null $exchange
     * @param array|null $options
     * @throws \JsonException
     */
    public function dispatch($message, string $queue, ?string $exchange, ?array $options = [])
    {
        $transportDsn = $this->parseUrl(getenv("RABBITMQ_URL"));

        $host = $transportDsn['host'];
        $pass = $transportDsn['password'];
        $user = $transportDsn['login'];
        $port = $transportDsn['port'];
        $vhost = $transportDsn['vhost'] ?? "/";

        $options['content_type'] = 'application/json';
        $options['delivery_mode'] = 2;

        $message = new AMQPMessage(json_encode($message, JSON_THROW_ON_ERROR), $options);

        if (getenv("APP_ENV") === 'prod') {

            $sslOptions = [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ];

            $conn = new AMQPSSLConnection($host, $port, $user, $pass, $vhost,  $sslOptions);

        } else {

            $conn = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
        }

        $ch = $conn->channel();

        $ch->queue_declare($queue, false, true, false, false);
        $ch->exchange_declare($exchange, 'fanout', false, true, false);
        $ch->queue_bind($queue, $exchange);

        $ch->basic_publish($message, $queue);
    }

    /**
     * @param string $amqpUrl
     * @return array
     */
    protected function parseUrl(string $amqpUrl): array
    {
        $parameters = [];
        $url = parse_url($amqpUrl);

        if ($url === false || !isset($url['scheme']) || !in_array($url['scheme'], ['amqp', 'amqps'], true)) {
            throw new MalformedUriException('Malformed parameter "url".');
        }

        if (isset($url['host'])) {
            $parameters['host'] = urldecode($url['host']);
        }
        if (isset($url['port'])) {
            $parameters['port'] = (int)$url['port'];
        }
        if (isset($url['user'])) {
            $parameters['login'] = urldecode($url['user']);
        }
        if (isset($url['pass'])) {
            $parameters['password'] = urldecode($url['pass']);
        }
        if (isset($url['path'])) {
            $parameters['vhost'] = urldecode(ltrim($url['path'], '/')) === "" ? "/" : urldecode(ltrim($url['path'], '/'));
        }

        if (isset($url['query'])) {

            $query = array();
            parse_str($url['query'], $query);
            $parameters = array_merge($parameters, $query);
        }

        return $parameters;
    }
}
