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
class JetExpressResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'JET EXPRESS',
        'site' => 'jetexpress.co.id'
    ];

    /** @var string */
    private $statusDelivery;

    /** @var string */
    private $tanggalTerima;

    /** @var string */
    private $namaPenerima;

    /**
     * Format result yang diproses
     *
     * @param object $data info from the first request
     *
     * @return object
     */
    public function result($data)
    {
        $history = $this->getHistory();

        return $this->build([
            'info'                  => [
                'no_awb'            => $data->awbNumber,
                'service'           => $data->productName,
                'status'            => $this->statusDelivery,
                'tanggal_kirim'     => Utils::setDate($data->transactionDate),
                'tanggal_terima'    => $this->tanggalTerima,
                'asal_pengiriman'   => $data->displayOriginCity,
                'tujuan_pengiriman' => $data->displayDestinationCity,
                'harga'             => $data->totalFee,
                'berat'             => $data->totalWeight * 1000, // gram
                'catatan'           => reset($data->connotes)->itemDescription,
            ],
            'pengirim'              => [
                'nama'              => $data->shipperName,
                'phone'             => null,
                'kota'              => $data->displayOriginCity,
                'alamat'            => $data->displayOriginCity,
            ],
            'penerima'              => [
                'nama'              => $data->consigneeName,
                'nama_penerima'     => $this->namaPenerima,
                'phone'             => null,
                'kota'              => $data->displayDestinationCity,
                'alamat'            => $data->displayDestinationCity,
            ],
            'history'               => $history,
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @param mixed $data
     *
     * @return bool
     */
    public function check($data)
    {
        if (empty($data)) {
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
        $gabung = [];

        // Gabung
        foreach ($this->getResponse() as $k => $v) {
            foreach ($v->tracks as $value) {
                $gabung[] = $value;
            }
        }

        $gabung  = array_reverse($gabung);
        $history = [];

        foreach ($gabung as $k => $v) {
            $this->statusDelivery = $v->status;

            if ($v->status === 'DELIVERED') {
                $this->tanggalTerima = Utils::setDate($v->trackDate);
                $this->namaPenerima  = strtoupper($v->receiverName);
            }

            $history[$k] = [
                'tanggal' => Utils::setDate($v->trackDate),
                'posisi'  => strtoupper($v->location),
                'message' => $v->operationStatusFail ?
                             trim($v->displayedStatus) . ' - ' . $v->operationStatusName :
                             $v->displayedStatus
            ];
        }

        return $history;
    }
}
