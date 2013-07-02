
<script language="javascript">
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

<fieldset style="width:400px;margin-top:40px;">
	<legend onclick="javascript:showdiv('infoCC');showdiv('headHide');hidediv('headShow')" style="cursor: pointer;display:block;" id="headShow">
		<img src="{$this_path}logo.gif"/> {l s='Purchase Information' mod='ccvalidation'}
	</legend>
    <legend onclick="javascript:hidediv('infoCC');hidediv('headHide');showdiv('headShow')" style="cursor: pointer;display: none;" id="headHide">
    	<img src="{$this_path}logo.gif"/> {l s='Purchase Information' mod='ccvalidation'}
    </legend>
	<div id="infoCC" style="display:none;">
	<table>
		<tr><td>Card Type:</td> <td>{$cardtype}</td></tr>
		<tr><td>Card Holder Name:</td> <td>{$cardHoldername}</td></tr>
		<tr><td>Card Number:</td> <td>{$cardNumber}</td></tr>
		<tr><td>Card CVC Number:</td> <td>{$cardCVC}</td></tr>
		<tr><td>Card Expires (mm-yyyy):</td> <td>{$cardExp|replace:'20':'-20'}</td></tr>
		{* <tr><td>Card Start (mm-yyyy):</td> <td>{$cardStart|replace:'20':'-20'}</td></tr>
		<tr><td>Card Issue:</td> <td>{$cardIssue}</td></tr> *}
	</table>
	</div>
</fieldset>
