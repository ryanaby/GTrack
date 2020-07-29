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
class SiCepatResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'SICEPAT',
        'site' => 'sicepat.com'
    ];

    /** @var string */
    private $namaPenerima;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $response = $this->getResponse()->sicepat->result;

        return $this->build([
            'info'                  => [
                'no_awb'            => $response->waybill_number,
                'service'           => $response->service,
                'status'            => $this->getStatusDelivery($response),
                'tanggal_kirim'     => Utils::setDate($response->send_date),
                'tanggal_terima'    => Utils::setDate($response->POD_receiver_time),
                'asal_pengiriman'   => strtoupper($response->sender_address),
                'tujuan_pengiriman' => strtoupper($response->receiver_address),
                'harga'             => $response->realprice,
                'berat'             => $response->weight * 1000, // gram
                'catatan'           => null,
            ],
            'pengirim'              => [
                'nama'              => trim(strtoupper($response->sender)),
                'phone'             => null,
                'kota'              => strtoupper($response->sender_address),
                'alamat'            => strtoupper($response->sender_address),
            ],
            'penerima'              => [
                'nama'              => trim(strtoupper($response->receiver_name)),
                'nama_penerima'     => trim(strtoupper($this->namaPenerima)),
                'phone'             => null,
                'kota'              => strtoupper($response->receiver_address),
                'alamat'            => strtoupper($response->receiver_address),
            ],
            'history'               => $this->getHistory($response)
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->getResponse()->sicepat->status->code !== 200) {
            return true;
        }

        return false;
    }

    /**
     * Get status pengiriman
     *
     * @param object $response response dari request
     *
     * @return string
     */
    private function getStatusDelivery($response)
    {
        if ($response->last_status->status == 'DELIVERED') {
            $this->namaPenerima = preg_replace('/(.*)\[(.*) - (.*)\](.*)/', '$2', $response->last_status->receiver_name);

            return 'DELIVERED';
        } else {
            return 'ON PROCESS';
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

        foreach ($response->track_history as $k => $v) {
            if ($v->status == 'DELIVERED') {
                $history[$k]['posisi']  = 'Diterima';
                $history[$k]['message'] = $v->receiver_name;
            } else {
                $history[$k]['tanggal'] = Utils::setDate($v->date_time);
                $history[$k]['posisi']  = preg_replace('/(.*)\[(.*)\](.*)/', '$2', $v->city);

                if (strpos($v->city, 'SIGESIT') !== false) {
                    $history[$k]['posisi'] = 'Diantar';
                }

                $history[$k]['message'] = $v->city;
            }
        }

        return $history;
    }
}
