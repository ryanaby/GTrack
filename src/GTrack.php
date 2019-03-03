<?php
namespace GTrack;

use \Curl\Curl;
use \GTrack\CurlRequest;
use \GTrack\Constants;
use \GTrack\GlobalFunction;

/**
 * GTrack
 */
class GTrack
{

    public static $proxy;

    function __construct($proxy = null)
    {
        $this->_post      = '';
        $this->_get       = '';
        $this->_headers   = [];
        $this->_basicAuth = '';
        self::$proxy      = $proxy;
    }

    /**
     * JNE
     * 
     * @param  string $resi Nomor resi yang mau di cek
     * 
     * @return object
     */
    public function jne($resi)
    {
        return (new CurlRequest)
            ->request()
            ->setHeaders([
                'Host'       => Constants::JNE_HOST,
                'User-Agent' => 'Java-Request',
            ])
            ->post(sprintf(Constants::JNE, $resi), [
                'username'   => 'JNEONE',
                'api_key'    => '504fbae0d815bf3e73a7416be328fcf2',
            ])
            ->getResponse();
    }

    /**
     * J&T
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function jnt($resi)
    {
        $query = [
            'parameter'     => (Object) [
                'billCodes' => $resi,
                'lang'      => 'id',
            ],
        ];

        $curl = (new CurlRequest)
            ->request()
            ->setHeaders([
                'X-Requested-With' => 'XMLHttpRequest',
                'Host'             => Constants::JNT_HOST,
                'User-Agent'       => 'okhttp/3.8.0',
            ])
            ->post(Constants::JNT, [
                'method'           => 'order.massOrderTrack',
                'data'             => json_encode($query),
                'format'           => 'json',
                'v'                => '1.0',
            ]);

        return GlobalFunction::formatJntResponse($curl->response);
    }

    /**
     * TIKI
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function tiki($resi)
    {
        $curl = (new CurlRequest)
            ->request()
            ->setHeaders([
                'User-Agent' => Constants::DALVIK_UA,
                'Host'       => Constants::TIKI_HOST,
            ])
            ->setBasicAuthentication('6871471541890123049', '842ab488840fbab11e2a2cd90a9a14d030fedef8');

        $try    = 3;
        $i      = 0;
        $result = new \stdClass();

        do {
            $valid  = true;

            $curl->post(Constants::TIKI_INFO, ['cnno' => $resi]);

            $info   = $curl->response;

            if (isset($info->errcode)) {
                $valid = false;
            }

            $i++;

            if ($i > $try) return $info->msg;

        } while ($i <= $try && !$valid);

        $result->info = $info->response[0];

        $curl->post(Constants::TIKI_HISTORY, ['cnno' => $resi]);
        $history = $curl->response;

        if (!isset($history->errcode)) {
            if (!is_null($history->response)) {
                $result->history = $history->response[0]->history;
            }else{
                $result->history = $history->response;
            }
        }

        return $result;
    }

    /**
     * POS INDONESIA
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function pos($resi)
    {
        return (new CurlRequest)
            ->request()
            ->setHeaders([
                'User-Agent' => Constants::DALVIK_UA,
                'Host'       => Constants::POS_HOST,
            ])
            ->post(Constants::POS, [
                'ky'   => '',
                'ai'   => GlobalFunction::randomStr(16, false, true),
                'eks'  => 'POS',
                'resi' => $resi,
                'nq'   => '',
                'nw'   => date('Y/m/d H:i:s', strtotime('now')),
                'vr'   => '7.0.9',
            ])
            ->getResponse();
    }

    /**
     * WAHANA
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function wahana($resi)
    {
        return (new CurlRequest)
            ->request()
            ->setHeaders([
                'User-Agent'   => 'Apache-HttpClient/UNAVAILABLE (java 1.4)',
                'Host'         => Constants::WAHANA_HOST,
            ])
            ->get(Constants::WAHANA, [
                'access_token' => '093a64444fa19f591682f7087a5e5a08febd9e43',
                'ttk'          => $resi,
            ])
            ->getResponse();
    }

    /**
     * SICEPAT
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function siCepat($resi)
    {
        return (new CurlRequest)
            ->request()
            ->setHeaders([
                'Host'    => Constants::SICEPAT_HOST,
                'api-key' => '96625fdc2bfe59fa05dcf7c9c71755dd',
            ])
            ->get(Constants::SICEPAT, ['waybill' => $resi])
            ->getResponse();
    }

    /**
     * NINJAXPRESS
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function ninjaXpress($resi)
    {
        return (new CurlRequest)
            ->request()
            ->setHeaders(['User-Agent' => 'okhttp/3.4.1'])
            ->get(Constants::NINJAXPRESS, ['id' => $resi])
            ->getResponse();
    }

    /**
     * JET EXPRESS
     * 
     * @param  string $resi Nomor resi yang mau di cek.
     * 
     * @return object
     */
    public function jetExpress($resi)
    {
        $query = ['User-Agent' => 'okhttp/3.4.1'];
        $data  = (new CurlRequest)
            ->request()
            ->setHeaders($query)
            ->get(Constants::JETEXPRESS, ['awbNumbers' => $resi])
            ->getResponse()[0];

        $history = (new CurlRequest)
            ->request()
            ->setHeaders($query)
            ->get(sprintf(Constants::JET_HISTORY, $resi, $data->connotes[0]->connoteCode))
            ->getResponse();

        $result          = new \stdClass();
        $result->data    = $data;
        $result->history = $history;

        return $result;
    }

}