{capture name=path}{l s='Credit Card Payment' mod='ccoffline'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summary' mod='ccoffline'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<h3>{l s='Credit Card Payment' mod='ccoffline'}</h3>

{* if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='ccoffline'}</p>
{else *}

<script language="javascript" src="{$this_path}js/validate-form.js"></script>
<link href="{$this_path}css/form.css" rel="stylesheet" type="text/css" media="all" />
<form action="{$link->getModuleLink('ccoffline', 'validation', [], true)}" class="ccofflineForm" id="ccofflineForm" name="ccofflineForm" method="post">
<fieldset>
  <legend>{l s='Payment Card Details' mod='ccoffline'}</legend>
	<p>Please complete the form below. Mandatory fields marked <em>*</em></p>
  <ol>
	<li>
	  <label for="cardType">{l s='Card Type:' mod='ccoffline'}<em>*</em></label>
	  <select name="cardType" id="cardType">{$this_valid_card}</select>  <span style="font-size:0.8em;" class="hotspot" onmouseover="tooltip.show('{l s="Use this drop down to select the type of payment card" mod='ccoffline'}');" onmouseout="tooltip.hide();">{l s="What is this?" mod='ccoffline'}</span>
	</li>
	<li>
	  <label for="cardholderName">{l s='Name on Card:' mod='ccoffline'}<em>*</em></label>
	  <input type="text"  name="cardholderName" id="cardholderName" />  <span style="font-size:0.8em;" class="hotspot" onmouseover="tooltip.show('{l s="The name of the card holder as written on the front of the card" mod='ccoffline'}');" onmouseout="tooltip.hide();">{l s="What is this?" mod='ccoffline'}</span>
	  <div id="errcardholderName" style="color:red;display: none;">{l s="Card Holder Name is Required" mod='ccoffline'}</div>
	</li>
	<li>
	  <label for="cardNumber">{l s='Card Number:' mod='ccoffline'}<em>*</em></label>
	  <input type="text"  name="cardNumber" id="cardNumber" />  <span style="font-size:0.8em;" class="hotspot" onmouseover="tooltip.show('{l s="The card number is the long number embossed on the front of your card'" mod='ccoffline'});" onmouseout="tooltip.hide();">{l s="What is this?" mod='ccoffline'}</span>
	  <div id="errcardNumber" style="color:red;display: none;"></div>
	</li>
	<li>
	  <label for="cardCVC">{l s='CVV/CVC Security Number:' mod='ccoffline'} <em>*</em></label>
	  <input type="text" size="3" name="cardCVC" id="cardCVC"  />  <span style="font-size:0.8em;" class="hotspot" onmouseover="tooltip.show('{l s="CVC/CVV numbers are found on the back of your card. <br><img src=\'cvc.png\' >" mod='ccoffline'}');" onmouseout="tooltip.hide();">{l s="What is this?" mod='ccoffline'}</span>
	   <div id="errcardCVC" style="color:red;display: none;">{l s="Valid CVC is Required" mod='ccoffline'}</div>
	</li>
	<li>
	  <label for="ExpDate_yr">{l s='Expiration Date:' mod='ccoffline'}<em>*</em></label>
	  <div id="errExpDate" style="color:red;display: none;">{l s="Valid Expiration Date is Required" mod='ccoffline'}</div>
	  {html_select_date 
			prefix='expDate_' 
			start_year='-0'
			end_year='+15' 
			display_days=false
			year_empty="Year" 
			month_empty="Month"}
	</li>
</fieldset>
{*
<fieldset>
 <p>You only need to enter a start date if the card has one</p>
	<li>
	  <label for="startDate_yr">{l s='Start Date:' mod='ccoffline'}</label>
		{html_select_date 
		prefix='startDate_' 
		start_year='-0'
		end_year='+15' 
		display_days=false
		year_empty="Year" 
		month_empty="Month"}
	</li>
  <p>You only need to enter an issue number if card has one</p>
       <li> 
	   <label>{l s='Issue Number:' mod='ccoffline'}</label>
	   <input type="text" size="3" name="cardIssue" id="cardIssue" />
	   </li>

  </ol>
</fieldset>
*}

<p class="cart_navigation">
	<a href="{$base_dir_ssl}order.php?step=3" class="button_large">{l s='Other payment methods' mod='ccoffline'}</a>
	<input type="submit" name="paymentSubmit" value="{l s='Submit Order' mod='ccoffline'}" class="exclusive_large" />
</p>
</form>
