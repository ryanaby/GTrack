<?php
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
    public  $response;
    private $_basicAuth;

    function __construct()
    {
        $this->_post        = '';
        $this->_get         = '';
        $this->_headers     = [];
        $this->_basicAuth   = '';
    }

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

    public function post($url, $data)
    {
        $this->_post    = $this->curl->post($url, $data);
        $this->response = $this->curl->response;

        return $this;
    }

    public function get($url, $data = array())
    {
        $this->_get     = $this->curl->get($url, $data);
        $this->response = $this->curl->response;

        return $this;
    }

    public function setHeaders($arr)
    {
        $this->_headers = $this->curl->setHeaders($arr);

        return $this;
    }

    public function getResponse()
    {
        return $this->curl->response;
    }

    public function setBasicAuthentication($username, $password)
    {
        $this->_basicAuth = $this->curl->setBasicAuthentication($username, $password);

        return $this;
    }


}