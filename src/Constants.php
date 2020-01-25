<?php
/**
 * Global Tesla - globaltesla.com
 *
 * @author     Global Tesla <dev@globaltesla.com>
 * @copyright  2019 Global Tesla
 */

namespace GTrack;

class Constants
{
    const JNE_HOST      = 'apiv2.jne.co.id:10101';
    const JNE           = 'http://apiv2.jne.co.id:10101/tracing/api/list/myjne/cnote/%s';
    const JNT_HOST      = 'jk.jet.co.id:22234';
    const JNT           = 'http://jk.jet.co.id:22234/jandt-app-ifd-web/router.do';
    const TIKI_HOST     = 'my.tiki.id';
    const TIKI_INFO     = 'https://my.tiki.id/api/connote/info';
    const TIKI_HISTORY  = 'https://my.tiki.id/api/connote/history';
    const POS_GET       = 'https://www.posindonesia.co.id/id/tracking?resi=%s';
    const POS           = 'https://www.posindonesia.co.id/id/api-get-resi';
    const WAHANA_HOST   = 'intranet.wahana.com';
    const WAHANA        = 'http://intranet.wahana.com/ci-oauth2/Api/trackingNew';
    const SICEPAT_HOST  = 'api.sicepat.com';
    const SICEPAT       = 'http://api.sicepat.com/customer/waybill';
    const NINJAXPRESS   = 'https://api.ninjavan.co/id/shipperpanel/app/tracking';
    const JETEXPRESS    = 'http://jet-api-resource.azurewebsites.net/v1/tracks/waybills';
    const JET_HISTORY   = 'http://jet-api-resource.azurewebsites.net/v2/tracks/Waybills/%s/items/%s/language/en/timezone/0';

    const DALVIK_UA     = 'Dalvik/2.1.0 (Linux; U; Android 5.1.1; LGM-V300K Build/N2G47H)';
}
