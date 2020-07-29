<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack\Response;

use GTrack\Response;
use GTrack\Utils\Utils;

/**
 * Formatting response
 */
class JntResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'JNT',
        'site' => 'jet.co.id'
    ];

    /** @var object */
    public $_response;

    /** @var string */
    private $tanggalTerima;

    /** @var string */
    private $tujuanPengiriman;

    /** @var string */
    private $namaPenerima;

    /** @var string */
    private $kotaPenerima;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $details = array_reverse($this->_response->details);
        $history = $this->getHistory($details);

        return $this->build([
            'info'                  => [
                'no_awb'            => $this->_response->billCode,
                'service'           => null,
                'status'            => strtoupper($this->_response->status),
                'tanggal_kirim'     => Utils::setDate($details[0]->acceptTime),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $details[0]->city,
                'tujuan_pengiriman' => $this->tujuanPengiriman,
                'harga'             => null,
                'berat'             => null, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => null,
                'phone'             => null,
                'kota'              => $details[0]->city,
                'alamat'            => $details[0]->city,
            ],
            'penerima'              => [
                'nama'              => $this->namaPenerima,
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => null,
                'kota'              => $this->kotaPenerima,
                'alamat'            => $this->kotaPenerima,
            ],
            'history'               => $history
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        $response = json_decode($this->getResponse());
        $response = json_decode($response->data)->bills[0];

        $this->_response = $response;

        if (empty($response->details)) {
            return true;
        }

        return false;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @param array $details detail history
     *
     * @return array
     */
    private function getHistory($details)
    {
        $history = [];

        foreach ($details as $k => $v) {
            $history[$k]['tanggal'] = Utils::setDate($v->acceptTime);
            $history[$k]['posisi']  = empty($v->siteName) ? $v->city : "$v->city ($v->siteName)";

            switch ($v->scanstatus) {
                case 'Departed':
                    $history[$k]['message'] = "Telah berangkat dari $v->state $v->city menuju $v->nextsite";
                    break;
                case 'Picked Up':
                    $history[$k]['message'] = sprintf(
                        'Telah diambil oleh %s (%s) dari %s %s',
                        strtoupper($v->deliveryName),
                        $v->deliveryPhone,
                        $v->state,
                        $v->city
                    );
                    break;
                case 'Arrived':
                    $history[$k]['message'] = "Telah tiba ke $v->state";
                    break;
                case 'On Delivery':
                    $history[$k]['message'] = sprintf(
                        'Sedang diantar oleh %s (%s) dari %s',
                        strtoupper($v->deliveryName),
                        $v->deliveryPhone,
                        $v->state
                    );
                    break;
                case 'Delivered':
                    $history[$k]['message'] = sprintf(
                        'Terkirim kepada %s',
                        strtoupper($v->signer)
                    );

                    $this->tanggalTerima    = Utils::setDate($v->acceptTime);
                    $this->tujuanPengiriman = strtoupper($v->city);
                    $this->namaPenerima     = strtoupper($v->signer);
                    $this->kotaPenerima     = strtoupper($v->city);

                    break;
            }
        }

        return $history;
    }
}
