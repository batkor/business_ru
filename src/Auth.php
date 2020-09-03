<?php

namespace Business;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Exception\RequestException;
use Psr\Http\Client\ClientInterface;

/**
 * Provides authorization mechanical for BusinessApi.
 */
class Auth
{
    /**
     * The path for repair token.
     */
    public const REPAIR_PATH = '/api/rest/repair.json';

    /**
     * The API key.
     *
     * @var string
     */
    private $appId;

    /**
     * The secret key.
     *
     * @var string
     */
    private $secret;

    /**
     * The app_psw key.
     *
     * @var string
     */
    private $appPsw;

    /**
     * The host.
     *
     * Your account domain https://myaccount.business.ru.
     *
     * @var string
     */
    private $host;

    /**
     * The token key.
     *
     * @var string
     */
    private $token;

    /**
     * The http client.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client, $appId, $secret, $host, $token = null)
    {
        $this->client = $client;
        $this->appId = $appId;
        $this->secret = $secret;
        $this->host = rtrim($host, '/');

        if ($token) {
            $this->token = $token;
        }
    }

    /**
     * Repair api token.
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     *
     * @see https://developers.business.ru/api-polnoe/poterya_tokena_i_ego_vosstanovlenie/366
     */
    public function repair()
    {
        $request = new Request('GET', $this->uri(), ['Content-Type' => 'application/json']);
        $response = $this->client->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);
        $app_psw = $result['app_psw'];
        unset($result['app_psw']);
        if (md5($this->secret . json_encode($result)) !== $app_psw) {
            throw new RequestException('Authorization error', $request);
        }
        $this->token = $result['token'];
    }

    public function secret()
    {
        return $this->secret;
    }

    public function appId()
    {
        return $this->appId;
    }

    /**
     * Returns token.
     *
     * @return string
     *   The token.
     */
    public function token()
    {
        if ($this->token) {
            return $this->token;
        }
        $this->repair();

        return $this->token;
    }

    /**
     * Returns host.
     *
     * @return string
     *   The host domain.
     */
    public function host()
    {
        return $this->host;
    }

    public function checkAppPws(array $result)
    {

    }

    /**
     * Returns uri for get token request.
     *
     * @return \GuzzleHttp\Psr7\Uri
     *   The uri object.
     */
    private function uri(): Uri
    {
        $uri = new Uri($this->host . self::REPAIR_PATH);
        $params = [
            'app_id' => $this->appId,
            'app_psw' => $this->appPsw(),
        ];

        return $uri->withQuery(http_build_query($params));
    }

    /**
     * Returns app_psw value.
     *
     * @return string
     *   The app_pws value.
     */
    private function appPsw()
    {
        if ($this->appPsw) {
            return $this->appPsw;
        }
        $this->appPsw = md5(
            $this->secret . http_build_query(
                [
                    'app_id' => $this->appId,
                ]
            )
        );

        return $this->appPsw;
    }

}
