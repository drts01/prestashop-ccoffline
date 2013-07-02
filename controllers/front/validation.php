<?php
/**
 * @since 1.5.0
 */
class CCOfflineValidationModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		/* Gather submitted payment card details send them for cleaning and validation*/
		$cardType           = $this->validatePost($_POST['cardType']);
		$cardholderName     = $this->validatePost($_POST['cardholderName']);
		$cardNumber         = $this->validatePost($_POST['cardNumber']);
		$cardCVC            = $this->validatePost($_POST['cardCVC']);
		$cardexpDate_mo     = $this->validatePost($_POST['expDate_Month']);
		$cardexpDate_yr     = $this->validatePost($_POST['expDate_Year']);
		$cardExp             = $cardexpDate_mo.$cardexpDate_yr;
		$cardstartDate_mo     = $this->validatePost($_POST['startDate_Month']);
		$cardstartDate_yr     = $this->validatePost($_POST['startDate_Year']);
		$cardStart            = $cardstartDate_mo.$cardstartDate_yr;
		$cardIssue         = $this->validatePost($_POST['cardIssue']);

		$cart = $this->context->cart;

		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'ccoffline')
			{
				$authorized = true;
				break;
			}

		if (!$authorized)
			die($this->module->l('This payment method is not available.', 'validation'));

		$customer = new Customer($cart->id_customer);

		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);

		//$ccpayment = new ccpayment();

		//if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$ccpayment->active)
		//	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

		//$customer = new Customer((int)$cart->id_customer);

		//$mailVars =	array(
		//	'{cheque_name}' => Configuration::get('CHEQUE_NAME'),
		//	'{cheque_address}' => Configuration::get('CHEQUE_ADDRESS'),
		//	'{cheque_address_html}' => str_replace("\n", '<br />', Configuration::get('CHEQUE_ADDRESS')));
		
		//from OG validate.php
		//$ccpayment->validateOrder((int)($cart->id), _PS_OS_PREPARATION_, $total, $ccpayment->displayName, NULL, array(), (int)($currency->id), false,$customer->secure_key);
		$this->module->validateOrder((int)$cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);

		//$order = new Order($ccpayment->currentOrder);
		//echo $order->id;
		$this->module->writePaymentcarddetails($this->module->currentOrder, $cardType, $cardholderName, $cardNumber, $cardCVC, $cardExp, $cardStart, $cardIssue);

		//from OG validate.php
		//Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$ccpayment->id.'&id_order='.$ccpayment->currentOrder.'&key='.$order->secure_key);
		Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
	}

	public function validatePost($text)
	{
		$text = trim($text);
		$text = strip_tags($text);
		$text = htmlspecialchars($text, ENT_QUOTES);
		return ($text); //output clean text
	}	
}
