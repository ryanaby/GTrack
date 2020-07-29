<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack;

use GTrack\Utils\Utils;

class Response
{
    /** @var int */
    private $error;

    /** @var string */
    private $errorMessage;

    /** @var object */
    protected $response;

    /** @var array */
    protected $_ekspedisi = [];

    /**
     * Is error
     *
     * @return bool
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * Set if error
     *
     * @param bool $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Set error message
     *
     * @param string $message
     *
     * @return $this
     */
    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;

        return $this;
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param mixed $response
     *
     * @return $this
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Build response object data
     *
     * @param array $data
     *
     * @return object
     */
    protected function build($data)
    {
        return Utils::decode(
            array_merge(
                $this->_ekspedisi,
                Utils::errMsg(false, 'success'),
                $data
            )
        );
    }

    /**
     * Set error result
     *
     * @param string $messageStatus
     *
     * @return object
     */
    public function _resultError($messageStatus)
    {
        return Utils::decode(
            array_merge(
                $this->_ekspedisi,
                Utils::errMsg(true, $messageStatus)
            )
        );
    }

    /**
     * Set ekspedisi info for header result
     *
     * @param array $data
     */
    public function _setEkspedisi($data)
    {
        $this->_ekspedisi = $data;
    }
}
