<?php
/**
 * This file is part of GTrack.
 *
 * @author walangkaji <walangkaji@outlook.com>
 */

namespace GTrack;

class Constants
{
    const JNE         = 'http://apiv2.jne.co.id:10101/tracing/api/list/myjne/cnote/%s';
    const JNE_KEY     = '504fbae0d815bf3e73a7416be328fcf2';
    const JNT         = 'http://jk.jet.co.id:22234/jandt-app-ifd-web/router.do';
    const TIKI        = 'https://my.tiki.id/api/connote/information';
    const TIKI_AUTH   = '0437fb74-91bd-11e9-a74c-06f2c0b7c6f0-91bf-11e9-a74c-06f2c4b0b602';
    const POS         = 'https://order.posindonesia.co.id/api/lacak';
    const WAHANA      = 'http://intranet.wahana.com/ci-oauth2/Api/trackingNew';
    const WAHANA_AUTH = '093a64444fa19f591682f7087a5e5a08febd9e43';
    const SICEPAT     = 'http://api.sicepat.com/customer/waybill?waybill=%s';
    const SICEPAT_KEY = '96625fdc2bfe59fa05dcf7c9c71755dd';
    const NINJAXPRESS = 'https://api.ninjavan.co/id/shipperpanel/app/tracking?id=%s';
    const JETEXPRESS  = 'http://jet-api-resource.azurewebsites.net/v1/tracks/waybills?awbNumbers=%s';
    const JET_HISTORY = 'http://jet-api-resource.azurewebsites.net/v2/tracks/Waybills/%s/items/%s/language/en/timezone/0';
    const LION_PARCEL = 'https://algo-api.lionparcel.com/v1/shipment/search?reference=%s';
    const ANTERAJA    = 'https://api.anteraja.id/order/tracking';
    const REX         = 'https://api.rex.co.id/KonosWS/v2/CekStatusMobile.aspx';
    const DALVIK_UA   = 'Dalvik/2.1.0 (Linux; U; Android 5.1.1; SM-N950N Build/LYZ28N)';
}
