<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack;

use GTrack\Utils\Utils;

/**
 * GTrack - PHP Resi Tracker API
 *
 * CATATAN FENTING:
 * - GTrack ini tidak berafiliasi, dikelola ataupun di sponsori oleh
 *   pihak ekspedisi ataupun anak perusahaannya. GTrack murni dibikin
 *   sendiri dan statusnya tidak resmi. Resiko ditanggung pengguna.
 * - GTrack dibuat tidak untuk kejahatan, spamming, ataupun kegiatan yang merugikan orang lain.
 * - Pada intinya GTrack dibuat untuk mempermudah kita untuk melakukan melacak resi ya man-teman.
 */
class GTrack
{
    /** @var string */
    public $proxy;

    /** @var string */
    public $default_useragent = 'okhttp/3.12.1';

    public function __construct($proxy = null)
    {
        $this->proxy = $proxy;
    }

    /**
     * JNE
     *
     * @param string $resi Nomor resi yang mau di cek
     *
     * @return Response\JneResponse
     */
    public function jne($resi)
    {
        return $this->request(sprintf(Constants::JNE, $resi))
            ->addPost([
                'username' => 'JNEONE',
                'api_key'  => Constants::JNE_KEY,
            ])
            ->mapResponse(new Response\JneResponse());
    }

    /**
     * J&T
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\JntResponse
     */
    public function jnt($resi)
    {
        return $this->request(Constants::JNT)
            ->addPost([
                'method' => 'order.massOrderTrack',
                'format' => 'json',
                'v'      => '1.0',
                'data'   => json_encode([
                    'parameter'     => (Object) [
                        'billCodes' => $resi,
                        'lang'      => 'en',
                    ]
                ]),
            ])
            ->mapResponse(new Response\JntResponse());
    }

    /**
     * TIKI
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\TikiResponse
     */
    public function tiki($resi)
    {
        return $this->request(Constants::TIKI)
            ->addAuthorization(Constants::TIKI_AUTH)
            ->addUserAgent(Constants::DALVIK_UA)
            ->addPost(['cnno' => $resi])
            ->mapResponse(new Response\TikiResponse());
    }

    /**
     * POS INDONESIA
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\PosResponse
     */
    public function pos($resi)
    {
        return $this->request(Constants::POS)
            ->addHeader('content-type', 'application/json')
            ->addPost(['barcode' => $resi])
            ->mapResponse(new Response\PosResponse());
    }

    /**
     * WAHANA
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\WahanaResponse
     */
    public function wahana($resi)
    {
        return $this->request(Constants::WAHANA)
            ->addParam([
                'access_token' => Constants::WAHANA_AUTH,
                'ttk'          => $resi,
            ])
            ->mapResponse(new Response\WahanaResponse());
    }

    /**
     * SICEPAT
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\SiCepatResponse
     */
    public function siCepat($resi)
    {
        return $this->request(sprintf(Constants::SICEPAT, $resi))
            ->addHeader('api-key', Constants::SICEPAT_KEY)
            ->mapResponse(new Response\SiCepatResponse());
    }

    /**
     * NINJAXPRESS
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\NinjaXpressResponse
     */
    public function ninjaXpress($resi)
    {
        return $this->request(sprintf(Constants::NINJAXPRESS, $resi))
            ->mapResponse(new Response\NinjaXpressResponse());
    }

    /**
     * JET EXPRESS
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\JetExpressResponse
     */
    public function jetExpress($resi)
    {
        $info = $this->request(sprintf(Constants::JETEXPRESS, $resi))
            ->getResponse();

        return $this->request(sprintf(Constants::JET_HISTORY, $resi, $info[0]->connotes[0]->connoteCode))
            ->mapResponse(new Response\JetExpressResponse(), $info[0]);
    }

    /**
     * LION PARCEL
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\LionParcelResponse
     */
    public function lionParcel($resi)
    {
        return $this->request(sprintf(Constants::LION_PARCEL, Utils::formatLionResi($resi)))
            ->mapResponse(new Response\LionParcelResponse());
    }

    /**
     * ANTERAJA
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\AnterAjaResponse
     */
    public function anterAja($resi)
    {
        return $this->request(Constants::ANTERAJA)
            ->addHeaders([
                'mv'           => '1.2',
                'source'       => 'aca_android',
                'content-type' => 'application/json',
            ])
            ->addPost([['codes' => $resi]])
            ->mapResponse(new Response\AnterAjaResponse());
    }

    /**
     * REX KIRIMAN EXPRESS
     *
     * @param string $resi nomor resi yang mau di cek
     *
     * @return Response\RexResponse
     */
    public function rex($resi)
    {
        return $this->request(Constants::REX)
            ->addHeader('content-type', 'application/json')
            ->addPost(['awb' => $resi])
            ->mapResponse(new Response\RexResponse());
    }

    /**
     * Request
     *
     * @param string $url
     *
     * @return Request
     */
    public function request($url)
    {
        return new Request($this, $url);
    }
}
