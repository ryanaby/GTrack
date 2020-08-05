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
class LionParcelResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'LION PARCEL',
        'site' => 'lionparcel.com'
    ];

    /** @var string */
    public $statusDelivery;

    /** @var string */
    public $tanggalTerima;

    /** @var string */
    public $namaPenerima;

    /** @var string */
    public $lastPossition;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $history = $this->getHistory();

        return $this->build([
            'info'                  => [
                'no_awb'            => $this->response->package_id,
                'service'           => sprintf('%s (%s)', $this->response->product_type, $this->response->service_type),
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => Utils::setDate($this->response->created_at),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $this->response->origin,
                'tujuan_pengiriman' => $this->response->destination,
                'harga'             => $this->totalHarga(),
                'berat'             => $this->response->gross_weight * 1000, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => $this->response->sender->name,
                'phone'             => $this->response->sender->phone,
                'kota'              => $this->response->origin,
                'alamat'            => $this->response->sender->address,
            ],
            'penerima'              => [
                'nama'              => $this->response->recipient->name,
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => $this->response->recipient->phone,
                'kota'              => $this->response->destination,
                'alamat'            => $this->response->recipient->address,
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
        if (isset($this->getResponse()->error_id)) {
            return true;
        }

        return false;
    }

    /**
     * Calculate total harga
     *
     * @return int
     */
    private function totalHarga()
    {
        return array_sum([
            $this->response->publish_rate,
            $this->response->forward_rate,
            $this->response->shipping_surcharge_rate,
            $this->response->commodity_surcharge_rate,
            $this->response->heavy_weight_surcharge_rate,
            $this->response->insurance_rate,
            $this->response->wood_packing_rate,
        ]);
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @return array
     */
    private function getHistory()
    {
        $history   = [];
        $histories = array_reverse($this->getResponse()->histories);
        foreach ($histories as $k => $v) {
            $this->statusDelivery = strtoupper($v->status);
            $this->lastPossition  = $v->city == '' ? $this->lastPossition : strtoupper($v->city);

            if ($v->status == 'Terkirim') {
                $this->statusDelivery = strtoupper('DELIVERED');
                $this->tanggalTerima  = Utils::setDate($v->created_at);
                $this->namaPenerima   = strtoupper($v->person_name);
            }

            $history[$k] = [
                'tanggal' => Utils::setDate($v->created_at),
                'posisi'  => $this->lastPossition,
                'message' => $v->long_status,
            ];
        }

        return $history;
    }
}
