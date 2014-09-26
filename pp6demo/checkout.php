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
<?php $EWPP_PAGE_ID = "checkout"; // Page ID ?>
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
P.pageID["checkout"] = true;
</script>
<!-- Note: DO NOT CHANGE THE ID OR CLASS AND DO NOT REMOVE THE COMMENTS! -->
<div id="ewCheckout" class="ewClientTemplate">
<h4 class="ewTitle">{{html P.Phrase("Checkout")}}</h4>
<p><span>{{html P.Phrase("ItemsInCart")}}</span></p>
<form id="ewCheckoutForm" name="ewCheckoutForm" role="form">
	{{if items.length}}
	<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>{{html P.Phrase("DescItemNumber")}}</th>
				<th>{{html P.Phrase("DescItemName")}}</th>
				<th>{{html P.Phrase("DescOption")}}</th>
				<th>{{html P.Phrase("DescPrice")}}</th>
				<th>{{html P.Phrase("DescQuantity")}}</th>
				<th>{{html P.Phrase("DescDiscount")}}</th>
				<th>{{html P.Phrase("DescAmount")}}</th>
				<th>{{html P.Phrase("DescRemove")}}</th>
			</tr>
		</thead>
		<tbody>
			<!-- {{each items}} -->
			<tr>
				<td>${$value.itemnumber}</td>
				<td>${$value.itemname}</td>
				<td>${$value.option}</td>
				<td class="ewNowrap">${$value.price}</td>
				<td>${$value.quantity}</td>
				<td>-${$value.discount}</td>
				<td class="ewNowrap">${$value.subtotal}{{html $value.hidden}}</td>	
				<td><button class="ewRemove btn btn-default" data-index="${ $value.index}"><span class="glyphicon glyphicon-remove"></span></button></td>
			</tr>
			<!-- {{/each}} -->
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6"><b>{{html P.Phrase("DescTotal2")}}</b></td>
				<td class="ewNowrap"><b>${total}</b>{{html hidden}}</td>
				<td></td>
			</tr>
		</tfoot>
	</table>
	</div>
	<br>
	<button type="button" class="btn btn-primary" name="btnContinueCheckout" id="btnContinueCheckout" value="${ P.Phrase('ContinueToCheckOut')}">${ P.Phrase('ContinueToCheckOut')}</button>
	{{else}}
	<div class="alert alert-info col-sm-4 col-xs-12">{{html P.Phrase("CartEmptyMessage")}}</div>
	{{/if}}
</form>
<br>
</div>
	</div>
</div>		
<?php

// Close connection
$conn->Close();
?>
<?php include "ppfooter.php" ?>
