<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack;

use Curl\Curl;
use GTrack\Utils\Utils;

/**
 * Request Class
 */
class Request
{
    /**
     * GTrack parent.
     *
     * @var GTrack
     */
    private $_parent;

    /**
     * Curl class
     *
     * @var Curl
     */
    private $_curl;

    /**
     * Endpoint URL for this request.
     *
     * @var string
     */
    private $_url;

    /**
     * An array of query param.
     *
     * @var array
     */
    private $_param;

    /**
     * An array of POST params.
     *
     * @var array
     */
    private $_post;

    /**
     * An array of headers.
     *
     * @var array
     */
    private $_headers;

    /**
     * An tmp array of headers.
     *
     * @var array
     */
    private $_tmpHeaders;

    /**
     * Use cookie file
     *
     * @var bool
     */
    private $_useCookie;

    public function __construct($parent, $url)
    {
        $this->_parent     = $parent;
        $this->_url        = $url;
        $this->_param      = [];
        $this->_post       = [];
        $this->_headers    = [];
        $this->_tmpHeaders = [];
        $this->_useCookie  = false;
    }

    /**
     * Add query parameter to this request
     *
     * @param array $param
     *
     * @return $this
     */
    public function addParam($param)
    {
        $this->_param = $param;

        return $this;
    }

    /**
     * Add post data to this request
     *
     * @param array $data
     *
     * @return $this
     */
    public function addPost($data)
    {
        $this->_post = $data;

        return $this;
    }

    /**
     * Add User-Agent, it will overwrite from addHeaders()
     *
     * @param string $value
     *
     * @return $this
     */
    public function addUserAgent($value)
    {
        $this->_tmpHeaders['user-agent'] = $value;

        return $this;
    }

    /**
     * Add authorization, it will overwrite from addHeaders()
     *
     * @param string $value
     *
     * @return $this
     */
    public function addAuthorization($value)
    {
        $this->_tmpHeaders['authorization'] = $value;

        return $this;
    }

    /**
     * Add headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function addHeaders($headers)
    {
        $this->_headers = $headers;

        return $this;
    }

    /**
     * Add header
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addHeader($key, $value)
    {
        $this->_tmpHeaders[$key] = $value;

        return $this;
    }

    /**
     * use request without cookie.txt
     *
     * @return $this
     */
    public function useCookie()
    {
        $this->_useCookie = true;

        return $this;
    }

    /**
     * Perform this request and get the response
     *
     * @return object
     */
    public function getResponse()
    {
        $request = $this->_getHttpRequest();

        return $request->getResponse();
    }

    /**
     * Perform this request and Map Object to "result()" method
     *
     * The Object class must have "result() and check()" method
     * and "$ekspedisi" property
     *
     * @param Response    $object
     * @param null|object $data   data tambahan apabila ada dua request
     *
     * @return object
     */
    public function mapResponse(Response $object, $data = null)
    {
        // Perform this request
        $this->_getHttpRequest($object);

        // Set ekspedisi info (name & site)
        $object->_setEkspedisi(Utils::ekspedisiInfo($object));

        // Check resi is available or not with "check()" method
        $refCheck = new \ReflectionMethod($object, 'check');
        if ($refCheck->invoke($object, $data)) {
            return $object->_resultError('Nomor resi tidak ditemukan.');
        }

        // Get response result from "result()" method
        $refResult = new \ReflectionMethod($object, 'result');

        return $refResult->invoke($object, $data);
    }

    /**
     * Perform this request and map to Response
     *
     * @param Response $object
     *
     * @return Curl
     */
    private function _getHttpRequest(Response $object = null)
    {
        $this->_curl = new Curl();

        // Set default user agent biar tidak mengulang penulisan
        if ($this->_parent->default_useragent) {
            $this->_curl->setUserAgent($this->_parent->default_useragent);
        } else {
            $this->_curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.89 Safari/537.36');
        }

        if ($this->_useCookie) {
            $this->_curl->setCookieJar('cookie.txt');
            $this->_curl->setCookieFile('cookie.txt');
        }

        $this->_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->_curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->_curl->setOpt(CURLOPT_TIMEOUT, 30);
        $this->_curl->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $this->_curl->setOpt(CURLOPT_MAXREDIRS, 30);
        $this->_curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->_curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);

        if ($this->_parent->proxy !== null) {
            $this->_curl->setProxy($this->_parent->proxy);
        }

        // Parse request and perform this request
        $this->_parseRequest();

        // Create and map Response object
        $this->_createResponse(is_null($object) ? new Response() : $object);

        return $this->_curl;
    }

    /**
     * Parse Curl Request
     */
    private function _parseRequest()
    {
        // Set final headers
        $this->_curl->setHeaders(
            array_merge($this->_headers, $this->_tmpHeaders)
        );

        $endpoint = $this->_url;

        // Set GET or POST method to this request
        if (!count($this->_post)) {
            $this->_curl->get($endpoint, $this->_param);
        } else {
            // build query if param is set
            if (!empty($this->_param)) {
                $endpoint = $endpoint . '?' . http_build_query($this->_param);
            }

            $this->_curl->post($endpoint, $this->_post);
        }
    }

    /**
     * Set object to response map
     *
     * @param Response $response
     */
    private function _createResponse(Response $response)
    {
        $response->setError($this->_curl->isError())
            ->setErrorMessage($this->_curl->getErrorMessage())
            ->setResponse($this->_curl->getResponse());
    }
}
