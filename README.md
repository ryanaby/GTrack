# GTrack API
<p align="center">
	<img src="https://raw.githubusercontent.com/walangkaji/emboh/master/img/GTrack.png" />
</p>
GTrack merupakan sebuah repository yang digunakan untuk Tracking / Cek Resi pada beberapa ekspedisi pengiriman baik di kelas Indonesia maupun Internasional.

----------
# Support me
- ![Paypal](https://raw.githubusercontent.com/walangkaji/emboh/master/img/paypal.png) Paypal: [Se-Ikhlasnya Saja](https://www.paypal.me/walangkaji)
----------
### Cara Install

### Composer
```sh
$ composer require walangkaji/gtrack
```

### Clone
```sh
$ git clone https://github.com/walangkaji/GTrack.git
$ cd GTrack/
$ composer install
```

### Cara Pakai

```php
require 'vendor/autoload.php';

use GTrack\GTrack;

$GTrack = new GTrack();
$cek    = $GTrack->jne('011440046444019');

var_dump($cek);
```

Jika pengen menggunakan proxy:
```php
$proxy  = '192.168.1.1:1111';
$GTrack = new GTrack($proxy);
```
### Ready digunakan

- **JNE**
- **J&T**
- **TIKI**
- **POS INDONESIA**
- **WAHANA**
- **SICEPAT**
- **NINJAXPRESS**
- **JET EXPRESS**
- **...**

### Methods
```php
GTrack::jne('xxxxxxx')
GTrack::jnt('xxxxxxx')
GTrack::tiki('xxxxxxx')
GTrack::pos('xxxxxxx')
GTrack::wahana('xxxxxxx')
GTrack::siCepat('xxxxxxx')
GTrack::ninjaXpress('xxxxxxx')
GTrack::jetExpress('xxxxxxx')
```

Cukup sekian dan Matursuwun.

Jangan lupa kalo mau support seikhlasnya bisa lewat sini:
- ![Paypal](https://raw.githubusercontent.com/walangkaji/emboh/master/img/paypal.png) Paypal: [Se-Ikhlasnya Saja](https://www.paypal.me/walangkaji)
