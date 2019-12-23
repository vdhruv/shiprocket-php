<?php

namespace Shiprocket;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException as HttpClientException;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;

class Client
{
    use Resources\Users,
        Resources\Manifests,
        Resources\Products,
        Resources\Settings,
        Resources\Orders,
        Resources\Couriers,
        Resources\Shipments,
        Resources\Channels;

    public $token;
    public $email;
    public $password;
    public $version = 'v1';
    public $rate_limit = null;
    public $httpClient;

    /**
     * Creates a new client.
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $this->configure($config);
        $this->httpClient = new HttpClient;

//        if (empty($this->token)) {
        $this->setToken($this->getToken());
//        }
    }

    /**
     * Gets authorization header value.
     *
     * @return string
     */
    public function getAuthorizationHeader()
    {
        if ($this->token) {
            return 'Bearer ' . $this->token;
        }
    }

    /**
     * Gets HttpClient config for verb and parameters.
     *
     * @param string $verb
     * @param array $parameters
     *
     * @return   array
     */
    public function getConfigForVerbAndParameters($verb, $parameters = [])
    {
        $config = [
            'headers' => $this->getHeaders()
        ];

        if (!empty($parameters)) {
            if (strtolower($verb) == 'get') {
                $config['query'] = $parameters;
            } else {
                $config['json'] = $parameters;
            }
        }

        return $config;
    }

    /**
     * Gets headers for request.
     *
     * @return   array
     */
    public function getHeaders()
    {
        return [
            'Authorization' => trim($this->getAuthorizationHeader())
        ];
    }

    /**
     * Builds url from path.
     *
     * @param string $path
     * @return   string   Url
     */
    public function getUrlFromPath($path)
    {
        $path = ltrim($path, '/');

        $host = 'https://apiv2.shiprocket.in/';

        return $host . ($this->version ? $this->version : '') . '/external/' . $path;
    }

    /**
     * Handles http client exceptions.
     *
     * @param HttpClientException $e
     *
     * @return   void
     * @throws   Exception
     */
    public function handleRequestException(HttpClientException $e)
    {
        if ($response = $e->getResponse()) {
            $exception = new Exception($response->getMessage(), $response->getStatusCode(), $e);
//            $exception->setBody(json_decode($response->getBody()));

            throw $exception;
        }

        throw new Exception($e->getMessage(), 500, $e);
    }

    /**
     * Parses configuration.
     *
     * @param array $config
     * @return Client $config
     */
    public function configure($config = [])
    {
        $this->email = $config['email'];
        $this->password = $config['password'];

        return $this;
    }

    /**
     * Parses configuration.
     *
     * @return   array    $config
     */
    public function getConfiguration()
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    /**
     * Get authorization token
     *
     * @return string
     * @throws Exception
     */
    public function getToken()
    {
        return $this->request(
            'post',
            'auth/login',
            $this->getConfiguration()
        )['token'];
    }

    /**
     * Set authorization token
     *
     * @param string $value
     * @return Client
     */
    public function setToken($value)
    {
        $this->token = $value;

        return $this;
    }

    /**
     * Makes a request to the Shiprocket API and returns the response.
     *
     * @param string $verb The Http verb to use
     * @param string $path The path of the APi after the domain
     * @param array $parameters Parameters
     *
     * @return   stdClass             The JSON response from the request
     * @throws   Exception
     */
    public function request($verb, $path, $parameters = [])
    {
        $client = $this->httpClient;
        $url = $this->getUrlFromPath($path);
        $verb = strtolower($verb);
        $config = $this->getConfigForVerbAndParameters($verb, $parameters);

        try {
            $response = $client->$verb($url, $config);
            return json_decode($response->getBody(), 1);
        } catch (HttpClientException $e) {
            $this->handleRequestException($e);
        }
    }

    /**
     * Sets Http Client.
     *
     * @param HttpClient $client
     *
     * @return   Client
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->httpClient = $client;

        return $this;
    }
}
