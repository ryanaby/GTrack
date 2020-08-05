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

    /**
     * Format result yang diproses
     *
     * @return object
     */
    public function result()
    {
        $cnote  = $this->getResponse()->cnote;
        $detail = $this->getResponse()->detail[0];

        return $this->build([
            'info'               => [
                'no_awb'         => $cnote->cnote_no,
                'service'        => $cnote->cnote_services_code,
                'status'         => strtoupper($cnote->pod_status),
                'tanggal_kirim'  => Utils::setDate($cnote->cnote_date),
                'tanggal_terima' => Utils::setDate($cnote->cnote_pod_date),
                'harga'          => (int) $cnote->amount,
                'berat'          => (int) $cnote->weight * 1000, // gram
                'catatan'        => $this->getCatatan($cnote),
            ],
            'pengirim'           => [
                'nama'           => rtrim(strtoupper($detail->cnote_shipper_name)),
                'phone'          => null,
                'alamat'         => $this->setAlamat($detail, 'pengirim'),
            ],
            'penerima'           => [
                'nama'           => rtrim(strtoupper($cnote->cnote_receiver_name)),
                'nama_penerima'  => Utils::setIfNull($cnote->cnote_pod_receiver),
                'phone'          => null,
                'alamat'         => $this->setAlamat($detail, 'penerima'),
            ],
            'history'            => $this->getHistory()
        ]);
    }

    /**
     * Check status, true if AWB is not found.
     *
     * @return bool
     */
    public function check()
    {
        if (isset($this->getResponse()->status) && !$this->getResponse()->status) {
            return true;
        }

        return false;
    }

    /**
     * Get catatan
     *
     * @param object $data
     *
     * @return string|null
     */
    private function getCatatan($data)
    {
        if (isset($data->cnote_goods_descr) && !empty($data->cnote_goods_descr)) {
            return $data->cnote_goods_descr;
        } elseif (isset($data->goods_desc) && !empty($data->goods_desc)) {
            return $data->goods_desc;
        } else {
            return null;
        }
    }

    /**
     * Set alamat
     *
     * @param object $detail
     * @param mixed  $type
     *
     * @return string
     */
    private function setAlamat($detail, $type)
    {
        if ($type == 'pengirim') {
            $alamat = [
                $detail->cnote_shipper_city,
                $detail->cnote_shipper_addr1,
                $detail->cnote_shipper_addr2,
                $detail->cnote_shipper_addr3,
            ];

            return rtrim(implode(', ', array_filter($alamat)));
        } elseif ($type == 'penerima') {
            $alamat = [
                $detail->cnote_receiver_city,
                $detail->cnote_receiver_addr1,
                $detail->cnote_receiver_addr2,
                $detail->cnote_receiver_addr3,
            ];

            return rtrim(implode(', ', array_filter($alamat)));
        }

        return null;
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

                $pecah         = preg_split('/[\[\]]/', $v->desc);
                $lastPossition = '';

                if (Utils::exist('DELIVERED', $pecah[0])) {
                    $explode                = explode(' | ', $pecah[1]);
                    $history[$k]['posisi']  = rtrim(end($explode));
                    $history[$k]['message'] = 'DELIVERED';
                } elseif (count($pecah) > 1) {
                    $history[$k]['posisi']  = str_replace(' , ', ', ', $pecah[1]);
                    $history[$k]['message'] = rtrim(str_replace(' AT', '', $pecah[0]));
                } else {
                    if (isset($pecah[1]) && !empty($pecah[1])) {
                        $lastPossition = str_replace(' , ', ', ', $pecah[1]);
                    }

                    $history[$k]['posisi']  = $lastPossition;
                    $history[$k]['message'] = $pecah[0];
                }
            }
        }

        return $history;
    }
}
