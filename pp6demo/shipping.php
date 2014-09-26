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
<?php $EWPP_PAGE_ID = "shipping"; // Page ID ?>
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
<script language="JavaScript" type="text/javascript">
P.pageID["shipping"] = true;

//
// Check Shipping Information
//
function CheckShipping(f) {

	var P = PAYPALSHOPMAKER;
	var ppad = f.elements["ppad"];
	ppad = (ppad && ppad.checked);
	var fname = f.elements[P.fldFirstName];
	var lname = f.elements[P.fldLastName];
	var address1 = f.elements[P.fldAddress1];
	var city = f.elements[P.fldCity];
	var state = f.elements[P.fldState];
	var zip = f.elements[P.fldZip];
	var country = f.elements[P.fldCountry];
	var email = f.elements[P.fldEmail];
	var custom = f.elements[P.fldCustom];
	var phone = f.elements[P.fldPhone];

	// Check email format
	function CheckEmail(elementValue) {

		var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
		return emailPattern.test(elementValue);
	}

	// Check phone number
	function CheckPhone(elementValue) {

		var p = new RegExp(P.fldPhoneRegExp);
		return p.test(elementValue);
	}	

	// Validate
	if (!ppad && fname && !fname.disabled) {
		if (fname.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("FirstName"));
			P.FocusOption(fname);
			return false;
		}
	}
	if (!ppad && lname && !lname.disabled) {
		if (lname.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("LastName"));
			P.FocusOption(lname);
			return false;
		}
	}
	if (!ppad && address1 && !address1.disabled) {
		if (address1.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("Address1"));
			P.FocusOption(address1);
			return false;
		}
	}
	if (!ppad && phone && !phone.disabled) { // 3.2
	 	if (P.fldPhoneRequired && phone.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("Phone"));
			P.FocusOption(phone);
			return false;
		}		
		if (P.fldPhoneCheck && !CheckPhone(phone.value)) {
			alert(P.Phrase("InvalidMessage") + ' ' + P.Phrase("Phone"));
			P.FocusOption(phone);			
			return false;
		}
	}	
	if (!ppad && city && !city.disabled) {
		if (city.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("City"));
			P.FocusOption(city);
			return false;
		}
	}
	if (!ppad && zip && !zip.disabled) {
		if (zip.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("Zip"));
			P.FocusOption(zip);
			return false;
		}
	}
	if (country && !country.disabled) {
		if ((country.type == "select-one" && country.selectedIndex <= 0) ||
			(country.type != "select-one" && country.value == "")) { // 3.1
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("Country"));
			P.FocusOption(country);
			return false;
		}		
	}
	if (state && !state.disabled) {
		if (P.fldStateCheck) {
			if ((state.type == "select-one" && state.selectedIndex <= 0) ||
				(state.type != "select-one" && state.value == "")) { // 3.2
				alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("State"));
				P.FocusOption(state);
				return false;
			}
		}
	}
	if (email) {
		if (email.value == "") {
			alert(P.Phrase("RequiredMessage") + ' ' + P.Phrase("Email"));
			P.FocusOption(email);
			return false;
		} else if (!CheckEmail(email.value)) {
			alert(P.Phrase("InvalidMessage") + ' ' + P.Phrase("Email"));
			P.FocusOption(email);
			return false;
		}
	}
	if (custom) {
		if (custom.value.length > 255) {
			alert(P.Phrase("InvalidMessage") + ' ' + P.Phrase("CustomCaption"));
			P.FocusOption(custom);
			return false;
		}
	}
	return true;
}
</script>
<!-- Note: DO NOT CHANGE THE ID OR CLASS AND DO NOT REMOVE THE COMMENTS! -->
<div id="ewShip" class="ewClientTemplate">
<h4 class="ewTitle">{{html P.Phrase("ShippingDetails")}}</h4> 
<!--Shopping Cart Checkout Begin-->
	{{if P.dsShopCartItems.length}}	
	<form id="ewShipForm" name="ewShipForm" role="form">
		{{if P.USE_PAYPAL}}{{html usePayPalStoredShippingAddress}}{{/if}}
			<div class="ewShipAddress form-group">
				<label for="firstname">{{html P.Phrase("FirstName")}}</label>
				<div class="row">
					<div class="col-sm-6">
					<input type="text" name="first_name" class="form-control" id="firstname" placeholder="firstname">
					</div>
				</div>
			</div>
			<div class="ewShipAddress form-group">
				<label for="lastname">{{html P.Phrase("LastName")}}</label>
				<div class="row">
					<div class="col-sm-6">
					<input type="text" name="last_name" class="form-control" id="lastname" placeholder="lastname">
					</div>
				</div>
			</div>	
			<div class="ewShipAddress form-group">
				<label for="address1" class="control-label">{{html P.Phrase("Address1")}}</label>
				<div class="row">
					<div class="col-sm-8">
					<input type="text" name="address1" class="form-control" id="address1" placeholder="address1">
					</div>
				</div>
			</div>	
			<div class="ewShipAddress form-group">
				<label for="address2" class="control-label">{{html P.Phrase("Address2")}}</label>
				<div class="row">
					<div class="col-sm-8">
					<input type="text" name="address2" class="form-control" id="address2" placeholder="address2">
					</div>
				</div>
			</div>	
			<div class="ewShipAddress form-group">
				<label for="night_phone" class="control-label">{{html P.Phrase("Phone")}}</label>
				<div class="row">
					<div class="col-sm-6">
					<input type="text" name="night_phone" class="form-control" id="phone" placeholder="night_phone">
					</div>
				</div>
			</div>	
			<div class="ewShipAddress form-group">
				<label for="city" class="control-label">{{html P.Phrase("City")}}</label>
				<div class="row">
					<div class="col-sm-6">
					<input type="text" name="city" class="form-control" id="city" placeholder="city">
					</div>
				</div>
			</div>	
			<div class="ewShipAddress form-group">
				<label for="zip" class="control-label">{{html P.Phrase("Zip")}}</label>
				<div class="row">
					<div class="col-sm-6">
					<input type="text" name="zip" class="form-control" id="zip" placeholder="zip">
					</div>
				</div>
			</div>	
			<div class="form-group">
				<label for="email" class="control-label">{{html P.Phrase("Email")}}</label>
				<div class="row">
					<div class="col-sm-8">
					<input type="text" name="email" class="form-control" id="email" placeholder="email">
					</div>
				</div>
			</div>	
				<!-- {{if country}} -->
			<div class="form-group">
				<label for="country" class="control-label">{{html P.Phrase("Country")}}</label>
					{{html country}}
			</div>	
				<!-- {{/if}} -->
				<!-- {{if state}} -->
			<div class="form-group">
				<label for="state" class="control-label">{{html P.Phrase("State")}}</label>
					{{html state}}
			</div>	
				<!-- {{/if}} -->
				<!-- {{if shipmethod}} -->
			<div class="form-group">
				<label for="shipmethod" class="control-label">{{html P.Phrase("ShippingMethod")}}</label>
					{{html shipmethod}}
			</div>	
				<!-- {{/if}} -->				
				<!-- {{if P.CUSTOM_AS_TEXTAREA}} -->
			<div class="form-group">
				<label for="shipmethod" class="control-label">{{html P.Phrase("CustomCaption")}}</label>
				<div class="row">
					<div class="col-sm-10">
					<textarea cols="40" rows="4" class="form-control" name="custom">${custom}</textarea><br>
					<input readonly="readonly" type="text" id="cntcustom" name="cntcustom" size="3" maxlength="3" value="${ cntcustom}" placeholder="cntcustom">&nbsp;{{html P.Phrase("TextAreaCntMessage")}}
					</div>
				</div>
			</div>	
				<!-- {{/if}} -->				
				<!-- {{if P.DISCOUNT_CODE}} -->
			<div class="form-group">
				<label for="discountcode" class="control-label">{{html P.Phrase("DiscountCode")}}</label>
				<div class="row">
					<div class="col-md-5 col-sm-6">
					<input type="text" name="discountcode" class="form-control" id="discountcode" placeholder="discountcode">
					</div>
				</div>
			</div>	
				<!-- {{/if}} -->
		{{html hidden}}
		<div class="form-group">
			<button type="button" class="btn btn-primary" name="btnContinue" id="btnContinue" value="${ P.Phrase('Continue')}">${ P.Phrase('Continue')}</button>
		</div>
	</form>
	{{else}}
	<div class="alert alert-info col-sm-4 col-xs-12">{{html P.Phrase("CartEmptyMessage")}}</div>
	{{/if}}
</div>
	</div>
</div>		
<?php

// Close connection
$conn->Close();
?>
<?php include "ppfooter.php" ?>
