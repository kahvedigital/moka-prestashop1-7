<?php

class MokasanalposResultModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;

    public function initContent() {
        parent::initContent();

        $module_action = Tools::getValue('module_action');
        $action_list = array('result' => 'initResult', 'payment' => 'initPayment');

        if (isset($action_list[$module_action])) {
            $this->{$action_list[$module_action]}();
        }
    }

    public function initResult() {

        function replaceSpace($veri) {
            $veri = str_replace("/s+/", "", $veri);
            $veri = str_replace(" ", "", $veri);
            $veri = str_replace(" ", "", $veri);
            $veri = str_replace(" ", "", $veri);
            $veri = str_replace("/s/g", "", $veri);
            $veri = str_replace("/s+/g", "", $veri);
            $veri = trim($veri);
            return $veri;
        }

        ;
        $context = Context::getContext();
        $language_iso_code = $context->language->iso_code;
        $cart = $context->cart;
        $error_msg = '';

        try {

            $name = Tools::getValue('name');
            $number = Tools::getValue('number');
            $expiry = Tools::getValue('expiry');
            $cvc = Tools::getValue('cvc');
            $total = Tools::getValue('mokatotal');

            if ((empty($name))) {

                $error_msg = ($language_iso_code == "tr") ? 'Kredi Kartı İsim Alanı Boş Gönderilemez.' : 'Kredi Kartı İsim Alanı Boş Gönderilemez.';
            } else if ((empty($number))) {

                $error_msg = ($language_iso_code == "tr") ? 'Kart Numarası Alanı Boş Gönderilemez.' : 'Kart Numarası Alanı Boş Gönderilemez.';
            } else if ((empty($expiry))) {

                $error_msg = ($language_iso_code == "tr") ? 'Kredi Kartı Son Kullanım Tarihi Boş Gönderilemez' : 'Kredi Kartı Son Kullanım Tarihi Boş Gönderilemez';
            } else if ((empty($cvc))) {

                $error_msg = ($language_iso_code == "tr") ? 'Kredi Kartı CVC Boş Gönderilemez' : 'Kredi Kartı CVC Boş Gönderilemez';
            } else {
                $expiry = explode("/", $expiry);
                $expiryMM = $expiry[0];
                $expiryYY = $expiry[1];
                $expiryMM = replaceSpace($expiryMM);
                $expiryYY = replaceSpace($expiryYY);
                $number = replaceSpace($number);

                $bankalar = KahveDigital::getAvailablePrograms();
                foreach ($bankalar as $key => $value) {

                    $isim = $key;
                    for ($x = 1; $x <= 12; $x++) {

                        $taksit = $total[$key][$x];
                        if (!empty($taksit)) {
                            $installement = $x;

                            $paid = number_format($taksit, 2, '.', '');
						
                        }
                    }
                }


                if (empty($paid)) {

                    $taksit = $total;
                    $paid = number_format($taksit, 2, '.', '');
                    $installement = 1;
                }

                $ucdaktif = Configuration::get('MOKA_THREED_AKTIF');
                if ($ucdaktif == 'close') {

                    $moka_url = "https://service.moka.com/PaymentDealer/DoDirectPayment";
                } else {

                    $moka_url = "https://service.moka.com/PaymentDealer/DoDirectPaymentThreeD";
                }

                $dealer_code = Configuration::get('MOKA_BAYI_KODU');
                $username = Configuration::get('MOKA_KULLANICI_ADI');
                $password = Configuration::get('MOKA_SYSTEM_PASS');
                $currency = "TL";

                $OtherTrxCode = 'prestashop-1-6-' . $this->context->cookie->id_cart;
                $SubMerchantName = "";
                $RedirectUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'index.php?module_action=payment&fc=module&module=mokasanalpos&controller=result&MyTrxCode=' . $OtherTrxCode;


                $checkkey = hash("sha256", $dealer_code . "MK" . $username . "PD" . $password);
                $veri = array('PaymentDealerAuthentication' => array('DealerCode' => $dealer_code, 'Username' => $username, 'Password' => $password,
                        'CheckKey' => $checkkey),
                    'PaymentDealerRequest' => array('CardHolderFullName' => $name,
                        'CardNumber' => $number,
                        'ExpMonth' => $expiryMM,
                        'ExpYear' => '20' . $expiryYY,
                        'CvcNumber' => $cvc,
                        'Amount' => $paid,
                        'Currency' => $currency,
                        'InstallmentNumber' => $installement,
                        'ClientIP' => $_SERVER['REMOTE_ADDR'],
                        'RedirectUrl' => $RedirectUrl,
                        'OtherTrxCode' => $OtherTrxCode,
                        'SubMerchantName' => $SubMerchantName));


                $veri = json_encode($veri);
                $ch = curl_init($moka_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $veri);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);    // ssl sayfa baglantilarinda aktif edilmeli
                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result);
                $ResultCode = $result->ResultCode;
                $Data = $result->Data;
                $ResultMessage = $result->ResultMessage;

                if ($ResultCode == 'Success') {

                    header("Location: " . $Data);
                } else {

                    switch ($ResultCode) {
                        case "PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest":
                            $errr = "Hatalı hash bilgisi";
                            break;
                        case "PaymentDealer.RequiredFields.AmountRequired":
                            $errr = "Tutar Göndermek Zorunludur.";
                            break;
                        case "PaymentDealer.RequiredFields.ExpMonthRequired":
                            $errr = "Son Kullanım Tarihi Gönderme Zorunludur.";
                            break;

                        case "PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount":
                            $errr = "Böyle bir bayi bulunamadı";
                            break;
                        case "PaymentDealer.CheckPaymentDealerAuthentication.VirtualPosNotFound":
                            $errr = "Bu bayi için sanal pos tanımı yapılmamış";
                            break;
                        case "PaymentDealer.CheckDealerPaymentLimits.DailyDealerLimitExceeded":
                            $errr = "Bayi için tanımlı günlük limitlerden herhangi biri aşıldı";
                            break;
                        case "PaymentDealer.CheckDealerPaymentLimits.DailyCardLimitExceeded":
                            $errr = "Gün içinde bu kart kullanılarak daha fazla işlem yapılamaz";

                        case "PaymentDealer.CheckCardInfo.InvalidCardInfo":
                            $errr = "Kart bilgilerinde hata var";
                            break;
                        case "PaymentDealer.DoDirectPayment3dRequest.InstallmentNotAvailableForForeignCurrencyTransaction":

                            $errr = "Yabancı para ile taksit yapılamaz";
                            break;
                        case "PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForDealer":
                            $errr = "Bu taksit sayısı bu bayi için yapılamaz";
                            break;
                        case "PaymentDealer.DoDirectPayment3dRequest.InvalidInstallmentNumber":
                            $errr = "Taksit sayısı 2 ile 9 arasıdır";
                            break;
                        case "PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForVirtualPos":
                            $errr = "Sanal Pos bu taksit sayısına izin vermiyor";
                            break;

                        default:
                            $errr = "Beklenmeyen Bir hata Oluştu";
                    }
                    $error_msg = $errr;
                }


                $this->context->smarty->assign(array(
                    'error' => $error_msg,
                    'result' => $result,
                    'ResultCode' => $ResultCode,
                    'ResultMessage' => $ResultMessage,
                    'Data' => $Data,
                    'name' => $name,
                    'locale' => $test,
                    'sayi' => $gelensayi,
                    'banks' => $bankalar,
                ));
            }
            $this->context->smarty->assign(array(
                'error' => $error_msg,
            ));

            $this->setTemplate('module:mokasanalpos/views/templates/front/paymentValid.tpl');
        } catch (\Exception $ex) {
            $error_msg = $ex->getMessage();
            if (!empty($error_msg)) {
            
            }
            $this->context->smarty->assign(array(
                'error' => $error_msg,
            ));
            $this->setTemplate('module:mokasanalpos/views/templates/front/paymentValid.tpl');
        }
    }

    public function initPayment() {



        $mokapos = new Mokasanalpos();
        $context = Context::getContext();
        $language_iso_code = $context->language->iso_code;
        $cart = $context->cart;
        $error_msg = '';

        $success = $_POST['isSuccessful'];
        $resultMessage = $_POST['resultMessage'];
        $trxCode = $_POST['trxCode'];


        $cart_total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $payment_currency = "TRY";
        $currency = new Currency((int) ($cart->id_currency));
        $iso_code = ($currency->iso_code) ? $currency->iso_code : '';

        if ($success == 'False') {
            $error_msg = $resultMessage;
        } else if ($success == 'True') {
            $mokapos->validateOrder((int) $cart->id, Configuration::get('PS_OS_PAYMENT'), $cart_total, $mokapos->displayName, null, $total, (int) $currency->id, false, $cart->secure_key);
            $success = $trxCode;
        }

        $local = "tr";

        $this->context->smarty->assign(array(
            'error' => $error_msg,
            'locale' => $local,
            'success' => $success,
            'currency' => $iso_code,
        ));

        $this->setTemplate('module:mokasanalpos/views/templates/front/order_result.tpl');
    }

}
