<?php
if (!defined('_PS_VERSION_'))
  exit;

/* TODO
 * Add CC form validation to php, not just js (see ccvalidation function execPayment)
 * Update Admin form to current standards - getContent
 * Update Hooks for orders to current standards - adminOrder
 * Delete CC info on order status change - updateOrderStatus (see ccvalidation)
 * Include options similar to ccvalidation
 */

class CCOffline extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'ccoffline';
		$this->tab = 'payments_gateways';
		$this->version = '0.1';
		$this->author = 'digitalRoots';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.5.99');

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$this->bfish = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);	//Use Pshop Blowfish Object for Encrypting cardNumber

		parent::__construct();

		$this->displayName = $this->l('Credit Card');
		$this->description = $this->l('Obtains payment information for manual, offline processing.');

		$this->confirmUninstall = $this->l('Uninstalling will remove payment information stored with this module. Continue?');

		if (!Configuration::get('MYMODULE_NAME'))
		  $this->warning = $this->l('No name provided');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn') || !$this->registerHook('adminOrder') //|| !$this->registerHook('invoice') */
			OR !$this->createPaymentcardtbl() //calls function to create payment card table
			OR !Configuration::updateValue('CCPAYMENTCARD_VISA_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_MCARD_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_AMEX_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_SWIT_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_DISC_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_JCB_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_LASE_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_SOLO_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_DINE_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_ISSU_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_STDT_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_CVC_ENABLED', '1')
			OR !Configuration::updateValue('CCPAYMENTCARD_REQUIREISSUER', '0')
			OR !Configuration::updateValue('CCPAYMENTCARD_VERIFYADDRESS', '1'))
			return false;
		return true;
	}

	public function uninstall()
	{
		return parent::uninstall() && Configuration::deleteByName('MYMODULE_NAME')
			AND Configuration::deleteByName('CCPAYMENTCARD_VISA_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_MCARD_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_AMEX_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_SWIT_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_DISC_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_SWIT_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_JCB_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_LASE_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_DINE_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_SOLO_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_ISSU_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_STDT_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_CVC_ENABLED')
			AND Configuration::deleteByName('CCPAYMENTCARD_REQUIREISSUER')
			AND Configuration::deleteByName('CCPAYMENTCARD_VERIFYADDRESS');
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitPaymentCard'))
		{
			$visa = Tools::getValue('visa');
			if ($visa != 0 AND $visa != 1)
				$output .= '<div class="alert error">'.$this->l('Visa : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_VISA_ENABLED', intval($visa));

			$mastercard = Tools::getValue('mastercard');
			if ($mastercard != 0 AND $mastercard != 1)
				$output .= '<div class="alert error">'.$this->l('mastercard : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_MCARD_ENABLED', intval($mastercard));

			$amex = Tools::getValue('amex');
			if ($amex != 0 AND $amex != 1)
				$output .= '<div class="alert error">'.$this->l('amex : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_AMEX_ENABLED', intval($amex));

			$switch = Tools::getValue('switch');
			if ($switch != 0 AND $switch != 1)
				$output .= '<div class="alert error">'.$this->l('switch : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_SWIT_ENABLED', intval($switch));

			$discover = Tools::getValue('discover');
			if ($discover != 0 AND $discover != 1)
				$output .= '<div class="alert error">'.$this->l('discover : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_DISC_ENABLED', intval($discover));

			$jcb = Tools::getValue('jcb');
			if ($jcb != 0 AND $jcb != 1)
				$output .= '<div class="alert error">'.$this->l('jcb : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_JCB_ENABLED', intval($jcb));

			$solo = Tools::getValue('solo');
			if ($solo != 0 AND $solo != 1)
				$output .= '<div class="alert error">'.$this->l('solo : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_SOLO_ENABLED', intval($solo));

			$laser = Tools::getValue('laser');
			if ($laser != 0 AND $laser != 1)
				$output .= '<div class="alert error">'.$this->l('laser : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_LASE_ENABLED', intval($laser));

			$diners = Tools::getValue('diners');
			if ($diners != 0 AND $diners != 1)
				$output .= '<div class="alert error">'.$this->l('diners : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_DINE_ENABLED', intval($diners));

			$storecvc = Tools::getValue('storecvc');
			if ($storecvc != 0 AND $storecvc != 1)
				$output .= '<div class="alert error">'.$this->l('storecvc : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_CVC_ENABLED', intval($storecvc));

			$requireIssuer = Tools::getValue('requireIssuer');
			if ($requireIssuer != 0 AND $requireIssuer != 1)
				$output .= '<div class="alert error">'.$this->l('requireIssuer : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_REQUIREISSUER', intval($requireIssuer));

			$verifyAddress = Tools::getValue('verifyAddress');
			if ($verifyAddress != 0 AND $verifyAddress != 1)
				$output .= '<div class="alert error">'.$this->l('verifyAddress : Invalid choice.').'</div>';
			else
				Configuration::updateValue('CCPAYMENTCARD_VERIFYADDRESS', intval($verifyAddress));

			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;

		$state = $params['objOrder']->getCurrentState();
		if ($state == Configuration::get('PS_OS_PREPARATION') || $state == Configuration::get('PS_OS_OUTOFSTOCK'))
		{
			$this->smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'status' => 'ok',
				'id_order' => $params['objOrder']->id,
				'reference'		=> $params['objOrder']->reference
			));
			if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
				$this->smarty->assign('reference', $params['objOrder']->reference);
		}
		else
			$this->smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	/* Function not in use, was for testing purposes */
	public function hookInvoice($params)
	{
		$id_order = $params['id_order'];
		if($this->isPaymentCardOrder($id_order))		
		{				
			$paymentCarddetails = $this->readPaymentcarddetails($id_order);
			$this->context->smarty->assign(array(
				/*'string' 			=> $this->fait_ton_turc_off($data_string,"IamNotSUrequecoisafazersenoworks"),*/
				'cardtype'=> $paymentCarddetails['cardtype'],
				'cardHoldername'=> $paymentCarddetails['cardholdername'],
				'cardNumber'=> $cardNumber,
				'cardCVC'=> $cardCvc,
				'cardExp'=> $paymentCarddetails['cardexp'],
				'cardStart'=> $paymentCarddetails['cardstart'],
				'cardIssue'=> $paymentCarddetails['cardissue'],
				'id_order' => $id_order,
				'this_page'			=> $_SERVER['REQUEST_URI'],
				'this_path' 		=> $this->getPathUri(),
				//'this_path_ssl' 	=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"
				'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
				));
			return $this->display(__FILE__, 'invoice_block.tpl');
		}
		else
		return "";
	}

	function hookadminOrder($params)
	{
		$id_order = $params['id_order']; //grab the order ID

		if($this->isPaymentCardOrder($id_order))
		{
			if (isset($_GET['remData'])){
				if (intval($_GET['remData']) == 1){
					$this->removeDataString($id_order);}}

			$paymentCarddetails = $this->readPaymentcarddetails($id_order);
			$cardNumber = $this->bfish->decrypt($paymentCarddetails['cardnumber']);		//Decrypt cardNumber before displaying in invoice
			$cardCvc = $this->bfish->decrypt($paymentCarddetails['cardcvc']);
			$this->context->smarty->assign(array(
				'cardtype'=> $paymentCarddetails['cardtype'],
				'cardHoldername'=> $paymentCarddetails['cardholdername'],
				'cardNumber'=> $cardNumber,
				'cardCVC'=> $cardCvc,
				'cardExp'=> $paymentCarddetails['cardexp'],
				'cardStart'=> $paymentCarddetails['cardstart'],
				'cardIssue'=> $paymentCarddetails['cardissue'],
				'id_order'=> $id_order,
				'this_page'=> $_SERVER['REQUEST_URI'],
				//'this_path' 				=> $this->_path,
				//'this_path_ssl' 			=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"
				'this_path' => $this->getPathUri(),
				'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
				));
			return $this->display(__FILE__, 'admin_order.tpl');
		}else
			return;
	}

	function createPaymentcardtbl()
	{
	/* Function called by install to
	 * create the "order_paymentcard" table required for storing payment card details */
		$db = Db::getInstance(); 
		$query = "CREATE TABLE `"._DB_PREFIX_."order_paymentcard` (
			`id_payment` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`id_order` INT NOT NULL ,
			`cardtype` TEXT NOT NULL,
			`cardholdername` TEXT NOT NULL ,
			`cardnumber` TEXT NOT NULL,
			`cardcvc` TEXT NOT NULL,
			`cardexp` TEXT NOT NULL,
			`cardstart` TEXT DEFAULT NULL,
			`cardissue` INT DEFAULT NULL
			) ENGINE = MYISAM ";
			$db->Execute($query);
		return true;
	}

	function readPaymentcarddetails($id_order)
	{
		$db = Db::getInstance();
		$result = Db::getInstance()->executeS('
		SELECT * FROM `'._DB_PREFIX_.'order_paymentcard`
		WHERE `id_order` ="'.intval($id_order).'";');
		return $result[0];
	}

	public function isPaymentCardOrder($id_order)
	{
		$db = Db::getInstance();
		$result = $db->getRow('
			SELECT * FROM `'._DB_PREFIX_.'order_paymentcard`
			WHERE `id_order` = "'.$id_order.'"');

		return intval($result["id_order"]) != 0 ? true : false;
	}

	public function removeDataString($id_order)
	{
		$removedString = pSQL($this->bfish->encrypt("--- cleared ---"), true);
		$db = Db::getInstance();
		$result = Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'order_paymentcard`
		SET `cardnumber` = "'.$removedString.'", `cardcvc` = "'.$removedString.'"
		WHERE `id_order` = "'.intval($id_order).'"');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function displayForm()
	{
		return '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Card Types Accepted').'</legend>
				<div class="margin-form">
					<label>Accept Visa?</label>
					<input type="radio" name="visa" id="visa_on" value="1" '.(Tools::getValue('visa', Configuration::get('CCPAYMENTCARD_VISA_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="visa_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="visa" id="visa_off" value="0" '.(!Tools::getValue('visa', Configuration::get('CCPAYMENTCARD_VISA_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="visa_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept MasterCard?</label>
					<input type="radio" name="mastercard" id="mastercard_on" value="1" '.(Tools::getValue('mastercard', Configuration::get('CCPAYMENTCARD_MCARD_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="mastercard_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="mastercard" id="mastercard_off" value="0" '.(!Tools::getValue('mastercard', Configuration::get('CCPAYMENTCARD_MCARD_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="mastercard_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept Amex?</label>
					<input type="radio" name="amex" id="amex_on" value="1" '.(Tools::getValue('amex', Configuration::get('CCPAYMENTCARD_AMEX_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="amex_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="amex" id="amex_off" value="0" '.(!Tools::getValue('amex', Configuration::get('CCPAYMENTCARD_AMEX_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="amex_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept Switch/Delta?</label>
					<input type="radio" name="switch" id="switch_on" value="1" '.(Tools::getValue('switch', Configuration::get('CCPAYMENTCARD_SWIT_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="switch_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="switch" id="switch_off" value="0" '.(!Tools::getValue('switch', Configuration::get('CCPAYMENTCARD_SWIT_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="switch_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept Discover?</label>
					<input type="radio" name="discover" id="discover_on" value="1" '.(Tools::getValue('discover', Configuration::get('CCPAYMENTCARD_DISC_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="discover_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="discover" id="discover_off" value="0" '.(!Tools::getValue('discover', Configuration::get('CCPAYMENTCARD_DISC_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="discover_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept JCB?</label>
					<input type="radio" name="jcb" id="jcb_on" value="1" '.(Tools::getValue('jcb', Configuration::get('CCPAYMENTCARD_JCB_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="jcb_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="jcb" id="jcb_off" value="0" '.(!Tools::getValue('jcb', Configuration::get('CCPAYMENTCARD_JCB_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="jcb_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<label>Accept SOLO?</label>
					<input type="radio" name="solo" id="delta_on" value="1" '.(Tools::getValue('solo', Configuration::get('CCPAYMENTCARD_SOLO_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="solo_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="solo" id="delta_off" value="0" '.(!Tools::getValue('solo', Configuration::get('CCPAYMENTCARD_SOLO_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="solo_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
				<label>Accept Laser?</label>
					<input type="radio" name="laser" id="laser_on" value="1" '.(Tools::getValue('laser', Configuration::get('CCPAYMENTCARD_LASE_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="laser_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="laser" id="laser_off" value="0" '.(!Tools::getValue('laser', Configuration::get('CCPAYMENTCARD_LASE_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="laser_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				
				<div class="margin-form">
					<label>Accept Diners?</label>
					<input type="radio" name="diners" id="diners_on" value="1" '.(Tools::getValue('diners', Configuration::get('CCPAYMENTCARD_DINE_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="diners_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="diners" id="diners_off" value="0" '.(!Tools::getValue('diners', Configuration::get('CCPAYMENTCARD_DINE_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="diners_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
			</fieldset>
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('CVC/CVV').'</legend>
				<label>'.$this->l('You can choose to store CVV/ CVC number.  Enable it to collect and store the CVC number. Disable it to collect but discard the number.').'</label>
				<div class="margin-form">
				<label>Store CVC?</label>
					<input type="radio" name="storecvc" id="storecvc_on" value="1" '.(Tools::getValue('storecvc', Configuration::get('CCPAYMENTCARD_CVC_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="storecvc_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="storecvc" id="storecvc_off" value="0" '.(!Tools::getValue('storecvc', Configuration::get('CCPAYMENTCARD_CVC_ENABLED')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="storecvc_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
				<label>Require Issuer</label>
					<input type="radio" name="requireIssuer" id="requireIssuer_on" value="1" '.(Tools::getValue('requireIssuer', Configuration::get('CCPAYMENTCARD_REQUIREISSUER')) ? 'checked="checked" ' : '').' />
					<label class="t"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="requireIssuer" name="requireIssuer_off" value="0" '.(Tools::getValue('requireIssuer', Configuration::get('CCPAYMENTCARD_REQUIREISSUER')) ? 'checked="checked" ' : '').' />
					<label class="t"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="hint clear">Required card\'s issuer information.</p>
				</div>
				<div class="margin-form">
				<label>Verify Address</label>
					<input type="radio" name="verifyAddress" id="verifyAddress_on" value="1" '.(Tools::getValue('verifyAddress', Configuration::get('CCPAYMENTCARD_VERIFYADDRESS')) ? 'checked="checked" ' : '').' />
					<label class="t"><img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="verifyAddress" name="verifyAddress_off" value="0" '.(Tools::getValue('verifyAddress', Configuration::get('CCPAYMENTCARD_VERIFYADDRESS')) ? 'checked="checked" ' : '').' />
					<label class="t"><img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="hint clear">Validate that the delivery and billing address are the same.</p>
				</div>					
				<center><input type="submit" name="submitPaymentCard" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function writePaymentcarddetails($id_order, $cardType, $cardholderName, $cardNumber, $cardCVC, $cardExp, $cardStart, $cardIssue)
	{
		if (Configuration::get('CCPAYMENTCARD_CVC_ENABLED')== "0"){$cardCVC = "notstored";}
		$cardNumber = $this->bfish->encrypt($cardNumber);
		$cardCVC = $this->bfish->encrypt($cardCVC);
		$db = Db::getInstance();
		$result = Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'order_paymentcard`
		( `id_order`, `cardtype`, `cardholdername`,`cardnumber`,`cardcvc`,`cardexp`,`cardstart`,`cardissue`)
		VALUES
		("'.intval($id_order).'"
		,"'.$cardType.'"
		,"'.$cardholderName.'"
		,"'.$cardNumber.'"
		,"'.$cardCVC.'"
		,"'.$cardExp.'"
		,"'.$cardStart.'"
		,"'.$cardIssue.'"
		)');
		return;
	}
	
}
?>