# PHP Kripto Para Habercisi
Php ile hazırlanmış basit kriptop para habercisi.


# Kullanımı

**Örnek kullanım**<br>
Curl ile bağlanıp www.investing.com üzerinden anlık XRP - TL  fiyat bilgisini çekelim. Mail atması için gereken limit aralığını belirleyelim.

``` php
$adres = 'https://tr.investing.com/indices/investing.com-xrp-try';
$limit = 0.19; // TL
```

**Mail ayarlarını yapalım**<br>

Eğer belirlediğimiz limitten fazla bir değişim mevcutsa kendi mail adresimize mail attıralım. Bunun için PHPMailer kullanıyoruz ve gerekli e-posta sunucu, hesap bilgilerini giriyoruz.

``` php
        $mail->Host       = 'ornek.emailsunucusu.com'; // Set the SMTP server to send through
        $mail->Username   = 'ornek@emailadresi.com';  // SMTP username
        $mail->Password   = 'ornekparola123';       // SMTP password
        $mail->Port       = 587;  // SMTP port
        ..
        $mail->addAddress('gonderilecek@gmail.com', 'Adınız');     // Add a recipient
```
**Zamanlama** <br>
Sunucunuzun paneli üzerinden Cron görevi oluşturarak, bu işlemleri belirli zamanlara bağlayabilirsiniz. Ayarladığınız cron görevi sayesinde zamanladığınız bu dosya ilgili zamanda sunucu tarafından otomatik çalıştırılacak ve görevini yerine getirecektir. 

**Yazar**
[Mustafa Çakmak](http://www.gunlukyaz.com/)
