<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

use GTrack\GTrack;
use GTrack\Utils\Utils;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    private $responseKey = [
        'name',
        'site',
        'error',
        'message',
        'info' => [
            'no_awb',
            'service',
            'status',
            'tanggal_kirim',
            'tanggal_terima',
            'harga',
            'berat',
            'catatan',
        ],
        'pengirim' => [
            'nama',
            'phone',
            'alamat',
        ],
        'penerima' => [
            'nama',
            'nama_penerima',
            'phone',
            'alamat',
        ],
        'history' => [
            [
                'tanggal',
                'posisi',
                'message'
            ]
        ]
    ];

    public function setUp() : void
    {
        $this->resi   = json_decode(file_get_contents('test_valid_resi.json'));
        $this->gtrack = new GTrack();
    }

    public function check($response)
    {
        foreach ($this->responseKey as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $v) {
                    if (is_array($v)) {
                        foreach ($v as $x => $y) {
                            $this->assertArrayHasKey($y, $response[$key][$x]);
                        }
                    } else {
                        $this->assertArrayHasKey($v, $response[$key]);
                    }
                }
            } else {
                $this->assertArrayHasKey($val, $response);
            }
        }
    }

    public function testJneResponse()
    {
        $response = $this->gtrack->jne($this->resi->jne);
        $this->check(Utils::decode($response, true));
    }

    public function testJntResponse()
    {
        $response = $this->gtrack->jnt($this->resi->jnt);
        $this->check(Utils::decode($response, true));
    }

    public function testTikiResponse()
    {
        $response = $this->gtrack->tiki($this->resi->tiki);
        $this->check(Utils::decode($response, true));
    }

    public function testPosResponse()
    {
        $response = $this->gtrack->pos($this->resi->pos);
        $this->check(Utils::decode($response, true));
    }

    public function testWahanaResponse()
    {
        $response = $this->gtrack->wahana($this->resi->wahana);
        $this->check(Utils::decode($response, true));
    }

    public function testSiCepatResponse()
    {
        $response = $this->gtrack->siCepat($this->resi->siCepat);
        $this->check(Utils::decode($response, true));
    }

    public function testNinjaXpressResponse()
    {
        $response = $this->gtrack->ninjaXpress($this->resi->ninjaXpress);
        $this->check(Utils::decode($response, true));
    }

    public function testJetExpressResponse()
    {
        $response = $this->gtrack->jetExpress($this->resi->jetExpress);
        $this->check(Utils::decode($response, true));
    }

    public function testLionParcelResponse()
    {
        $response = $this->gtrack->lionParcel($this->resi->lionParcel);
        $this->check(Utils::decode($response, true));
    }

    public function testAnterAjaResponse()
    {
        $response = $this->gtrack->anterAja($this->resi->anterAja);
        $this->check(Utils::decode($response, true));
    }

    public function testRexResponse()
    {
        $response = $this->gtrack->rex($this->resi->rex);
        $this->check(Utils::decode($response, true));
    }
}
