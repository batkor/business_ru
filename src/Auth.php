<?php

namespace Business;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Http\Client\HttpClient;

/**
 * Provides authorization mechanical for BusinessApi.
 */
class Auth
{
    const REPAIR_PATH = '/api/rest/repair.json';

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
     * @var string
     */
    private $host;

  /**
   * The token key.
   *
   * @var string
   */
    private $token;

    public function __construct($appId, $secret, $host)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->host = rtrim($host, '/');
        $this->generateAppPsw();
    }

    public function repair(HttpClient $client)
    {
        $request = new Request('GET', $this->uri(), ['Content-Type' => 'application/json']);
        $response = $client->sendRequest($request);
        $result = json_decode($response->getBody()->getContents(), TRUE);
        $app_psw = $result[ 'app_psw' ];
        unset( $result[ 'app_psw' ] );
        if (md5($this->secret . json_encode($result)) !== $app_psw) {
          throw new BadResponseException('Authorization error', $request);
        }
        $this->token = $result['token'];
    }

    public function token() {
      return $this->token;
    }

    private function uri(): Uri {
        $uri = new Uri($this->host . self::REPAIR_PATH);
        $params = [
          'app_id' => $this->appId,
        ];
        $params['app_psw'] = md5($this->secret . http_build_query($params));
        return $uri->withQuery(http_build_query($params));
    }

    private function generateAppPsw() {
        $this->appPsw = md5($this->secret . http_build_query([
            'app_id' => $this->appId,
          ])
        );
    }

}
