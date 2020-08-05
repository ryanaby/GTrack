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
class NinjaXpressResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'NINJA XPRESS',
        'site' => 'ninjaxpress.co'
    ];

    /** @var string */
    public $statusDelivery;

    /** @var string */
    private $tanggalKirim;

    /** @var string */
    private $tanggalTerima;

    /**
     * Format result yang diproses
     *
     * @param object $response response dari request
     *
     * @return object
     */
    public function result()
    {
        $response = reset($this->getResponse()->orders);
        $history  = $this->getHistory($response);
        $this->_getDeliveryStatus($response);

        return $this->build([
            'info'                  => [
                'no_awb'            => $response->tracking_id,
                'service'           => $response->service_type,
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => $this->tanggalKirim,
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => null,
                'tujuan_pengiriman' => $response->to_city,
                'harga'             => null,
                'berat'             => null, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => trim(strtoupper($response->from_name)),
                'phone'             => null,
                'kota'              => null,
                'alamat'            => null,
            ],
            'penerima'              => [
                'nama'              => $this->getPenerima($response),
                'nama_penerima'     => $this->getPenerima($response),
                'phone'             => null,
                'kota'              => $response->to_city,
                'alamat'            => $response->to_city,
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
        if (empty($this->getResponse())) {
            return true;
        }

        return false;
    }

    /**
     * Get nama penerima
     *
     * @param object $response
     *
     * @return string|null
     */
    private function getPenerima($response)
    {
        if (!empty(($response->transactions))) {
            $trx = end($response->transactions);
            if (!is_null($trx->signature)) {
                return $trx->signature->name;
            }

            return null;
        }

        return null;
    }

    /**
     * get delivery status
     *
     * @param object $response
     */
    private function _getDeliveryStatus($response)
    {
        if ($response->status == 'Completed') {
            $this->statusDelivery = 'DELIVERED';
        } else {
            $this->statusDelivery = strtoupper($response->status);
        }
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @param object $response response dari request
     *
     * @return array
     */
    private function getHistory($response)
    {
        $history = [];

        foreach ($response->events as $k => $v) {
            $time = substr($v->time, 0, -3);

            if (Utils::exist(' - ', $v->description)) {
                $posisi = strtoupper(preg_replace('/(.*) - (.*)/', '$2', $v->description));
            } elseif (Utils::exist('Penitipan Parsel', $v->description)) {
                $posisi = strtoupper(preg_replace('/(.*) Penitipan Parsel (.*)/', '$2', $v->description));
            } else {
                $posisi = null;
            }

            if ($v->description == 'Order dibuat') {
                $this->tanggalKirim = Utils::setDate($time, true);
            }

            if (Utils::exist('Telah berhasil dijemput dari', $v->description)) {
                $this->tanggalKirim = Utils::setDate($time, true);
            }

            if ($v->description == 'Parsel telah berhasil dikirimkan') {
                $this->tanggalTerima = Utils::setDate($time, true);
            }

            $history[$k]['tanggal'] = Utils::setDate($time, true);
            $history[$k]['posisi']  = $posisi;
            $history[$k]['message'] = $v->description;
        }

        return $history;
    }
}
