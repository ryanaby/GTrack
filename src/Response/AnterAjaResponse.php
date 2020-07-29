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
class AnterAjaResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'ANTERAJA',
        'site' => 'anteraja.id'
    ];

    /** @var string */
    public $statusDelivery;

    /** @var string */
    public $tanggalTerima;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $response = reset($this->getResponse()->content);
        $history  = $this->getHistory($response);

        return $this->build([
            'info'                  => [
                'no_awb'            => $response->awb,
                'service'           => $response->detail->service_code,
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => reset($history)['tanggal'],
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $response->detail->sender->address,
                'tujuan_pengiriman' => $response->detail->receiver->address,
                'harga'             => $response->detail->actual_amount,
                'berat'             => $response->detail->weight, // gram
                'catatan'           => trim(reset($response->items)->item_desc),
            ],
            'pengirim'              => [
                'nama'              => $response->detail->sender->name,
                'phone'             => $response->detail->sender->phone,
                'kota'              => $response->detail->sender->address,
                'alamat'            => $response->detail->sender->address,
            ],
            'penerima'              => [
                'nama'              => $response->detail->receiver->name,
                'nama_penerima'     => $response->detail->actual_receiver,
                'phone'             => $response->detail->receiver->phone,
                'kota'              => $response->detail->receiver->address,
                'alamat'            => $response->detail->receiver->address,
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
        if ($this->getResponse()->status !== 200) {
            return true;
        }

        return false;
    }

    /**
     * Compile history dengan format yang sudah disesuaikan
     *
     * @param object $response
     *
     * @return array
     */
    private function getHistory($response)
    {
        foreach (array_reverse($response->history) as $k => $v) {
            $message = $v->message->id;

            if (Utils::exist('pickup', $message)) {
                $possition            = 'PICKUP';
                $this->statusDelivery = 'PICKUP';
            } elseif (Utils::exist('Parcel sudah tiba di', $message)) {
                $possition            = Utils::getBetween($message, 'Parcel sudah tiba di ', ' untuk');
                $this->statusDelivery = 'ON PROCESS';
            } elseif (Utils::exist('Delivery sukses', $message)) {
                $this->statusDelivery = 'DELIVERED';
                $this->tanggalTerima  = $v->timestamp;
            } elseif (Utils::exist('segera diantar ke penerima', $message)) {
                $this->statusDelivery = 'ON DELIVERY';
            } elseif (Utils::exist('Proses transit parcel dikirim dari', $message)) {
                $possition = preg_replace('/(.*)Proses transit parcel dikirim dari Hub (.*)/', '$2', $message);
            } elseif (Utils::exist('tertunda', $message)) {
                $this->statusDelivery = 'PENDING';
            } else {
                $this->statusDelivery = 'ON PROCESS';
            }

            $history[$k] = [
                'tanggal' => Utils::setDate($v->timestamp),
                'posisi'  => trim(strtoupper(str_replace('.', '', $possition))),
                'message' => $message
            ];
        }

        return $history;
    }
}
