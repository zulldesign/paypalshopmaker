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
<?php
if (@$_GET["sid"] == "" || @$_GET["sid"] <> ewpp_TeaEncrypt(session_id(), EWPP_RANDOM_KEY))
	exit();

// Open connection to the database
$conn = ewpp_Connect();

// Execute query
if (@$_GET["item"] <> "") {
	$itemno = $_GET["item"];
	$itemcnt = ewpp_GetItemCount($itemno);	

	//ewpp_WriteLog("QUERY", "item number", $itemno);
	//ewpp_WriteLog("QUERY", "item count", $itemcnt);

	echo $itemcnt;
} elseif (@$_GET["code"] <> "") {
	$code = @$_GET["code"];
	$disc = ewpp_GetDiscount($code); // "Percent(0-100)|Amount"

	//ewpp_WriteLog("QUERY", "discount code", $code);
	//ewpp_WriteLog("QUERY", "discount", $disc);

	echo $disc;
} elseif (@$_GET["order"] <> "") {
	ewpp_WriteLog("ORDER", "submitted variables", ewpp_PostVars());
}

// Close connection
$conn->Close();
exit();
?>
