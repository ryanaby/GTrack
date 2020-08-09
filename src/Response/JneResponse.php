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
class JneResponse extends Response
{
    /** @var array */
    public $ekspedisi  = [
        'name' => 'JNE',
        'site' => 'jne.co.id'
    ];

    /** @var string */
    public $namaPenerima;

    /** @var string */
    public $tanggalTerima;

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $history = $this->getHistory();

        return $this->build([
            'info'               => [
                'no_awb'         => $this->response->summary->awb,
                'service'        => $this->response->summary->service,
                'status'         => $this->response->status,
                'tanggal_kirim'  => Utils::setDate($this->response->summary->date),
                'tanggal_terima' => $this->tanggalTerima,
                'harga'          => (int) $this->response->summary->amount,
                'berat'          => (int) $this->response->summary->weight * 1000, // gram
                'catatan'        => $this->response->summary->desc,
            ],
            'pengirim'           => [
                'nama'           => strtoupper(rtrim($this->response->detail->shipper)),
                'phone'          => null,
                'alamat'         => $this->response->detail->origin,
            ],
            'penerima'           => [
                'nama'           => strtoupper($this->response->detail->receiver),
                'nama_penerima'  => $this->namaPenerima,
                'phone'          => null,
                'alamat'         => $this->response->detail->destination,
            ],
            'history'            => $history
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        if (isset($this->getResponse()->error)) {
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
        $history = [];

        if (!empty($this->getResponse()->history)) {
            foreach ($this->getResponse()->history as $k => $v) {
                $history[$k]['tanggal'] = Utils::setDate($v->date);

                $pecah = preg_split('/[\[\]]/', $v->desc);

                if (Utils::exist('Delivered', $v->desc)) {
                    $explode                = explode(' | ', $pecah[1]);
                    $this->namaPenerima     = strtoupper(rtrim(reset($explode)));
                    $this->tanggalTerima    = Utils::setDate($v->date);
                    $history[$k]['posisi']  = strtoupper(rtrim(end($explode)));
                    $history[$k]['message'] = 'DELIVERED';
                } else {
                    $history[$k]['posisi']  = $v->location;
                    $history[$k]['message'] = rtrim(str_replace(' At', '', $pecah[0]));
                }
            }
        }

        return $history;
    }
}
