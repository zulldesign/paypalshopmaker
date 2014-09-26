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
<?php $EWPP_PAGE_ID = "menupage"; // Page ID ?>
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

// Init page content
$sMenuPageContent = "";

// Set up current menu item
ewpp_LoadMenuId();

// Build SQL
$sSql = EWPP_MENU_SELECT_SQL;
if ($ewpp_MenuId <> "")
	$sWhere = str_replace("@@MenuId@@", $ewpp_MenuId, EWPP_MENU_MENUID_FILTER);
if (@$sWhere <> "")
	$sSql .= " WHERE " . $sWhere;

// Open recordset
$rs = $conn->Execute($sSql); 
if ($rs && !$rs->EOF) {
	ewpp_LoadMenu($rs); // load menu details
	$sMenuPageContent = $ewpp_MenuPageContent;
	$rs->Close();
}
?>
<?php include "ppheader.php" ?>
<div>
<?php echo $sMenuPageContent ?>
</div>
	</div>
</div>		
<?php
$conn->Close();
?>
<?php include "ppfooter.php" ?>
