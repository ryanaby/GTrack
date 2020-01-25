<?php
/**
 * Global Tesla - globaltesla.com
 *
 * @author     Global Tesla <dev@globaltesla.com>
 * @copyright  2019 Global Tesla
 */

namespace GTrack;

use \Curl\Curl;

/**
 * Hanya untuk request
 */
class CurlRequest
{
    private $curl;
    private $_post;
    private $_get;
    private $_headers;
    private $_cookies;
    public $response;
    private $_basicAuth;

    public function __construct()
    {
        $this->_post        = '';
        $this->_get         = '';
        $this->_headers     = [];
        $this->_basicAuth   = '';
    }

    /**
     * Curl Request
     */
    public function request()
    {
        $curl = new Curl();
        $curl->setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
        $curl->setCookieJar('cookie.txt');
        $curl->setCookieFile('cookie.txt');
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_TIMEOUT, 30);
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curl->setOpt(CURLOPT_MAXREDIRS, 30);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 2);

        $this->curl = $curl;

        if (!is_null(GTrack::$proxy)) {
            $this->curl->setProxy(GTrack::$proxy);
        }

        return $this;
    }

    /**
     * Post data
     *
     * @param string $url  urlnya
     * @param array  $data postdata
     */
    public function post($url, $data)
    {
        $this->_post    = $this->curl->post($url, $data);
        $this->response = $this->curl->response;

        return $this;
    }

    /**
     * Get data
     *
     * @param string $url  urlnya
     * @param array  $data datanya
     */
    public function get($url, $data = [])
    {
        $this->_get     = $this->curl->get($url, $data);
        $this->response = $this->curl->response;

        return $this;
    }

    /**
     * Set header
     *
     * @param array $arr header array
     */
    public function setHeaders($arr)
    {
        $this->_headers = $this->curl->setHeaders($arr);

        return $this;
    }

    /**
     * Set cookie
     *
     * @param array $arr cookie array
     */
    public function setCookies($arr)
    {
        $this->_cookies = $this->curl->setCookies($arr);

        return $this;
    }

    /**
     * Get response
     */
    public function getResponse()
    {
        return $this->curl->response;
    }

    /**
     * Set basic Authentication
     *
     * @param string $username username / value
     * @param string $password password / key
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->_basicAuth = $this->curl->setBasicAuthentication($username, $password);

        return $this;
    }
}
