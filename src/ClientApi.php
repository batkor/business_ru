<?php

namespace Business;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Exception\RequestException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Client\ClientInterface;

class ClientApi implements ClientApiInterface
{
    protected $path;

    /**
     * The app_psw key.
     *
     * @var string
     */
    protected $appPsw;

    /**
     * The authentication credentials.
     *
     * @var \Business\Auth
     */
    protected $auth;

    /**
     * The http client.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    protected $client;

    public function __construct(Auth $auth, ClientInterface $client)
    {
        $this->auth = $auth;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function request($method, array $params, $action = null)
    {
        $body = '';
        if ($action) {
            $uri = $this->auth->host() . sprintf(ClientApiInterface::PATH_MASK, $action);
        }
        else {
            $uri = $this->auth->host() . $this->path();
        }
        if ($method == 'get') {
            $uri = $this
                ->addQueryParams(new Uri($uri), $params);
        }
        else {
            $body = json_encode($this->buildParams($params));
        }

        $request = new Request($method, $uri, ['Content-Type' => 'application/json'], $body);
        $response = $this->client->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), true);
        dump($result);
        $app_psw = $result['app_psw'];
        unset($result['app_psw']);
        if (md5($this->auth->token() . $this->auth->secret() . json_encode($result)) !== $app_psw) {
            throw new RequestException('Request error', $request);
        }

        return $result;
    }

    /**
     * Attach query params and returns uri.
     *
     * @param \Psr\Http\Message\UriInterface $uri
     *   The source uri.
     * @param array $params
     *   The query params.
     *
     * @return \Psr\Http\Message\UriInterface
     *   The uri object.
     */
    protected function addQueryParams(UriInterface $uri, array $params = []): UriInterface
    {
        return $uri->withQuery(http_build_query($this->buildParams($params)));
    }

    public function buildParams($params)
    {

        $params = $params + ['app_id' => $this->auth->appId()];
        ksort($params);
        $params['app_psw'] = $this->appPsw($params);
        return $params;
    }

    /**
     * Returns app_psw value.
     *
     * @return string
     *   The app_pws value.
     */
    protected function appPsw($params)
    {
        if ($this->appPsw) {
            return $this->appPsw;
        }
        $source = $this->auth->token() . $this->auth->secret() . http_build_query($params);
        $this->appPsw = md5($source);

        return $this->appPsw;
    }

    /**
     * {@inheritDoc}
     */
    public function path(){}

}