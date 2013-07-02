<h3>{l s='Your is Order Complete!' mod='ccoffline'}</h3>
<h3>{l s='Thank you for Your Order' mod='ccoffline'}</h3>
<p>{l s='Your order on' mod='ccoffline'} <span class="bold">{$shop_name}</span> {l s='is now complete.' mod='offlinecardpayment'}

<br /><br />
<p>{l s='Your Order Number Is: ' mod='ccoffline'}<span class="bold">{$reference}</span> </p>
<p>{l s='Your order will be processed and shipped as soon as possible' mod='ccoffline'}</p>
<p>{l s='Total Payment Pending: ' mod='ccoffline'} <span class="price">{$total_to_pay}</span></p>
<p>{l s='For any questions or for further information, please contact our' mod='ccoffline'} <a href="{$base_dir}contact-form.php">{l s='customer support' mod='offlinecardpayment'}</a>.</p>
</p>
