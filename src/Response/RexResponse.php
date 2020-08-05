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
class RexResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'REX KIRIMAN EXPRESS',
        'site' => 'rex.co.id'
    ];

    /** @var object */
    public $_response;

    /** @var string */
    public $statusDelivery;

    /** @var string */
    public $namaPenerima;

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
                'no_awb'            => $this->_response->awb,
                'service'           => $this->_response->detail->services_code,
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => Utils::setDate($this->_response->detail->shipped_date),
                'tanggal_terima'    => Utils::setDate($this->_response->detail->delivered_date),
                'asal_pengiriman'   => $this->_response->detail->sender->city,
                'tujuan_pengiriman' => $this->_response->detail->receiver->city,
                'harga'             => $this->_response->detail->actual_amount,
                'berat'             => $this->_response->detail->weight, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => null,
                'phone'             => null,
                'kota'              => $this->_response->detail->sender->city,
                'alamat'            => $this->_response->detail->sender->city,
            ],
            'penerima'              => [
                'nama'              => null,
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => null,
                'kota'              => $this->_response->detail->receiver->city,
                'alamat'            => $this->_response->detail->receiver->city,
            ],
            'history'               => $history,
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        $this->_response = json_decode($this->getResponse());

        if (isset($this->_response->error_id)) {
            return true;
        }

        return false;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @return array
     */
    private function getHistory()
    {
        foreach ($this->_response->history as $k => $v) {
            $this->statusDelivery = strtoupper($v->status);
            $this->namaPenerima   = strtoupper($v->receiver);

            $message = $v->status;
            if ($message == 'manifested') {
                $message = "Diberangkatkan dari $v->city_name";
            } elseif ($message == 'received on destination') {
                $message = "Diterima di $v->city_name";
            } elseif ($message == 'delivered') {
                $this->statusDelivery = 'DELIVERED';
                $message              = "Diterima oleh : $this->namaPenerima";
            }

            $history[$k] = [
                'tanggal' => Utils::setDate($v->date_time),
                'posisi'  => $v->city_name,
                'message' => $message,
            ];
        }

        return $history;
    }
}
