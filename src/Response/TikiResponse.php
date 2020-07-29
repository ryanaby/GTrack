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
class TikiResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'TIKI',
        'site' => 'tiki.id'
    ];

    /** @var string */
    private $statusDelivery;

    /** @var string */
    private $namaPenerima;

    /** @var string */
    private $tanggalTerima;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $response = $this->getResponse()->response[0];
        $history  = $this->getHistory($response);

        return $this->build([
            'info'                  => [
                'no_awb'            => $response->cnno,
                'service'           => $response->product,
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => Utils::setDate($response->sys_created_on),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $response->consignor_address,
                'tujuan_pengiriman' => $response->destination_city_name,
                'harga'             => (int) $response->shipment_fee,
                'berat'             => (int) $response->weight * 1000, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => rtrim(strtoupper($response->consignor_name)),
                'phone'             => null,
                'kota'              => rtrim($response->consignor_address),
                'alamat'            => Utils::setIfNull($response->consignor_address),
            ],
            'penerima'              => [
                'nama'              => rtrim(strtoupper($response->consignee_name)),
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => null,
                'kota'              => $response->destination_city_name,
                'alamat'            => Utils::setIfNull($response->consignee_address),
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
        if ($this->getResponse()->status !== 200) {
            return true;
        }

        return false;
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

        foreach (array_reverse($response->history) as $k => $v) {
            if ($v->status == 'POD 01') {
                $this->statusDelivery = 'DELIVERED';
                $this->namaPenerima   = preg_replace('/(.*) RECEIVED BY: (.*)/', '$2', $v->noted);
                $this->tanggalTerima  = Utils::setDate($v->entry_date);
            } elseif ($v->status == 'POD 04') {
                $this->statusDelivery = 'SUCCESS';
                $this->namaPenerima   = preg_replace('/(.*) PENGIRIM: (.*)/', '$2', $v->noted);
                $this->tanggalTerima  = Utils::setDate($v->entry_date);
            } else {
                $this->statusDelivery = 'ON PROCESS';
                $this->namaPenerima   = null;
                $this->tanggalTerima  = null;
            }

            $history[$k]['tanggal'] = Utils::setDate($v->entry_date);
            $history[$k]['posisi']  = rtrim($v->entry_name);
            $history[$k]['message'] = $v->noted;
        }

        return $history;
    }
}
