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

// Unset all of the Session variables
$_SESSION = array();

// Delete the Session cookie and kill the Session
if (isset($_COOKIE[session_name()]))
	setcookie(session_name(), '', time()-42000, '/');

// Finally, destroy the Session
@session_destroy();
?>
<?php $EWPP_PAGE_ID = "finish"; // Page ID ?>
<?php

// Open connection to the database
$conn = ewpp_Connect();
?>
<?php include "ppheader.php" ?>
<h4 class="ewTitle"><?php echo $PPLanguage->Phrase("ThankYou") ?></h4>
<div class="alert alert-success col-xs-6">
<?php if (EWPP_USE_PAYPAL) { ?>
	<?php echo $PPLanguage->Phrase("ThankYouMessage") ?>
<?php } else { ?>
	<?php echo $PPLanguage->Phrase("OrderReceivedMessage") ?>
<?php } ?>
</div>
<?php

// Close connection
$conn->Close();
?>
<?php include "ppfooter.php" ?>
