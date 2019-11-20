<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require './src/PHPMailer.php';
require './src/SMTP.php';
require './src/Exception.php';


    // investing.com adresinden XRP anlık bilgilerini çekiyoruz

    $zaman = date('d.m.Y H:i:s');
    $limit = 0.19;
    $coin_tur = "XRP / RIPPLE";
    $curl_handle = curl_init(); // Curl'u başlattık
    curl_setopt($curl_handle, CURLOPT_URL,'https://tr.investing.com/indices/investing.com-xrp-try');
    // Hangi coin istenirse url kısmına o coin bilgilerini içeren adres yazılır;
    // curl_setopt($curl_handle, CURLOPT_URL,'https://tr.investing.com/indices/investing.com-btc-try');
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'XRP - TL');
    // Ayarları yaptık

    $query = curl_exec($curl_handle); // cURL çalıştırdık

    curl_close($curl_handle); // Sonra cURL kapattık

    // Ayıklama işlemi yapıyoruz
    preg_match_all('@<span class="arial_26 inlineblock pid-1062818-last" id="last_last" dir="ltr">(.*?)</span>@si', $query, $fiyat);
    preg_match_all('@<span class="inlineblock pid-1062818-low">(.*?)</span>@si', $query, $baslangic);

    $data["fiyat"]  = str_replace(",",".",implode("", $fiyat[1])); // Anlık fiyat
    $data["baslangic"]  = str_replace(",",".",implode("", $baslangic[1])); // Günün başlangıç fiyatı
    $data["degisim"] = $data["fiyat"] - $data["baslangic"];

    $icerik = "<h3>Şu anki fiyat: ".$data["fiyat"]."<br><hr>";
    $icerik .= "<h3>Başlangıç fiyatı: ".$data["baslangic"]."<br><hr>";
    $icerik .= "<h3>Değişim: ".$data["degisim"]."<br><hr>";
    $icerik .= "<h3>Tarih: ".$zaman;



    /*
     * HTML Mail şablonu
     */
    $template = '<div style="display:inline-block;width:100%; vertical-align:top;" class="width800">
    							<!-- ID:BG SECTION-2 -->
    							<table align="center" bgcolor="#f6f6f6" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="max-width:800px;" data-bgcolor="Section 2 BG">
    								<tbody>
    									<tr>
    										<td align="center" class="res-padding">
    											<!--[if (gte mso 9)|(IE)]>
    																	<table aria-hidden="true" border="0" cellspacing="0" cellpadding="0" align="center" width="600" style="width:600px;">
    																		<tr>
    																			<td align="center">
    																					<![endif]-->
    											<div style="display:inline-block; width:100%; max-width:600px; vertical-align:top;" class="width600">
    												<table align="center" border="0" class="display-width-inner" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">
    													<tbody><tr>
    														<td height="60" style="mso-line-height-rule:exactly; line-height:60px;">
    															&nbsp;
    														</td>
    													</tr>
    													<tr>
    														<!-- ID:TXT TITLE -->
    														<td align="center" class="MsoNormal" style="color:#333333; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-weight:700; font-size:24px; line-height:34px; letter-spacing:1px;" data-color="Title" data-size="Title" data-min="15" data-max="44">
    															 XRP - TL
    														</td>
    													</tr>
    													<tr>
    														<td height="30" style="mso-line-height-rule:exactly; line-height:30px;font-size: 16px;text-align: center;">
    															'.$durum.'
    														</td>
    													</tr>
    													<tr>
    														<!-- ID:TXT TITLE -->
    														<td align="center" valign="middle" width="600">
    														  <table style="border-radius:10px;border:1px solid;font-family:monospace;">
                                    <thead>
                                      <tr style="background-color: #cdcde2;">
                                        <td>Coin</td>
                                        <td>Şu anki Fiyat</td>
                                        <td>Başlangıç Fiyatı</td>
                                        <td>Değişim</td>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr>
                                        <td>Ripple</td>
                                        <td>'.$data["fiyat"].'</td>
                                        <td>'.$data["baslangic"].'</td>
                                        <td>'.$data["degisim"].'</td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <span style="text-align:center;font-family: monospace;">'.$zaman.'</span>
    														</td>
    													</tr>
    													<tr>
    														<td height="60" style="mso-line-height-rule:exactly; line-height:60px;">
    															&nbsp;
    														</td>
    													</tr>
    												</tbody></table>
    											</div>
    										</td>
    									</tr>
    								</tbody>
    							</table>
    						</div>';

    /*
     * Eğer fiyatta $limit'ten fazla bir artış veya düşüş olduysa mail at
     */

    if($data["degisim"] > $limit || $data["degisim"] < ($limit * -1))
    {
        echo "<br>Fiyat fark:".$data["degisim"];
        $durum = $limit." kuruşluk Değişim";
        mail_gonder($template,$durum);
    } else {
        echo "Pek bir değişiklik yok.";
    }

    /**
     * Mail gönderme işlemi
     * Mail sunucu/kullanıcı bilgileri giriliyor
     * @param $template
     * @param $durum
     */
    function mail_gonder($template,$durum)
    {
        $mail = new PHPMailer;
        try{
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();   // Send using SMTP
            $mail->Host       = 'ornek.emailsunucusu.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                     // Enable SMTP authentication
            $mail->Username   = 'ornek@emailadresi.com';  // SMTP username
            $mail->Password   = 'ornekparola123';       // SMTP password
            $mail->Port       = 587;  // SMTP port
            $mail->isHTML(true);
            $mail->SetLanguage('tr','language');
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('ornek@emailadresi.com', 'XRP-TL Gunluk');
            $mail->addAddress('gonderilecek@gmail.com', 'Adınız');     // Add a recipient
            $mail->addReplyTo('ornek@emailadresi.com', 'Bilgi');
            $mail->Subject = date('H:i').' XRP -TL Bildirimi - '.$durum;
            $mail->Body = $template;

            $mail->send();
            echo 'Mail başarıyla gönderildi!';

        } catch (Exception $e) {
            echo "Mail gönderilemedi.Mailer Error: $mail->ErrorInfo";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?=$coin_tur ?> - TL</title>
</head>
<body>

<div style="display:inline-block; width:100%;vertical-align:top;" class="width800">
							<table align="center" bgcolor="#f6f6f6" border="0" cellpadding="0" cellspacing="0" class="display-width" width="100%" style="max-width:800px;" data-bgcolor="Section 2 BG">
								<tbody>
									<tr>
										<td align="center" class="res-padding">
											<div style="display:inline-block; width:100%; max-width:600px; vertical-align:top;" class="width600">
												<table align="center" border="0" class="display-width-inner" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;">
													<tbody><tr>
														<td height="60" style="mso-line-height-rule:exactly; line-height:60px;">
															&nbsp;
														</td>
													</tr>
													<tr>
														<td align="center" class="MsoNormal" style="color:#333333; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-weight:700; font-size:24px; line-height:34px; letter-spacing:1px;" data-color="Title" data-size="Title" data-min="15" data-max="44">
															 <?=$coin_tur ?>
														</td>
													</tr>
													<tr>
														<td height="30" style="mso-line-height-rule:exactly;line-height:30px;font-size: 16px;text-align: center;">
															&nbsp;
														</td>
													</tr>
													<tr>
														<td align="center" valign="middle" width="600">
														  <table style="border-radius:10px;border:1px solid;font-family:monospace !important;">
                                <thead>
                                  <tr style="background-color: #cdcde2;">
                                    <td>Coin</td>
                                    <td>Şu anki Fiyat</td>
                                    <td>Başlangıç Fiyatı</td>
                                    <td>Değişim</td>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td><?=$coin_tur ?></td>
                                    <td><?=$data["fiyat"];?></td>
                                    <td><?=$data["baslangic"];?></td>
                                    <td><?=$data["degisim"];?></td>
                                  </tr>
                                </tbody>
                              </table>
                              <span style="text-align:center;font-family: monospace;"><?=$zaman;?></span>
														</td>
													</tr>
													<tr>
														<td height="60" style="mso-line-height-rule:exactly; line-height:60px;">
															&nbsp;
														</td>
													</tr>
												</tbody></table>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
</body>
</html>
