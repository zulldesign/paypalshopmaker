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
<?php $EWPP_PAGE_ID = "list"; // Page ID ?>
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
<?php

// Get URL parameters
ewpp_GetUrlParams();

// Set up current category
ewpp_LoadCatById();

// Build SQL
$sSql = EWPP_PRODUCT_SELECT_SQL;
$sWhere = "";
$sOrderBy  = EWPP_PRODUCT_ORDER_BY;

// Get search criteria
$sKeyword = @$_SESSION[EWPP_SESSION_SEARCH_KEYWORD];

// Add default and item count filter
ewpp_AddDefaultFilter($sWhere);
ewpp_AddCountFilter($sWhere);

// Add category filter
if (!EWPP_SEARCH_ALL_CATEGORIES || $sKeyword == "")
	ewpp_AddFilter($sWhere, ewpp_GetCategoryFilter($ewpp_CatId));

// Get the keywords for auto-suggest
$tmpsql = "SELECT ";
$arfields = explode(",", EWPP_SEARCH_FIELDS);
$cnt = count($arfields);
for ($i = 0; $i < $cnt; $i++) {
	$tmpsql .= ewpp_DbQuote($arfields[$i]);
	if ($i < $cnt - 1)
		$tmpsql .= ", ";
}
$tmpsql .= " FROM " . ewpp_DbQuote(EWPP_TABLENAME_ITEM) . " WHERE " . $sWhere;
$rs = $conn->Execute($tmpsql);
$keywords = array();
while ($rs && !$rs->EOF) {
	for ($i = 0; $i < $cnt; $i++) {
		$kw = $rs->fields($arfields[$i]);
		if (strval($kw) <> "")
			$keywords[] = $kw;
	}
	$rs->MoveNext();
}
if ($rs)
	$rs->Close();
sort($keywords);
foreach ($keywords as &$kw)
	$kw = "\"" . ewpp_JsEncode($kw) . "\"";

// Add search criteria
ewpp_AddSearchCriteria($sWhere);
if ($sWhere <> "")
	$sSql .= " WHERE " . $sWhere;
if ($sOrderBy <> "")
	$sSql .= " ORDER BY " . $sOrderBy;

// Number of records per row (multi column)
$ewpp_RecPerRow = 0;

// Set up pager
$ewpp_StartRec = 0; // Start record index
$ewpp_StopRec = 0; // Stop record index
$ewpp_TotalRecs = 0; // Total number of records
$ewpp_DisplayRecs = 6;
$ewpp_NoRecordShown = FALSE;
$ewpp_SelectLimit = (EWPP_DB_TYPE == "MYSQL" || EWPP_DB_TYPE == "SQLITE");
$ewpp_TotalRecs = ewpp_GetCategoryProductCount($ewpp_CatId);
$ewpp_TotalPages = intval($ewpp_TotalRecs/$ewpp_DisplayRecs);
if ($ewpp_TotalRecs % $ewpp_DisplayRecs > 0) $ewpp_TotalPages++;
$ewpp_PageNumber = 1;
$ewpp_StartRec = 1;

// Set up pager
ewpp_LoadPagerPosition();
if ($ewpp_SelectLimit) {
	if ($ewpp_DisplayRecs > 0) {
		$sSql .= " LIMIT " . $ewpp_DisplayRecs;
		if ($ewpp_StartRec > 1)
			$sSql .= " OFFSET " . ($ewpp_StartRec - 1);
	}
}

// Open recordset
$rs = $conn->Execute($sSql);

// Set up end record position
$ewpp_StopRec = $ewpp_StartRec + $ewpp_DisplayRecs - 1;
if ($ewpp_StopRec > $ewpp_TotalRecs) $ewpp_StopRec = $ewpp_TotalRecs;
?>
<div id="ewCartList">
<?php

//if (EWPP_SEARCH_ALL_CATEGORIES && $sKeyword <> "") {
if (EWPP_SEARCH_ALL_CATEGORIES) {
	ewpp_Breadcrumb($ewpp_CatPath, $ewpp_CatFn);

	//echo $PPLanguage->Phrase("Products");
} else {
	if (@$ewpp_CatPath <> "") {
		ewpp_Breadcrumb($ewpp_CatPath, $ewpp_CatFn, TRUE);

		//echo $PPLanguage->Phrase("Category") . $ewpp_CatPath;
	//} elseif (@$ewpp_CatName <> "") {
		//ewpp_Breadcrumb($ewpp_CatPath, $ewpp_CatFn, TRUE);
		//echo $PPLanguage->Phrase("Category") . $ewpp_CatName;

	} else {
		ewpp_Breadcrumb($ewpp_CatPath, "");

		//echo $PPLanguage->Phrase("Products");
	}
}
?>
<!-- Search form -->
<script type="text/javascript">
P.keywords = [<?php echo implode(",", $keywords) ?>];
</script>
<form role="form">
<div class="form-group">
	<div class="row">
		<div class="col-sm-8 col-xs-12">
			<div class="input-group"> 
				<input type="text" name="<?php echo EWPP_SEARCH_KEYWORD ?>" id="<?php echo EWPP_SEARCH_KEYWORD ?>" class="form-control" data-provide="typeahead" placeholder="<?php echo ewpp_HtmlEncode($PPLanguage->Phrase("Search")) ?>" value="<?php echo ewpp_HtmlEncode($sKeyword) ?>">
				<span class="input-group-btn">
					<button class="btn btn-primary" name="btnSearch" id="btnSearch" value="<?php echo ewpp_HtmlEncode($PPLanguage->Phrase("Search")) ?>" type="submit"><?php echo ewpp_HtmlEncode($PPLanguage->Phrase("Search")) ?></button>
			<?php if ($sKeyword <> "") { ?>
			 	<a class="btn btn-default" href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_COMMAND . "=" . EWPP_COMMAND_RESET ?>"><?php echo $PPLanguage->Phrase("ClearSearch") ?></a>
			<?php } ?>
				</span>
			</div>
		</div>
	</div>
</div>			
</form>
</div>
<?php include "cartpager.php" ?>
<?php
if ($ewpp_TotalRecs > 0) {
?>
<!-- list page content begin -->
<div class="list-group" id="ewItems">
<?php

// Move to first record
$nRecCount = $ewpp_StartRec - 1;
$nRecActual = 0;
if (!$rs->EOF) {
	$rs->MoveFirst();
	if (!$ewpp_SelectLimit)
		$rs->Move($ewpp_StartRec - 1);
}
while (!$rs->EOF && $nRecCount < $ewpp_StopRec) {
	$nRecCount++;
	if ($nRecCount >= $ewpp_StartRec) {
		$nRecActual++;

		//###$sRowClass = ($nRecActual % 2 == 1) ? EWPP_ROW_CLASS_NAME : EWPP_ALT_ROW_CLASS_NAME;
		ewpp_LoadProduct($rs); // Load product details

		// Cart detail URL
		$ewpp_CartViewFullUrl = EWPP_CART_VIEW_PAGE . "?id=" . $ewpp_Item["ItemId"];
		$ewpp_CartViewUrl = (strrpos($ewpp_CartViewFullUrl, "/") !== FALSE) ? substr($ewpp_CartViewFullUrl, strrpos($ewpp_CartViewFullUrl, "/")+1) : $ewpp_CartViewFullUrl;

		// 5.0
		//###$ewpp_Item["RowClass"] = $sRowClass;

		$ewpp_Item["CartViewUrl"] = $ewpp_CartViewUrl;
		$ewpp_Item["ImageWidth"] = (EWPP_IMAGE_THUMBNAIL_WIDTH > 0) ? " width: " . EWPP_IMAGE_THUMBNAIL_WIDTH . "px" : "";
		$ewpp_Item["ImageTag"] = ewpp_ImageTag($ewpp_Item["ItemId"], $ewpp_Item["ItemImage"], EWPP_IMAGE_THUMBNAIL_LIST, EWPP_IMAGE_THUMBNAIL_WIDTH, EWPP_IMAGE_THUMBNAIL_HEIGHT);
		for ($i = 1; $i <= 7; $i++) {
			$ewpp_Item["ShowOption" . $i] = ewpp_ShowOption($ewpp_Item, $i);
			if ($ewpp_Item["ShowOption" . $i]) {
				$ewpp_Item["FormattedOption" . $i] = ewpp_FormatOption("os" . ($i-1) . "d", $ewpp_Item["ItemOption" . $i . "Type"], $ewpp_Item["ItemOption" . $i], @$ewpp_Item["ItemOption" . $i . "Default"]);
			}
		}
		$ewpp_Item["FormattedPrice"] = ewpp_FormatCurrency($ewpp_Item["ItemPrice"]);
		$ewpp_Item["ShowSoldOut"] = !EWPP_USE_ITEM_COUNT && $ewpp_Item["ItemCount"] <= 0 && EWPP_SHOW_SOLD_OUT;
		for ($i = 1; $i <= 6; $i++) {
			$si = ($i == 1) ? "" : strval($i);
			$ewpp_Item["ShowCustom" . $si] = (@$ewpp_Item["ItemCustomName" . $si] <> "" && @$ewpp_Item["ItemCustom" . $si] <> "");
		}

		// Check for multi column TR start
		$ewpp_Item["BeginRow"] = TRUE;
		$ewpp_Item["EndRow"] = TRUE;
		if ($ewpp_RecPerRow > 0) {
			$nRowCount = intval($nRecActual/$ewpp_RecPerRow) + 1;
			$nColCount = ($nRecActual % $ewpp_RecPerRow);

			//###$sRowClass = ($nRowCount % 2 == 1) ? EWPP_ROW_CLASS_NAME : EWPP_ALT_ROW_CLASS_NAME;
			$bPageEnd = ($nRecCount == $ewpp_TotalRecs ||	$nRecActual == $ewpp_DisplayRecs);
			$ewpp_Item["BeginRow"] = ($nColCount == 1);
			$ewpp_Item["EndRow"] = ($nColCount == 0 || $bPageEnd);
			$ewpp_Item["EmptyTableCells"] = "";
			if ($nColCount > 0) {
				for ($i = 1; $i <= ($ewpp_RecPerRow - $nColCount); $i++)
					$ewpp_Item["EmptyTableCells"] .= "<td></td>";
			}
		}
?>
<div class="list-group-item">
<table class="ewItemTable">
<tr>
<td class="ewListThumb">
<a href="<?php echo ewpp_ImageHref($ewpp_Item["ItemImage"], EWPP_IMAGE_FULL_VIEW) ?>" rel="<?php echo $ewpp_Item["ItemId"] ?>"><?php echo $ewpp_Item["ImageTag"] ?></a><br>
<?php
for ($i = 2; $i <= 6; $i++) {
	if ($ewpp_Item["ItemImage" . $i] <> "") {
?>
<a href="<?php echo ewpp_ImageHref($ewpp_Item["ItemImage" . $i], EWPP_IMAGE_FULL_VIEW) ?>" rel="<?php echo $ewpp_Item["ItemId"] ?>" class="ewHidden"><?php echo ewpp_ImageTag($ewpp_Item["ItemId"] . $i, $ewpp_Item["ItemImage" . $i], EWPP_IMAGE_THUMBNAIL_LIST, EWPP_IMAGE_THUMBNAIL_WIDTH, EWPP_IMAGE_THUMBNAIL_HEIGHT) ?></a><br>
<?php
	}
}
?>
</td>
<td class="ewListInfo">
<h4><a href="<?php echo $ewpp_Item["CartViewUrl"] ?>"><span class="ewItemName"><?php echo $ewpp_Item["ItemName"] ?></span> <small class="ewItemNumber"><?php echo $ewpp_Item["ItemNumber"] ?></small></a></h4>
<?php

// Custom name value pairs
for ($i = 1; $i <= 6; $i++) {
	$si = ($i == 1) ? "" : strval($i);
	if ($ewpp_Item["ShowCustom" . $si]) {
?>
<div class="row">
	<label class="col-xs-3 ewCustomName"><?php echo $ewpp_Item["ItemCustomName" . $si] ?></label>
	<span class="col-xs-9"><?php echo $ewpp_Item["ItemCustom" . $si] ?></span>
</div>
<?php
	}
}
?>
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
<!-- Options -->
<?php
for ($i = 1; $i <= 7; $i++) {
	if ($ewpp_Item["ShowOption" . $i]) {
?>
<div class="form-group">
	<label class="col-xs-3 control-label"><?php echo $ewpp_Item["ItemOption" . $i . "FieldName"] ?></label><div class="col-xs-9"><?php echo $ewpp_Item["FormattedOption" . $i] ?></div>
</div>
<?php
	}
}
?>
<div class="form-group">
	<label class="col-xs-3 control-label"><?php echo $PPLanguage->Phrase("Price") ?></label><div class="col-xs-9 control-label <?php echo EWPP_AMOUNT_DIV_PREFIX . $ewpp_Item["ItemId"] ?>" id="<?php echo EWPP_AMOUNT_DIV_PREFIX . $ewpp_Item["ItemId"] ?>"><?php echo $ewpp_Item["FormattedPrice"] ?></div>
</div>
<?php if ($ewpp_Item["ShowSoldOut"]) { ?>
<div class="alert alert-warning ewSoldOut"><?php echo $PPLanguage->Phrase("SoldOut") ?></div>
<?php } else { ?>
	<?php if ($ewpp_Item["ItemButtonTypeId"] <> 2) { ?>
	<div class="form-group">
     	<label class="col-xs-3 control-label"><?php echo $PPLanguage->Phrase("DescQuantity") ?></label>
		<div class="col-xs-9">
			<input type="text" class="form-control" name="quantity" value="1" placeholder="Qty">
	<?php } ?>
			<button type="submit" name="btnSubmit" class="btn btn-primary" value="<?php echo ewpp_SubmitButtonText($ewpp_Item["ItemButtonTypeId"]) ?>"><?php echo ewpp_SubmitButtonText($ewpp_Item["ItemButtonTypeId"]) ?></button>
		</div>
	</div>	
<?php } ?>
</form>
</td>
</tr>
</table>
</div>
<?php
	}
	$rs->MoveNext();
}
?>
</div>
<!-- list page content end -->
<?php
}
if ($rs)
	$rs->Close();
?>
	</div>
</div>		
<?php
$conn->Close();
?>
<?php include "ppfooter.php" ?>
