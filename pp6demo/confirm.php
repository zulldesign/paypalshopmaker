<?php include "ppcfg.php" ?>
<?php include "ppfn.php" ?>
<?php
session_start(); // Initialize session data
ob_start(); // Turn on output buffering

// Output HTTP header
ewpp_Header();

// Include ADODB
include_once 'adodb5/adodb.inc.php';
?>
<?php $EWPP_PAGE_ID = "confirm"; // Page ID ?>
<?php

// Open connection to the database
$conn = ewpp_Connect();
?>
<?php

// Check application status
if (!ewpp_ApplicationEnabled()) {
	echo $PPLanguage->Phrase("ApplicationStopped");
	exit();
}
?>
<?php include "ppheader.php" ?>
<script type="text/javascript">
P.pageID["confirm"] = true;
</script>
<!-- Note: DO NOT CHANGE THE ID  AND DO NOT REMOVE THE COMMENTS! -->
<div id="ewConfirm" class="ewClientTemplate">
<h4 class="ewTitle">{{html P.Phrase("ConfirmOrder")}}</h4>
<br>
<!--Shopping Cart Checkout Begin-->
<form action="${ P.PAYPAL_URL}" method="post" id="ewConfirmForm" name="ewConfirmForm" class="ewForm">
<h5><b>{{html P.Phrase("OrderDetails")}}</b></h5>
<!-- shopping cart -->
{{if items.length}}
	<div class="table-responsive">
	<table id="ewOrderDetails" class="table table-bordered">
		<thead>
			<tr>
				<th>{{html P.Phrase("DescItemNumber")}}</th>
				<th>{{html P.Phrase("DescItemName")}}</th>
				<th>{{html P.Phrase("DescOption")}}</th>
				<th>{{html P.Phrase("DescPrice")}}</th>
				<th>{{html P.Phrase("DescQuantity")}}</th>
				<th>{{html P.Phrase("DescDiscount")}}</th>
				<th>{{html P.Phrase("DescAmount")}}</th>				
			</tr>
		</thead>
		<tbody>
			<!-- {{each items}} -->
			<tr>
				<td>${$value.itemnumber}</td>
				<td>${$value.itemname}</td>
				<td>${$value.option}</td>
				<td class="Nowrap">${$value.price}</td>
				<td>${$value.quantity}</td>
				<td>-${$value.discount}</td>
				<td class="Nowrap">${$value.subtotal}{{html $value.hidden}}</td>				
			</tr>
			<!-- {{/each}} -->
		</tbody>
		<tfoot>
			<tr class="ewTable1Summary">
				<td class="text-right" colspan="6">{{html P.Phrase("DescSubtotal")}}</td>
				<td class="Nowrap">${total}</td>
			</tr>
			<!-- {{if nDiscount}} -->
			<tr>
				<td class="text-right" colspan="6">{{html P.Phrase("Discount")}} ({{html P.Phrase("DiscountCode")}})</td>
				<td class="Nowrap">-${discount}</td>		
			</tr>
			<!-- {{/if}} -->
			<!-- {{if nShipping}} -->
			<tr>
				<td class="text-right" colspan="6">{{html P.Phrase("DescShipping")}}</td>
				<td class="Nowrap">${shipping}</td>				
			</tr>
			<!-- {{/if}} -->
			<!-- {{if nHandling}} -->
			<tr>
				<td class="text-right" colspan="6">{{html P.Phrase("DescHandling")}}</td>
				<td class="Nowrap">${handling}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if nTax}} -->
			<tr>
				<td class="text-right" colspan="6">{{html P.Phrase("DescTax")}}</td>
				<td class="Nowrap">${tax}</td>	
			</tr>
			<!-- {{/if}} -->
			<tr class="ewTable1Summary">
				<td class="text-right" colspan="6"><b>{{html P.Phrase("DescTotal")}}</b></td>				
				<td class="Nowrap"><b>${net}</b></td>
			</tr>
		</tfoot>
	</table>
	</div>	
	{{html hidden}}
	<br>
	{{each shipview}}
	<!-- shipping details -->
	<h5><b>{{html P.Phrase("ShippingDetails")}}</b></h5>
	{{if P.USE_PAYPAL}}{{html $value.usePayPalStoredShippingAddress}}{{/if}}
	<div class="table-responsive">
	<table id="ewShippingDetails" class="table table-bordered">
		<tbody>
			<!-- {{if $value.firstname}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("FirstName")}}</td>
				<td>${$value.firstname}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.lastname}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("LastName")}}</td>
				<td>${$value.lastname}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.address1}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("Address1")}}</td>
				<td>${$value.address1}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.address2}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("Address2")}}</td>
				<td>${$value.address2}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.phone}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("Phone")}}</td>
				<td>${$value.phone}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.city}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("City")}}</td>
				<td>${$value.city}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.zip}} -->
			<tr class="ewShipAddress">
				<td class="ewTable2Header">{{html P.Phrase("Zip")}}</td>
				<td>${$value.zip}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.email}} -->
			<tr>
				<td class="ewTable2Header">{{html P.Phrase("Email")}}</td>
				<td>${$value.email}</td>
			</tr>
			<!-- {{/if}} -->
	<!--
	Note: Country and state are required by PayPal. If you remove the country (or state) row, make sure you add a hidden element named "country" (or "state") as default values. See Appendix C in PayPal's Website Payments Standard Integration Guide for allowable country codes. State (for U.S. only) must be two-character official U.S. abbreviation. e.g.
	<input type="hidden" name="country" value="GB">
	<input type="hidden" name="state" value="">
	-->
			<!-- {{if $value.country}} -->
			<tr>
				<td class="ewTable2Header">{{html P.Phrase("Country")}}</td>
				<td>${$value.country}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.state}} -->
			<tr>
				<td class="ewTable2Header">{{html P.Phrase("State")}}</td>
				<td>${$value.state}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.shipmethod}} -->
			<tr>
				<td class="ewTable2Header">{{html P.Phrase("ShippingMethod")}}</td>
				<td>${$value.shipmethod}</td>
			</tr>
			<!-- {{/if}} -->
			<!-- {{if $value.custom}} -->		
			<tr>
				<td class="ewTable2Header">{{html P.Phrase("CustomCaption")}}</td>
				<td>{{html $value.custom}}</td>
			</tr>
			<!-- {{/if}} -->
		</tbody>
	</table>
	</div>
	{{html $value.hidden}}
	{{/each}}
{{else}}
	<div class="alert alert-info col-sm-4 col-xs-12">{{html P.Phrase("CartEmptyMessage")}}</div>
{{/if}}
<br>
<input type="hidden" name="cmd" value="_cart">
<input type="hidden" name="upload" value="1">
<input type="hidden" name="business" value="${ P.BUSINESS}">
{{if P.PROJECT_CHARSET}}
<input type="hidden" name="charset" value="${ P.PROJECT_CHARSET}">
{{/if}}
{{if P.CURRENCY_CODE}}
<input type="hidden" name="currency_code" value="${ P.CURRENCY_CODE}">
{{/if}}
<input type="hidden" name="cs" value="0">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="no_shipping" value="0">
<div class="form-group">
<button type="submit" class="btn btn-primary" name="btnClickToBuy" id="btnClickToBuy" value="${ P.Phrase('ClickToBuy')}">${ P.Phrase('ClickToBuy')}</button>
</div>
</form>
</div>
<!--Shopping Cart Checkout End  -->
<?php

// Close connection
$conn->Close();
?>
<?php include "ppfooter.php" ?>
