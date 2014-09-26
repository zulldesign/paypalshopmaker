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
<?php $EWPP_PAGE_ID = "view"; // Page ID ?>
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
<?php

// Set up current item
ewpp_GetItemId();
if (strval($ewpp_Item["ItemId"]) == "") {
	ob_end_clean();
	header("Location: " . EWPP_CART_LIST_PAGE);
	exit();
}

// Build SQL
$sSql = EWPP_PRODUCT_SELECT_SQL;
$sWhere = str_replace("@@ItemId@@", $ewpp_Item["ItemId"], EWPP_PRODUCT_ITEM_FILTER);
if (EWPP_USE_ITEM_COUNT && EWPP_PRODUCT_ITEMCOUNT_FILTER <> "") {
	if ($sWhere <> "")
		$sWhere .= " AND ";
	$sWhere .= EWPP_PRODUCT_ITEMCOUNT_FILTER;
}
if ($sWhere <> "")
	$sSql .= " WHERE " . $sWhere;

// Open recordset
$rs = $conn->Execute($sSql);

// Load product details
ewpp_LoadProduct($rs);

// Close recordset
if ($rs)
	$rs->Close();
?>
<?php include "ppheader.php" ?>
<form name="pp<?php echo $ewpp_Item["ItemId"] ?>" id="pp<?php echo $ewpp_Item["ItemId"] ?>" class="ewItemForm form-horizontal" data-type="<?php echo $ewpp_Item["ItemButtonTypeId"] ?>">
<!-- Common -->
<input type="hidden" name="id" value="<?php echo $ewpp_Item["ItemId"] ?>">
<input type="hidden" name="item_number" value="<?php echo ewpp_HtmlEncode($ewpp_Item["ItemNumber"]) ?>">
<input type="hidden" name="item_name" value="<?php echo ewpp_HtmlEncode($ewpp_Item["ItemName"]) ?>">
<input type="hidden" name="amount" value="<?php echo $ewpp_Item["ItemPrice"] ?>">
<input type="hidden" name="amount_base" value="<?php echo $ewpp_Item["ItemPrice"] ?>">
<?php if (EWPP_PROJECT_CHARSET <> "") { ?>
<input type="hidden" name="charset" value="<?php echo EWPP_PROJECT_CHARSET ?>">
<?php } ?>
<?php
for ($i=0; $i<7; $i++) {
	if ($ewpp_Item["ItemOption" . ($i+1) . "FieldName"] <> "") { ?>
<input type="hidden" name="on<?php echo $i ?>d" value="<?php echo ewpp_HtmlEncode($ewpp_Item["ItemOption" . ($i+1) . "FieldName"]) ?>">
<input type="hidden" name="or<?php echo $i ?>" value="<?php echo $ewpp_Item["ItemOption" . ($i+1) . "Required"] ?>">
<input type="hidden" name="on<?php echo $i ?>" value="">
<input type="hidden" name="os<?php echo $i ?>" value="">
<?php
	}
}
?>
<?php if (floatval($ewpp_Item["ItemHandling"]) > 0) { ?>
<input type="hidden" name="handling" value="<?php echo $ewpp_Item["ItemHandling"] ?>">
<?php } ?>
<?php if (floatval($ewpp_Item["ItemWeight"]) > 0) { ?>
<input type="hidden" name="weight" value="<?php echo $ewpp_Item["ItemWeight"] ?>">
<?php } ?>
<?php if (floatval($ewpp_Item["ItemTax"]) > 0) { ?>
<input type="hidden" name="tax" value="<?php echo ewpp_HtmlEncode($ewpp_Item["ItemTax"]) ?>">
<?php } ?>
<!-- Button Type Specific -->
<?php if (intval($ewpp_Item["ItemButtonTypeId"]) == 0) { // Add to Cart ?>
	<input type="hidden" name="discounttype" value="<?php echo $ewpp_Item["ItemDiscountTypeId"] ?>">
	<input type="hidden" name="taxtype" value="<?php echo $ewpp_Item["ItemTaxTypeId"] ?>">
	<input type="hidden" name="shiptype" value="<?php echo $ewpp_Item["ItemShippingTypeId"] ?>">
<?php } else { // Buy Now or Subscribe ?>
	<?php if (intval($ewpp_Item["ItemButtonTypeId"]) == 1) { // Buy Now ?>
		<input type="hidden" name="cmd" value="_xclick">
	<?php } elseif (intval($ewpp_Item["ItemButtonTypeId"]) == 2) { // Subscribe ?>
		<input type="hidden" name="cmd" value="_xclick-subscriptions">		
		<?php if (ewpp_ValidSubscribe($ewpp_Item, 3)) { // Mandatory, regular price ?>
			<?php if (ewpp_ValidSubscribe($ewpp_Item, 1)) { // Trial 1 ?>
		<input type="hidden" name="a1" value="<?php echo $ewpp_Item["ItemSubscribeA1"] ?>">
		<input type="hidden" name="p1" value="<?php echo $ewpp_Item["ItemSubscribeP1"] ?>">
		<input type="hidden" name="t1" value="<?php echo $ewpp_Item["ItemSubscribeT1"] ?>">			
				<?php if (ewpp_ValidSubscribe($ewpp_Item, 2)) { // Trial 2, requires Trial 1 ?>
		<input type="hidden" name="a2" value="<?php echo $ewpp_Item["ItemSubscribeA2"] ?>">
		<input type="hidden" name="p2" value="<?php echo $ewpp_Item["ItemSubscribeP2"] ?>">
		<input type="hidden" name="t2" value="<?php echo $ewpp_Item["ItemSubscribeT2"] ?>">
				<?php	} ?>							
			<?php	} ?>			
		<input type="hidden" name="a3" value="<?php echo $ewpp_Item["ItemSubscribeA3"] ?>">
		<input type="hidden" name="p3" value="<?php echo $ewpp_Item["ItemSubscribeP3"] ?>">
		<input type="hidden" name="t3" value="<?php echo $ewpp_Item["ItemSubscribeT3"] ?>">
		<?php } else { ?>
			<div class="alert alert-warning col-sm-4 col-xs-12"><?php echo $PPLanguage->Phrase("InvalidSubscribeSettings") ?></div>		
		<?php	} ?>
		<?php if ($ewpp_Item["ItemSubscribeRecurring"] && intval($ewpp_Item["ItemSubscribeRecurringTimes"]) > 1) { ?>
		<input type="hidden" name="src" value="1">
		<input type="hidden" name="srt" value="<?php echo $ewpp_Item["ItemSubscribeRecurringTimes"] ?>">
		<input type="hidden" name="sra" value="<?php echo (($ewpp_Item["ItemSubscribeReattempt"]) ? 1 : 0) ?>">
		<?php } ?>
	<?php } ?>
	<input type="hidden" name="business" value="<?php echo EWPP_BUSINESS ?>">		
	<input type="hidden" name="currency_code" value="<?php echo EWPP_CURRENCY_CODE ?>">
	<input type="hidden" name="weight_unit" value="kgs">
	<input type="hidden" name="cs" value="0">
	<input type="hidden" name="no_note" value="0">
	<input type="hidden" name="no_shipping" value="0">
<?php } ?>
<!-- view page content begin -->
<div id="returntolist"><a href="<?php echo EWPP_CART_LIST_PAGE ?>" class="btn btn-default" role="button"><?php echo $PPLanguage->Phrase("BackToList") ?></a></div>
<h4><span class="ewItemName"><?php echo $ewpp_Item["ItemName"] ?></span> <small class="ewItemNumber"><?php echo $ewpp_Item["ItemNumber"] ?></small></h4>
<table class="ewItemTable">
<tr>
<td class="ewViewThumb">
<?php if (strval(@$ewpp_Item["ItemImage"]) <> "") { ?>
<a href="<?php echo ewpp_ImageHref($ewpp_Item["ItemImage"], EWPP_IMAGE_FULL_VIEW) ?>" rel="<?php echo $ewpp_Item["ItemId"] ?>"><?php echo ewpp_ImageTag($ewpp_Item["ItemId"], $ewpp_Item["ItemImage"], EWPP_IMAGE_THUMBNAIL_VIEW, EWPP_IMAGE_THUMBNAIL_WIDTH_VIEW, EWPP_IMAGE_THUMBNAIL_HEIGHT_VIEW) ?></a>
<?php }?>
</td>
<td class="ewViewInfo">
<!-- Custom name value pairs -->
<?php
for ($i = 1; $i <= 6; $i++) {
	$si = ($i == 1) ? "" : strval($i);
	if (@$ewpp_Item["ItemCustomName" . $si] <> "" && @$ewpp_Item["ItemCustom" . $si] <> "") {
?>
<div class="form-group">
<div class="col-xs-12">
		<label class="col-xs-3 control-label"><?php echo $ewpp_Item["ItemCustomName" . $si] ?></label>
		<div class="col-xs-9 control-label">
			<div class="text-left"><?php echo $ewpp_Item["ItemCustom" . $si] ?></div>
		</div>	
</div>
</div>
<?php
	}
}
?>
<!-- Options -->
<?php
for ($i = 1; $i <= 7; $i++) {
	if (ewpp_ShowOption($ewpp_Item, $i)) {
?>
<div class="form-group">
<label class="col-xs-3 control-label"><?php echo $ewpp_Item["ItemOption" . $i . "FieldName"] ?></label><div class="col-xs-9 control-label"><?php echo ewpp_FormatOption("os" . ($i-1) . "d", $ewpp_Item["ItemOption" . $i . "Type"], $ewpp_Item["ItemOption" . $i], @$ewpp_Item["ItemOption" . $i . "Default"]) ?></div></div>
<?php
	}
}
?>
<div class="form-group">
	<label class="col-xs-3 control-label"><?php echo $PPLanguage->Phrase("Price") ?></label><div class="col-xs-9 control-label pp_amount_<?php echo $ewpp_Item["ItemId"] ?>" id="pp_amount_<?php echo $ewpp_Item["ItemId"] ?>" ><?php echo ewpp_FormatCurrency($ewpp_Item["ItemPrice"]) ?></div>
</div>
<?php if (!(EWPP_USE_ITEM_COUNT) && $ewpp_Item["ItemCount"] <= 0 && EWPP_SHOW_SOLD_OUT) { // 502 ?>
<div class="alert alert-warning ewSoldOut"><?php echo $PPLanguage->Phrase("SoldOut") ?></div>	
<?php } else { ?>
	<?php if ($ewpp_Item["ItemButtonTypeId"] <> 2) { ?>
<div class="form-group">
     		<label class="col-xs-3 control-label"><?php echo $PPLanguage->Phrase("DescQuantity") ?></label>
			<div class="col-xs-9">
				<input type="text" class="form-control" name="quantity" value="1" placeholder="Qty">
	<?php } ?>
				<button type="submit" class="btn btn-primary" name="btnSubmit" value="<?php echo ewpp_SubmitButtonText($ewpp_Item["ItemButtonTypeId"]) ?>"><?php echo ewpp_SubmitButtonText($ewpp_Item["ItemButtonTypeId"]) ?></button>
			</div>
		</div>	
<?php } ?>
</td>
</tr>
</table>
<br>
<div class="form-group">
	<div class="col-xs-12">
		<?php echo ewpp_FormatDescription($ewpp_Item["ItemDescription"]) ?>
	</div>
</div>
<?php
for ($i = 2; $i <= 6; $i++) {
	if ($ewpp_Item["ItemImage" . $i] <> "") {
?>
<div class="form-group">
	<div class="col-xs-12">
		<a href="<?php echo ewpp_ImageHref($ewpp_Item["ItemImage" . $i], EWPP_IMAGE_FULL_VIEW) ?>" rel="<?php echo $ewpp_Item["ItemId"] ?>"><?php echo ewpp_ImageTag($ewpp_Item["ItemId"] . $i, $ewpp_Item["ItemImage" . $i], EWPP_IMAGE_THUMBNAIL_VIEW, EWPP_IMAGE_THUMBNAIL_WIDTH_VIEW, EWPP_IMAGE_THUMBNAIL_HEIGHT_VIEW) ?></a>
	</div>
</div>
<?php
	}
}
?>
</div>
</div>
<!-- view page content end -->
</form>
<?php

// Close connection
$conn->Close();
?>
<?php include "ppfooter.php" ?>
