	<?php
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
if (!defined('_PS_VERSION_'))
    exit;



include(dirname(__FILE__) . '/class/kahvedigital.php');

class Mokasanalpos extends PaymentModule {

    protected $_html = '';
    protected $_postErrors = array();
    public $details;
    public $owner;
    public $address;
    public $extra_mail_vars;
    public $_prestashop = '_ps';
    public $_ModuleVersion = '1.7.0';
    protected $hooks = array(
        'payment',
        'backOfficeHeader',
        'displayAdminOrder'
    );

    public function __construct() {
        $this->name = 'mokasanalpos';
        $this->tab = 'payments_gateways';
        $this->version = '1.7.0';
        $this->author = 'KahveDigital';
        $this->controllers = array('payment', 'validation');
        $this->is_eu_compatible = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Moka Sanal Pos');
        $this->description = $this->l('Moka ile kolay ödeme');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => '1.7.99.99');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');
    }

    public function install() {
        if (!parent::install() || !$this->registerHook('paymentOptions') || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn')) {
            return false;
        }
        Configuration::updateValue('MOKA_TAKSIT', serialize(KahveDigital::setRatesDefault()));


        return true;
    }

    public function uninstall() {
        if (parent::uninstall()) {
            Configuration::deleteByName('MOKA_BAYI_KODU');
            Configuration::deleteByName('MOKA_KULLANICI_ADI');

            Configuration::deleteByName('MOKA_SYSTEM_PASS');
            foreach ($this->hooks as $hook) {
                if (!$this->unregisterHook($hook))
                    return false;
            }
        }
        return true;
    }

    protected function _postValidation() {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('MOKA_BAYI_KODU') || !Tools::getValue('MOKA_KULLANICI_ADI') || !Tools::getValue('MOKA_BAYI_KODU') || !Tools::getValue('MOKA_KULLANICI_ADI')) {
                $this->_postErrors[] = $this->l('Account keys are required.');
            }
        }
    }

    protected function _postProcess() {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MOKA_BAYI_KODU', Tools::getValue('MOKA_BAYI_KODU'));
            Configuration::updateValue('MOKA_KULLANICI_ADI', Tools::getValue('MOKA_KULLANICI_ADI'));
            Configuration::updateValue('MOKA_SYSTEM_PASS', Tools::getValue('MOKA_SYSTEM_PASS'));
            Configuration::updateValue('MOKA_TAKSIT_AKTIF', Tools::getValue('MOKA_TAKSIT_AKTIF'));
            Configuration::updateValue('MOKA_THREED_AKTIF', Tools::getValue('MOKA_THREED_AKTIF'));
			Configuration::updateValue('MOKA_MAX_TAKSIT', Tools::getValue('MOKA_MAX_TAKSIT'));
			
        }
        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function getContent() {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
        } else





        if (((bool) Tools::isSubmit('kahvedigital_moka_taksit_update')) == true) {
            Configuration::updateValue('MOKA_TAKSIT', serialize(Tools::getValue('kahvedigital_moka_taksit')));
        }



        $version = $this->_ModuleVersion;
        $psver = _PS_VERSION_;
	$serverdomain=$_SERVER['HTTP_HOST'];    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.kahvedigital.com/version');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "psversion=$psver&moka=$version&type=prestashop&domain=$serverdomain");
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        $this->context->smarty->assign('version', $response);

        $test = $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $this->context->smarty->assign('link', $test);
        if ($version == $response['moka_version']) {
            if (isset($_GET['updated_moka'])) {
                $version_updatable = $_GET['updated_moka'];

                function recurse_copy($src, $dst) {
                    $dir = opendir($src);
                    @mkdir($dst);
                    while (false !== ( $file = readdir($dir))) {
                        if (( $file != '.' ) && ( $file != '..' )) {
                            if (is_dir($src . '/' . $file)) {
                                recurse_copy($src . '/' . $file, $dst . '/' . $file);
                            } else {
                                copy($src . '/' . $file, $dst . '/' . $file);
                            }
                        }
                    }
                    closedir($dir);
                }

                function rrmdir($dir) {
                    if (is_dir($dir)) {
                        $objects = scandir($dir);
                        foreach ($objects as $object) {
                            if ($object != "." && $object != "..") {
                                if (filetype($dir . "/" . $object) == "dir")
                                    rrmdir($dir . "/" . $object);
                                else
                                    unlink($dir . "/" . $object);
                            }
                        }
                        reset($objects);
                        rmdir($dir);
                    }
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://api.kahvedigital.com/update');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "new_version=$version_updatable");
                $response = curl_exec($ch);
                $response = json_decode($response, true);
                curl_close($ch);
                $serveryol = $_SERVER['DOCUMENT_ROOT'];
                $ch = curl_init();
                $source = $response['file_dest'];
                curl_setopt($ch, CURLOPT_URL, $source);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
                $foldername = $response['version_name'];
                $fullfoldername = $serveryol . '/' . $foldername;
                if (!file_exists($fullfoldername)) {
                    mkdir($fullfoldername);
                }
                $unzipfilename = 'mokasanalpos.zip';
                $file = fopen($fullfoldername . '/' . $unzipfilename, "w+");
                fputs($file, $data);
                fclose($file);
                $path = pathinfo(realpath($fullfoldername . '/' . $unzipfilename), PATHINFO_DIRNAME);
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive;
                    $res = $zip->open($fullfoldername . '/' . $unzipfilename);
                    if ($res === TRUE) {
                        $zip->extractTo($path);
                        $zip->close();
                        $zip_name_folder = $response['zip_name_folder'];
                        recurse_copy($fullfoldername . '/' . $zip_name_folder, _PS_MODULE_DIR_ . '/' . $zip_name_folder);
                        rrmdir($fullfoldername);
                    }
                }
            }
        }



        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'err' => $this->displayError($err),
            'kahvedigital_moka_form' => $this->renderForm(),
            'kahvedigital_taksit_form' => KahveDigital::createRatesUpdateForm(unserialize(Configuration::get('MOKA_TAKSIT'))),
            'kahvedigital_moka_try_currency_enabled' => (int) Currency::getIdByIsoCode('TRY'),
        ));
        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/configure.tpl');
    }

    public function hookPaymentOptions($params) {
       try {

        $currency_iso = $currency[0]['iso_code'];
        $this->smarty->assign('currency_iso', $currency_iso);
        $taksit = unserialize(Configuration::get('MOKA_TAKSIT'));
 
   
        $total_cart = (double) number_format($params['cart']->getOrderTotal(true, Cart::BOTH), 2, '.', '');
        $moka_rates = KahveDigital::calculatePrices($total_cart, $taksit);
        $this->smarty->assign('rates', $moka_rates);
        $taksitaktif = Configuration::get('MOKA_TAKSIT_AKTIF');
		$maxtaksit = Configuration::get('MOKA_MAX_TAKSIT');
		$this->smarty->assign('max_taksit', $maxtaksit);
        $this->smarty->assign('taksit', $taksitaktif);
        $formurl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'index.php?module_action=result&fc=module&module=mokasanalpos&controller=result';
        $this->smarty->assign('url', $formurl);
        $this->smarty->assign('total', $total_cart);
		
        $iso_code = $this->context->language->iso_code;
		         $error_terms = ($iso_code == "tr") ? 'Şartlar ve koşulları kabul etmeniz gerekir.' : 'You must accept terms and conditions.';
            $this->smarty->assign(array(
                'error_terms' => $error_terms,
            ));
        $credit_card = ($iso_code == "tr") ? "Kredi Kartı" : "Credit Card";
        $this->smarty->assign('credit_card', $credit_card);
        $this->smarty->assign('module_dir', $this->_path);
                $logo = Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/img/mokaicon.png');
                $newOption = new PaymentOption();
                $newOption->setCallToActionText($this->trans('Kredi Kartı İle Öde', array(), 'Modules.Mokasanalpos'))
                       ;
						
                $newOption->setAction($this->context->link->getModuleLink($this->name, 'payment', array(), true))->setLogo($logo);
				  $newOption->setModuleName('mokasanalpos', array(), 'Modules.Mokasanalpos')
				     ->setAdditionalInformation($this->fetch('module:mokasanalpos/views/templates/hook/payment.tpl'));
                $payment_options = [
                    $newOption,
                ];
				
                return $payment_options;
				
				     } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }

    public function renderForm() {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Moka Bayi Kodu'),
                        'name' => 'MOKA_BAYI_KODU',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Moka Kullanıcı'),
                        'name' => 'MOKA_KULLANICI_ADI',
                        'required' => true
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Moka Sistem Şifre'),
                        'name' => 'MOKA_SYSTEM_PASS',
                        'required' => true
                    ),
					                    array(
                        'type' => 'text',
                        'label' => $this->l('Moka Maximum Taksit ÖRN:(9)'),
                        'name' => 'MOKA_MAX_TAKSIT',
                        'required' => true
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Taksit Etkinleştirme'),
                        'name' => 'MOKA_TAKSIT_AKTIF',
                        'values' => array(
                            array(
                                'value' => 'tekcekim',
                                'label' => $this->l('Tek Çekim')
                            ),
                            array(
                                'value' => 'taksit',
                                'label' => $this->l('Taksit')
                            ),
                        )
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('3D Secure Etkinleştirme'),
                        'name' => 'MOKA_THREED_AKTIF',
                        'values' => array(
                            array(
                                'value' => 'aktif',
                                'label' => $this->l('3D Aktif')
                            ),
                            array(
                                'value' => 'close',
                                'label' => $this->l('3D Kapalı')
                            ),
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues() {

        return array(
            'MOKA_BAYI_KODU' => Tools::getValue('MOKA_BAYI_KODU', Configuration::get('MOKA_BAYI_KODU')),
            'MOKA_KULLANICI_ADI' => Tools::getValue('MOKA_KULLANICI_ADI', Configuration::get('MOKA_KULLANICI_ADI')),
            'MOKA_SYSTEM_PASS' => Tools::getValue('MOKA_SYSTEM_PASS', Configuration::get('MOKA_SYSTEM_PASS')),
            'MOKA_TAKSIT_AKTIF' => Tools::getValue('MOKA_TAKSIT_AKTIF', Configuration::get('MOKA_TAKSIT_AKTIF')),
            'MOKA_THREED_AKTIF' => Tools::getValue('MOKA_THREED_AKTIF', Configuration::get('MOKA_THREED_AKTIF')),
			'MOKA_MAX_TAKSIT' => Tools::getValue('MOKA_MAX_TAKSIT', Configuration::get('MOKA_MAX_TAKSIT')),
        );
    }

    function getRecordById($id_order) {


        $order = New Order((int) $id_order);

        $moka_order_id = 'prestashop-1-6-' . $order->id_cart;

        $url = 'https://service.testmoka.com/PaymentDealer/GetDealerPaymentTrxDetailList';
        $dealercode = Configuration::get('MOKA_BAYI_KODU');
        $username = Configuration::get('MOKA_KULLANICI_ADI');
        $password = Configuration::get('MOKA_SYSTEM_PASS');

        $moka['PaymentDealerAuthentication'] = array(
            'DealerCode' => $dealercode,
            'Username' => $username,
            'Password' => $password,
            'CheckKey' => hash('sha256', $dealercode . 'MK' . $username . 'PD' . $password)
        );
        $moka['PaymentDealerRequest'] = array(
            'DealerPaymentId' => null,
            'OtherTrxCode' => $moka_order_id
        );
        return json_decode($this->curlPostExt(json_encode($moka), $url, true));
    }

    public function hookDisplayAdminOrder($params) {

        $id_order = Tools::getValue('id_order');
        if (!$record = $this->getRecordById($id_order))
            return;
        $this->smarty->assign('record', $record);

        return $this->display(__FILE__, 'admin_order_detail.tpl');
    }

    private function curlPostExt($data, $url, $json = false) {
        $ch = curl_init(); // initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        if ($json)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
        curl_setopt($ch, CURLOPT_POST, 1); // set POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
        if ($result = curl_exec($ch)) { // run the whole process
            curl_close($ch);
            return $result;
        }
        return false;
    }

}
