<?php
/**
 * Global Tesla - globaltesla.com
 *
 * @author     Global Tesla <dev@globaltesla.com>
 * @copyright  2019 Global Tesla
 */

namespace GTrack;

/**
 * GTrack - Resi Tracker API
 *
 * CATATAN FENTING:
 * - GTrack ini tidak berafiliasi, diizinkan, dikelola ataupun di sponsori
 *   oleh pihak ekspedisi ataupun anak perusahaannya. GTrack murni dibikin
 *   sendiri dan statusnya tidak resmi. Resiko ditanggung pengguna.
 * - GTrack dibuat tidak untuk kejahatan, spamming, ataupun kegiatan yang merugikan orang lain.
 * - Pada intinya GTrack dibuat untuk mempermudah kita untuk melakukan melacak resi ya man teman.
 *
 * @author walangkaji (https://github.com/walangkaji)
 */
class GTrack
{
    public static $proxy;

    public function __construct($proxy = null)
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
     * @param string $resi Nomor resi yang mau di cek
     *
     * @return object
     */
    public function jne($resi)
    {
        $request = (new CurlRequest)
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

        return Response\JneResponse::result($request);
    }

    /**
     * J&T
     *
     * @param string $resi nomor resi yang mau di cek
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

        $request = (new CurlRequest)
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
            ])
            ->getResponse();

        return Response\JntResponse::result($request);
    }

    /**
     * TIKI
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return object
     */
    public function tiki($resi)
    {
        $curl = (new CurlRequest)
            ->request()
            ->setHeaders([
                'User-Agent'    => Constants::DALVIK_UA,
                'Host'          => Constants::TIKI_HOST,
                'Authorization' => '0437fb74-91bd-11e9-a74c-06f2c0b7c6f0-91bf-11e9-a74c-06f2c4b0b602',
            ]);

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

            if ($i > $try) {
                return $info->msg;
            }
        } while ($i <= $try && !$valid);

        $result->info = $info->response[0];

        $curl->post(Constants::TIKI_HISTORY, ['cnno' => $resi]);
        $history = $curl->response;

        if (!isset($history->errcode)) {
            if (!is_null($history->response)) {
                $result->history = $history->response[0]->history;
            } else {
                $result->history = $history->response;
            }
        }

        return Response\TikiResponse::result($result);
    }

    /**
     * POS INDONESIA
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return object
     */
    public function pos($resi)
    {
        $request  = (new CurlRequest)
            ->request()
            ->get(sprintf(Constants::POS_GET, $resi))
            ->getResponse();

        $token   = GlobalFunction::GetBetween($request, 'csrf-token" content="', '">');

        $request = (new CurlRequest)
            ->request()
            ->post(Constants::POS, [
                'resi'   => $resi,
                '_token' => $token,
            ])
            ->getResponse();

        @unlink('cookie.txt');

        return Response\PosResponse::result(json_decode($request));
    }

    /**
     * WAHANA
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return object
     */
    public function wahana($resi)
    {
        $request = (new CurlRequest)
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

        return Response\WahanaResponse::result($request);
    }

    /**
     * SICEPAT
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return object
     */
    public function siCepat($resi)
    {
        $request = (new CurlRequest)
            ->request()
            ->setHeaders([
                'Host'    => Constants::SICEPAT_HOST,
                'api-key' => '96625fdc2bfe59fa05dcf7c9c71755dd',
            ])
            ->get(Constants::SICEPAT, ['waybill' => $resi])
            ->getResponse()->sicepat;

        return Response\SiCepatResponse::result($request);
    }

    /**
     * NINJAXPRESS
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return object
     */
    public function ninjaXpress($resi)
    {
        $request = (new CurlRequest)
            ->request()
            ->setHeaders(['User-Agent' => 'okhttp/3.4.1'])
            ->get(Constants::NINJAXPRESS, ['id' => $resi])
            ->getResponse();

        return Response\NinjaXpressResponse::result($request);
    }

    /**
     * JET EXPRESS
     *
     * @param string $resi nomor resi yang mau di cek
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
            ->getResponse();

        $history = [];

        if (!empty($data)) {
            $history = (new CurlRequest)
                ->request()
                ->setHeaders($query)
                ->get(sprintf(Constants::JET_HISTORY, $resi, $data[0]->connotes[0]->connoteCode))
                ->getResponse();
        }

        $result          = new \stdClass();
        $result->data    = $data[0];
        $result->history = $history;

        return Response\JetExpressResponse::result($result);
    }
}
