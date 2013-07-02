<!-- CCOffline -->
<script>
function showdiv(id) {
	//safe function to show an element with a specified id	  
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'block';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'block';
		}
		else { // IE 4
			document.all.id.style.display = 'block';
		}
	}
}
function hidediv(id) {
	//safe function to hide an element with a specified id
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'none';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'none';
		}
		else { // IE 4
			document.all.id.style.display = 'none';
		}
	}
}
</script>
<br />
<fieldset style="width:auto;margin-top:auto;">
	<legend onclick="javascript:showdiv('ccinfo');showdiv('headHide');hidediv('headShow')" style="cursor: pointer;display:block;" id="headShow">
		<img src="{$this_path}logo.gif"/> {l s='Credit Card Information' mod='ccoffline'}
	</legend>
    <legend onclick="javascript:hidediv('ccinfo');hidediv('headHide');showdiv('headShow')" style="cursor: pointer;display: none;" id="headHide">
    	<img src="{$this_path}logo.gif"/> {l s='Credit Card Information' mod='ccoffline'}
    </legend>
	<div id="ccinfo" style="display:none;">
		<table>
			<tr><td>Card Type:</td> <td>{$cardtype}</td></tr>
			<tr><td>Card Holder Name:</td> <td>{$cardHoldername}</td></tr>
			<tr><td>Card Number:</td> <td>{$cardNumber}</td></tr>
			<tr><td>Card CVC Number:</td> <td>{$cardCVC}</td></tr>
			<tr><td>Card Expires (mm-yyyy):</td> <td>{$cardExp|replace:'20':'-20'}</td></tr>
			{* <tr><td>Card Start (mm-yyyy):</td> <td>{$cardStart|replace:'20':'-20'}</td></tr>
			<tr><td>Card Issue:</td> <td>{$cardIssue}</td></tr> *}
		</table>
		<input type="button" class="button" style="margin-top: .5em;float: right;" onclick="if(confirm('Are You Sure')) window.location = document.location + '&remData=1'" alt="{l s='Remove Creditcard Data' mod='ccoffline'}" value="{l s='Clear Credit Card Data' mod='ccoffline'}" />
	</div>
</fieldset>