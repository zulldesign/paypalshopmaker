<?php

/**
 * PayPal Shop Maker 6
 * (C) 2008-2014 e.World Technology Limited. All rights reserved.
 */

function ewpp_Breadcrumb($name, $uri, $cat = FALSE) {
		global $PPLanguage;
		$title = explode(",", $name);
		$cnt = count($title);
		$link = explode(",", $uri);
		if ($cat && $cnt > 1)
			$nav = "<ul class=\"breadcrumb\">";
		else
			$nav = "";
		if ($cat && $cnt > 1) { 
			for ($i = 0; $i < $cnt; $i++) {
				if ($i < $cnt - 1) {
					$nav .= "<li>";
				} else {
					$nav .= "<li class=\"active\">";
					$link[$i] = ""; // No need to show url for current page
				}
				if ($link[$i] <> "")
					$nav .= "<a href=\"" . $link[$i] . "\">" . $title[$i] . "</a>";
				else
					$nav .= $title[$i];
				if ($i < $cnt - 1)
					$nav .= "<span class=\"divider\">" . "</span>";
				$nav .= "</li>";
			}
		} elseif ($cat && $cnt == 1) {
			$nav .= "<h4>" . $title[0] . "</h4>";
		} else {	
			$nav .= "<h4>" . $PPLanguage->Phrase("Products") . "</h4>";
		}		
		if ($cat && $cnt > 1)
			$nav .= "</ul>";
		echo $nav;
	}

// Function to check application status
function ewpp_ApplicationEnabled() {

	return ewpp_ExecuteScalar("SELECT " . ewpp_DbQuote("AppEnabled") . " FROM " . ewpp_DbQuote(EWPP_TABLENAME_APPSTATUS));
}

// Include PHPMailer class
include_once EWPP_PHPMAILER_PATH;

// Function to Load Email Content from input file name
// - Content Loaded to the following global variables
// - Subject: sEmailSubject
// - From: sEmailFrom
// - To: sEmailTo
// - Cc: sEmailCc
// - Bcc: sEmailBcc
// - Format: sEmailFormat
// - Content: sEmailContent
function ewpp_LoadEmail($fn) {

	$sWrk = ewpp_LoadTxt($fn); // Load text file content
	ewpp_LoadEmailEx($sWrk);
}

function ewpp_LoadEmailEx($content) {
	global $ewpp_EmailSubject, $ewpp_EmailFrom, $ewpp_EmailTo, $ewpp_EmailCc, $ewpp_EmailBcc,
		$ewpp_EmailFormat, $ewpp_EmailContent, $ewpp_EmailError;

	// Initialize
	$ewpp_EmailFrom = "";
	$ewpp_EmailTo = "";
	$ewpp_EmailCc = "";
	$ewpp_EmailBcc = "";
	$ewpp_EmailSubject = "";
	$ewpp_EmailFormat = "";
	$ewpp_EmailContent = "";
	$ewpp_EmailError = "";
	$sWrk = $content; // Get content
	$sWrk = str_replace("\r\n", "\n", $sWrk); // Convert to LF
	$sWrk = str_replace("\r", "\n", $sWrk); // Convert to LF
	if ($sWrk <> "") {
		$i = strpos($sWrk, "\n\n"); // Locate header and mail content
		if ($i > 0) {
			$sHeader = substr($sWrk, 0, $i);
			$ewpp_EmailContent = trim(substr($sWrk, $i, strlen($sWrk)));
			$arrHeader = explode("\n", $sHeader);
			for ($j = 0; $j < count($arrHeader); $j++) {
				$i = strpos($arrHeader[$j], ":");
				if ($i > 0) {
					$sName = trim(substr($arrHeader[$j], 0, $i));
					$sValue = trim(substr($arrHeader[$j], $i+1));
					switch (strtolower($sName))
					{
						case "subject":
							$ewpp_EmailSubject = $sValue;
							break;
						case "from":
							$ewpp_EmailFrom = $sValue;
							break;
						case "to":
							$ewpp_EmailTo = $sValue;
							break;
						case "cc":
							$ewpp_EmailCc = $sValue;
							break;
						case "bcc":
							$ewpp_EmailBcc = $sValue;
							break;
						case "format":
							$ewpp_EmailFormat = $sValue;
							break;
					}
				}
			}
		}
	}
}

// Function to load a text file
function ewpp_LoadTxt($fn) {

	if (strpos($fn, EWPP_PATH_DELIMITER) !== FALSE) {
		$wrkfn = $fn; // Assume full path given
	} else {
		$wrkfn = ewpp_IncludeTrailingDelimiter(realpath("."), TRUE) . $fn; // Assume in current folder
	}
	if (file_exists($wrkfn)) {
		$fobj = fopen($wrkfn , "r");
		$contents = fread($fobj, filesize($wrkfn));
		fclose($fobj);
		return $contents;
	} else {
		return "";
	}
}

// Function to send email
function ewpp_SendEmail($sFrEmail, $sToEmail, $sCcEmail, $sBccEmail, $sSubject, $sMail, $sFormat) {
		// Uncomment to debug
//		echo "sSubject: " . $sSubject . "<br>";
//		echo "sFrEmail: " . $sFrEmail . "<br>";
//		echo "sToEmail: " . $sToEmail . "<br>";
//		echo "sCcEmail: " . $sCcEmail . "<br>"; 
//		echo "sSubject: " . $sSubject . "<br>";
//		echo "sMail: " . $sMail . "<br>";
//		echo "sFormat: " . $sFormat . "<br>";
//		die();

	$mail = new PHPMailer();
	if (EWPP_PROJECT_CHARSET <> "")
		$mail->CharSet = EWPP_PROJECT_CHARSET;
	$mail->SetLanguage(EWPP_PHPMAILER_LANG, realpath(EWPP_PHPMAILER_LANG_PATH) . EWPP_PATH_DELIMITER);
	$mail->IsSMTP(); 
	$mail->Host = EWPP_SMTPSERVER;
	$mail->SMTPAuth = (EWPP_SMTPSERVER_USERNAME <> "" && EWPP_SMTPSERVER_PASSWORD <> "");
	$mail->Username = EWPP_SMTPSERVER_USERNAME;
	$mail->Password = EWPP_SMTPSERVER_PASSWORD;
	$mail->Port = EWPP_SMTPSERVER_PORT; 
	$mail->From = $sFrEmail;
	$mail->FromName = $sFrEmail;
	$mail->Subject = $sSubject;
	$mail->Body = $sMail;
	$sToEmail = str_replace(";", ",", $sToEmail);
	$arrTo = explode(",", $sToEmail);
	foreach ($arrTo as $sTo)
		$mail->AddAddress(trim($sTo));
	if ($sCcEmail <> "") {
		$sCcEmail = str_replace(";", ",", $sCcEmail);
		$arrCc = explode(",", $sCcEmail);
		foreach ($arrCc as $sCc)
			$mail->AddCC(trim($sCc));
	}
	if ($sBccEmail <> "") {
		$sBccEmail = str_replace(";", ",", $sBccEmail);
		$arrBcc = explode(",", $sBccEmail);
		foreach ($arrBcc as $sBcc)
			$mail->AddBCC(trim($sBcc));
	}
	if (strtolower($sFormat) == "html") {
		$mail->ContentType = "text/html";
	} else {
		$mail->ContentType = "text/plain";
	}
	$res = $mail->Send();
	if (!$res) {
		global $ewpp_EmailError;
		$ewpp_EmailError = $mail->ErrorInfo;
	}
	$mail->ClearAddresses();
	$mail->ClearAttachments();
	return $res;
}

// Function for Creating Folder
function ewpp_CreateFolder($dir, $mode = 0777) {

  if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
  if (!ewpp_CreateFolder(dirname($dir), $mode)) return FALSE;
  return @mkdir($dir, $mode);
}

// Get content using HTTP POST by CURL (Client URL Library)
// Note: CURL must be enabled
function ewpp_GetContentByCurl($url, $method, $postdata) {

	$ch = curl_init();
	if (strtoupper(trim($method)) == "POST") {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	} elseif (strtoupper(trim($method)) == "GET") {
		curl_setopt($ch, CURLOPT_URL, $url . "?" . $postdata);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$fp = curl_exec($ch);
	curl_close($ch);
	return $fp;
}

// Get content using HTTP POST by socket
// Note: Sockets must be enabled
function ewpp_GetContentBySocket($url, $method, $postdata) {

	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($postdata) . "\r\n\r\n";
	$pos = strpos(strtolower($url), "sandbox");
	if ($pos === FALSE) {
		$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
	} else {
		$fp = fsockopen('www.sandbox.paypal.com', 80, $errno, $errstr, 30);
	}
	if (!$fp) {
		return "Failed to open socket connection";
	} else {
		fputs($fp, $header . $postdata);
		$response = "";
		$getres = FALSE;
		while (!feof($fp)) {
			$res = fgets($fp, 1024);
			if (!$getres && $res == "\r\n")
				$getres = TRUE;
			if ($getres)
				$response .= $res;
		}
		fclose($fp);
	}
	return $response;
}

// Get content using HTTP POST
// parameters:
// - url = destination url
// - method = "GET", "POST"
// - postdata = Post Data
// Note: Either CURL or sockets must be enabled
function ewpp_GetContent($url, $method, $postdata) {

	if (!function_exists("curl_init") && !function_exists("fsockopen"))
		return "Both curl and fsockopen not exists.";
	$fp = FALSE;
	if (function_exists("curl_init"))
		$fp = @ewpp_GetContentByCurl($url, $method, $postdata);
	if (!$fp)
		$fp = @ewpp_GetContentBySocket($url, $method, $postdata);
	return trim($fp);
}

// Add dates
function ewpp_DateAdd($interval, $number, $date) {

	$date_time_array = getdate($date);
	$hours = $date_time_array['hours'];
	$minutes = $date_time_array['minutes'];
	$seconds = $date_time_array['seconds'];
	$month = $date_time_array['mon'];
	$day = $date_time_array['mday'];
	$year = $date_time_array['year'];
	switch ($interval) {
		case 'yyyy':
			$year += $number;
			break;
		case 'q':
			$year += $number*3;
			break;
		case 'm':
			$month += $number;
			break;
		case 'y':
		case 'd':
		case 'w':
			$day += $number;
			break;
		case 'ww':
			$day += $number*7;
			break;
		case 'h':
			$hours += $number;
			break;
		case 'n':
			$minutes += $number;
			break;
		case 's':
			$seconds += $number;
			break;
	}
	$timestamp = mktime($hours, $minutes, $seconds, $month, $day, $year);
	return $timestamp;
}

// Scramble string
function ewpp_Scramble($str) {

	$encstr = base64_encode(crypt($str, EWPP_RANDOM_KEY));
	$encstr = str_replace("+", "", $encstr);
	$encstr = str_replace("/", "", $encstr);
	$encstr = str_replace("=", "", $encstr);
	return $str . "_" . substr($encstr, 0, 12);
}

// Function for writing log
function ewpp_WriteLog($pfx, $key, $value) {

	@ewpp_WriteLogDb($pfx, $key, $value);
	if (EWPP_WRITE_LOG_FILE)
		@ewpp_WriteLogFile($pfx, $key, $value);
}

// Function for writing to database log table
function ewpp_WriteLogDb($pfx, $key, $value) {

	global $conn;
	$Sql = "INSERT INTO " . ewpp_DbQuote(EWPP_TABLENAME_LOG) . " (LogType, " .
		"LogDate, LogKey, LogValue) VALUES (" .
		"'" . ewpp_AdjustSql($pfx) . "', " . EWPP_NOW_FUNC . ", " .
		"'" . ewpp_AdjustSql($key) . "', " .
		"'" . ewpp_AdjustSql($value) . "')";
	return $conn->Execute($Sql);
}

// Function for writing to log file
function ewpp_WriteLogFile($pfx, $key, $value) {

	$sHeader = "datetime" . "\t" . "key" . "\t" .	"value";
	$sMsg = date("Y-m-d H:i:s") . "\t" . $key . "\t" . $value;
	$folder = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . EWPP_LOG_PATH;
	$file = $pfx . "_" . date("Ymd") . ".txt";
	ewpp_CreateFolder($folder);
	$filename = $folder . EWPP_PATH_DELIMITER . $file;
	$writeheader = !file_exists($filename);
	$handle = fopen($filename, "a+b");
	if ($writeheader)
		fwrite($handle, $sHeader . "\n");
	fwrite($handle, $sMsg . "\n");
	fclose($handle);
}

// Function for writing to file
function ewpp_WriteFile($folder, $file, $content) {

	if ($folder <> "") { // folder relative to root
		$wrkfile = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . $folder .
			EWPP_PATH_DELIMITER . $file;
	} else {
		$wrkfile = ewpp_IncludeTrailingDelimiter(realpath("."), TRUE) . $file; // Assume in current folder
	}
	$handle = fopen($wrkfile, "a+b");
	fwrite($handle, $content);
	fclose($handle);
}

// Create a temp folder relative to root
function ewpp_GetTempFolder($tx) {
	// Clean up old folders first

	ewpp_CleanupOldFolders();

	// Create temp folder
	$sRelPath = ewpp_TempDownloadFolder($tx);
	$sPath = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . $sRelPath;
	if (!file_exists($sPath))
		ewpp_CreateFolder($sPath);
	return $sRelPath;
}

// Get temp download folder
function ewpp_TempDownloadFolder($tx) {

	$sPath = trim(EWPP_DOWNLOAD_PATH); // Get download path
	$sPath = str_replace("/", EWPP_PATH_DELIMITER, $sPath);
	$sPath = ewpp_IncludeTrailingDelimiter($sPath, TRUE);

	// Append scrambled path for this tx
	return $sPath . ewpp_Scramble($tx);
}

// Get all item download file name
function ewpp_GetDownloadFiles($tx, &$arrCart, $folder) {

	$result = TRUE;
	$cnt = count($arrCart);
	for ($i = 0; $i < $cnt; $i++) {
		$url = "";
		$file = ewpp_GetDownloadFile($tx, $arrCart[$i]["item_number"], 1); // get download file name and check existence
		if ($file === FALSE) { // Error
			$result = FALSE;
		} elseif ($file <> "") { // Item has download file
			$url = (EWPP_DOWNLOAD_BINARY_WRITE) ? ewpp_BinaryWriteUrl($file) : ewpp_CopyTempFile($folder, $file);
			if ($url <> "")
				$url = ewpp_GetServerUrl() . $url;
		}
		$arrCart[$i]["download_file"] = $url;
	}
	return $result;
}

// Get item download file name
function ewpp_GetDownloadFile($tx, $item_number, $chkfile) {

	global $conn, $arItems, $EWPP_PAGE_ID;
	$type = ($EWPP_PAGE_ID <> "") ? strtoupper($EWPP_PAGE_ID) : "DOWNLOADFILE";
	$sql = "SELECT ItemDownloadFn FROM " . ewpp_DbQuote(EWPP_TABLENAME_ITEM) .
		" WHERE ItemNumber ='" . ewpp_AdjustSql($item_number) . "'";
	$url = ewpp_ExecuteScalar($sql);

	// Write log
	if (strval($url) == "") {
		ewpp_WriteLog($type, "item without download file", $tx . ", " . $item_number);
	} else {
		ewpp_WriteLog($type, "item with download file", $tx . ", " . $item_number . ", " . $url);
	}

	// Check if file exists
	if ($chkfile && $url <> "") {
		$src = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . EWPP_DOWNLOAD_SRC_PATH . EWPP_PATH_DELIMITER . $url;
		if (file_exists($src)) {
			ewpp_WriteLog($type, "download file exists", $tx . ", " . $item_number . ", " . $src);
		} else {
			ewpp_WriteLog($type, "download file not exists", $tx . ", " . $item_number . ", " . $src);
			$url = FALSE; // Error
		}
	}
	return $url;
}

// Binary write URL
function ewpp_BinaryWriteUrl($file) {

	$fn = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . EWPP_DOWNLOAD_SRC_PATH . EWPP_PATH_DELIMITER . $file;
	if (file_exists($fn)) {
		$url = "fn=" . urlencode($fn) . "&tl=" . time();
		return ewpp_GetCurrentPath() . "/" . EWPP_DOWNLOAD_PAGE . "?data=" . urlencode(ewpp_TeaEncrypt($url, EWPP_RANDOM_KEY));
	}
	return "";
}

// Write transaction data to database
function ewpp_WriteTxnData($sTx, $rawdata, $postdata) {

	global $conn;
	$Sql = "INSERT INTO " . ewpp_DbQuote(EWPP_TABLENAME_TXN) . " (TxnPPId, " .
		"TxnDateTime, TxnRawData) VALUES ('" . ewpp_AdjustSql($sTx) . "', " .
			EWPP_NOW_FUNC . ", '" . ewpp_AdjustSql($rawdata) . "')";

	//ewpp_WriteLog("IPN", "execute sql", $Sql);
	if ($conn->Execute($Sql) === FALSE) {
		ewpp_WriteLog("IPN", "insert error", $conn->ErrorMsg() . " " . $Sql);
		exit();
	}
	$txnid = $conn->Insert_ID(); // Get the last insert ID
	if ($txnid > 0 && is_array($postdata)) {
		foreach ($postdata as $name => $value) {
			if ($name <> "btnClickToBuy") {
				$Sql = "INSERT INTO " . ewpp_DbQuote(EWPP_TABLENAME_TXNDETAIL) . " (TxnId, " .
				ewpp_DbQuote("Name") . ", " . ewpp_DbQuote("Value") . ") VALUES (" .
					$txnid . ", '" . ewpp_AdjustSql($name) . "', '" . ewpp_AdjustSql($value) . "')";

				//ewpp_WriteLog("IPN", "execute sql", $Sql);
				if ($conn->Execute($Sql) === FALSE) {
					ewpp_WriteLog("IPN", "insert error", $conn->ErrorMsg() . " " . $Sql);
					exit();
				}
			}
		}
	}
}

// Update transaction record in database
function ewpp_WriteTxn($sTx, $sBusiness, $sPaymentStatus, $sPayerEmail, $testipn, $amt) {

	global $conn;
	if ($testipn <> "1") $testipn = "0";
	if (strval($amt) == "" || !is_numeric($amt)) $amt = 0;
	$Sql = "UPDATE " . ewpp_DbQuote(EWPP_TABLENAME_TXN) . " SET " .
		"TxnBusiness = '" . ewpp_AdjustSql($sBusiness) . "', " .
		"TxnStatus = '" . ewpp_AdjustSql($sPaymentStatus) . "', " .
		"TxnPayerEmail = '" . ewpp_AdjustSql($sPayerEmail) . "', " .
		"TxnTestIPN = " . $testipn . ", " .
		"TxnMcGross = " . $amt .
		" WHERE TxnPPId = '" . ewpp_AdjustSql($sTx) . "'";

	//ewpp_WriteLog("IPN", "execute sql", $Sql);
	$conn->Execute($Sql);
}

// Get item count in database
function ewpp_GetItemCount($itemno) {

	if ($itemno == "")
		return 0;
	else {
		$Sql = "SELECT ItemCount FROM " . ewpp_DbQuote(EWPP_TABLENAME_ITEM) .
			" WHERE ItemNumber = '" . ewpp_AdjustSql($itemno) . "'";
		return intval(ewpp_ExecuteScalar($Sql));
	}
}

// Get discount by discount code
function ewpp_GetDiscount($code) {

	$discount = 0;
	$Sql = "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_DISCOUNTCODE) .
		" WHERE Code = '" . ewpp_AdjustSql($code) . "'";
	$rs = ewpp_ExecuteRow($Sql);
	if ($rs) {
		$today = mktime(0, 0, 0);
		$start = strtotime($rs["CodeStart"]);
		$end = strtotime($rs["CodeEnd"]);
		if ($today >= $start && $today <= $end)
			return $rs["CodePercent"] . "|" . $rs["CodeAmount"];
	}
	return $discount;
}

// Update item count in database
function ewpp_UpdateItemCount($itemno, $qty) {

	global $conn;
	if ($itemno == "") return;
	if (strval($qty) == "" || !is_numeric($qty)) return;
	$Sql = "UPDATE " . ewpp_DbQuote(EWPP_TABLENAME_ITEM) . " SET " .
		"ItemCount = ItemCount - " . $qty .
		" WHERE ItemNumber = '" . ewpp_AdjustSql($itemno) . "'";
	$conn->Execute($Sql);
}

// Get server URL
function ewpp_GetServerUrl() {

	$url = "";
	if (ewpp_ServerVar("HTTPS") == "on") {
		$url .= "https://";
	} else {
		$url .= "http://";
	}
	return $url . ewpp_ServerVar("SERVER_NAME");
}

// Copy file to temp folder
function ewpp_CopyTempFile($folder, $file) {

	$filename = str_replace("/", EWPP_PATH_DELIMITER, $file);
	$i = strrpos($filename, EWPP_PATH_DELIMITER);
	if ($i !== FALSE)
		$filename = substr($filename, $i+1);

	// Copy file to temp folder
	$sFromPath = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . EWPP_DOWNLOAD_SRC_PATH . EWPP_PATH_DELIMITER . $file;
	$sToPath = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . $folder . EWPP_PATH_DELIMITER . $file;
	if (file_exists($sFromPath)) {
		if (!file_exists($sToPath))
			@copy($sFromPath, $sToPath);
		if (file_exists($sToPath)) {
			return ewpp_GetRootPath() . "/" . str_replace(EWPP_PATH_DELIMITER, "/", $folder) . "/" . $file;
		} else {
			return "";
		}
	} else {
		return "";
	}
}

// Delete old files and folders in download folder
function ewpp_CleanupOldFolders() {

	$sPath = trim(EWPP_DOWNLOAD_PATH);
	$sPath = str_replace("/", EWPP_PATH_DELIMITER, $sPath);
	$sPath = ewpp_IncludeTrailingDelimiter($sPath, TRUE);
	$sPath = ewpp_GetRootFolder() . EWPP_PATH_DELIMITER . $sPath; // Append current path
	if (file_exists($sPath))
		@ewpp_RemoveDirectory($sPath);
}

// Romove folder
function ewpp_RemoveDirectory($path) {

	if ($dir_handle = opendir($path)) {
		while ($file = readdir($dir_handle)) {
			if ($file == "." || $file == "..") {
				continue;
			} else {
				$filename = ewpp_IncludeTrailingDelimiter($path, TRUE) . $file;
				if (is_dir($filename)) {

					// Delete subfolder except the log folder
					if (strtolower($file) <> strtolower(substr(EWPP_LOG_PATH, -1*strlen($file)))) {
						ewpp_WriteLog("PURGE", "check directory", $filename);
						ewpp_RemoveDirectory($filename);
						if (@rmdir($filename)) // try remove empty dir
							ewpp_WriteLog("PURGE", "remove directory success", $filename);
					}
				} elseif (is_file($filename)) {
					if (ewpp_DateAdd(EWPP_DOWNLOAD_TIMEOUT_UNIT,
						EWPP_DOWNLOAD_TIMEOUT_INTERVAL,
						filemtime($filename)) < strtotime("now")) {
						if (@unlink($filename)) {
							ewpp_WriteLog("PURGE", "delete file success", $filename);
						} else {
							ewpp_WriteLog("PURGE", "delete file failure", $filename);
						}
					}
				}
			}
		}
		closedir($dir_handle);
		return TRUE; // All files deleted
	} else {
		return FALSE; // Directory doesn't exist
	}
} 

// Get physical root folder
function ewpp_GetRootFolder() {

	$path = realpath("."); // Get current folder
	if (EWPP_FOLDER_LEVEL > 0) {
		return ewpp_ParentPath($path, EWPP_FOLDER_LEVEL, EWPP_PATH_DELIMITER); // Up the required levels
	} else {
		return $path;
	}
}

// Get current path
function ewpp_GetCurrentPath() {

	$path = ewpp_ServerVar("PHP_SELF"); // Get current script name
	$p = strrpos($path, "/");
	if ($p !== FALSE)	$path = substr($path, 0, $p); // Remove script name
	return $path;
}

// Get root path
function ewpp_GetRootPath() {

	$path = ewpp_GetCurrentPath(); // Get current path
	if (EWPP_FOLDER_LEVEL > 0) {
		return ewpp_ParentPath($path, EWPP_FOLDER_LEVEL, "/"); // Up the required levels
	} else {
		return $path;
	}
}

// Get root path
function ewpp_RootPath() {

	return ewpp_GetRootPath() . "/"; // Return root path
}

// Get parent path
function ewpp_ParentPath($sPath, $iLevel, $sPathDlm) {

	$wrkpath = $sPath;
	for ($i = 1; $i <= $iLevel; $i++) {
		$p = strrpos($wrkpath, $sPathDlm);
		if ($p !== FALSE) $wrkpath = substr($wrkpath, 0, $p);
	}
	return $wrkpath;
}

//
//  Menu functions
//
// Load menu id
function ewpp_LoadMenuId() {

	global $ewpp_MenuId;
	if (@$_GET[EWPP_MENU_ID] <> "") {
		$ewpp_MenuId = $_GET[EWPP_MENU_ID];
		if (!is_numeric($ewpp_MenuId)) $ewpp_MenuId = "";
	}
}

// Load menu details
function ewpp_LoadMenu(&$rs) {

	global $ewpp_MenuId, $ewpp_MenuLink, $ewpp_MenuGen,
		$ewpp_MenuFn, $ewpp_MenuUrl, $ewpp_MenuParentId, $ewpp_MenuPageContent;
	if ($rs && !$rs->EOF) {
		$ewpp_MenuId = $rs->fields("MenuId");
		$ewpp_MenuLink = $rs->fields("MenuLink");
		if ($ewpp_MenuLink == "{{{Cart}}}")
			$ewpp_MenuLink = $GLOBALS["PPLanguage"]->Phrase("CartMenu");
		if ($ewpp_MenuLink == "{{{Categories}}}")
			$ewpp_MenuLink = $GLOBALS["PPLanguage"]->Phrase("CategoryMenu");
		$ewpp_MenuGen = ($rs->fields("MenuGen")) ? 1 : 0;
		$ewpp_MenuFn = EWPP_MENU_PAGE . "?" . EWPP_MENU_ID . "=" . $ewpp_MenuId;
		$ewpp_MenuUrl = $rs->fields("MenuUrl");
		$ewpp_MenuParentId = $rs->fields("MenuParentId");
		if ($ewpp_MenuParentId == "") $ewpp_MenuParentId = -1;
		$ewpp_MenuPageContent = $rs->fields("MenuPageContent");
	}
}

//
//  Shopping cart functions
//
// Load category by ID
function ewpp_LoadCatById() {

	global $ewpp_CatId, $conn;

	// Load category id
	if (@$_GET[EWPP_CATEGORY_ID] <> "") {
		$ewpp_CatId = $_GET[EWPP_CATEGORY_ID];
		if (!is_numeric($ewpp_CatId)) $ewpp_CatId = "";
	} else {
		$ewpp_CatId = @$_SESSION[EWPP_SESSION_CATEGORY_ID];
	}

	// Load category info
	if ($ewpp_CatId <> "") {
		$sSql = str_replace("@@CategoryId@@", $ewpp_CatId, EWPP_CATEGORY_CATEGORYID_FILTER);
		$sSql = EWPP_CATEGORY_SELECT_SQL . " WHERE " . $sSql;
		$rs = $conn->Execute($sSql);
		ewpp_LoadCat($rs);
		if ($rs)
			$rs->Close();
		$_SESSION[EWPP_SESSION_CATEGORY_ID] = $ewpp_CatId;
	}
}

// Load category
function ewpp_LoadCat(&$rs) {

	global $PPLanguage, $ewpp_CatId, $ewpp_CatName, $ewpp_CatPath, $ewpp_CatFn, $conn;
	$ewpp_CatPath = "";
	$ewpp_CatFn = "";
	if ($rs && !$rs->EOF) {
		$ewpp_CatId = $rs->fields("CategoryId");
		$ewpp_CatName = $rs->fields("CategoryName");
		$ewpp_CatPath .= $ewpp_CatName;
		$parent = $rs->fields("CategoryParentId");
		$rs->Close();

		// Build the category path
		while ($parent > 0) {
			$sSql = EWPP_CATEGORY_SELECT_SQL . " WHERE CategoryId=" . $parent;
			$parent = -1; // Reset
			$rs = $conn->Execute($sSql);
			if ($rs && !$rs->EOF) {
				$ewpp_CatFn = EWPP_CART_LIST_PAGE . "?" . EWPP_CATEGORY_ID . "=" . $rs->fields("CategoryId") . "," . $ewpp_CatFn;
				$ewpp_CatPath = $rs->fields("CategoryName") . "," . $ewpp_CatPath;
				$parent = $rs->fields("CategoryParentId");
				$rs->Close();
			}
		}
	}
}

// Load sub category list
function ewpp_LoadSubCatList($catid) {

	global $conn;
	$sCurSubCatList = $catid;
	$sSubCatList = "";
	while ($sSubCatList <> $sCurSubCatList) {
		if ($sSubCatList <> "") $sCurSubCatList = $sSubCatList;
		$sSql = EWPP_PRODUCT_SELECT_SUBCATEGORY_SQL;
		$sWhere = str_replace("@@CategoryParentId@@", $sCurSubCatList, EWPP_PRODUCT_SUBCATEGORY_FILTER);
		$sSql .= " WHERE " . $sWhere . " ORDER BY CategoryId";
		$sSubCatList = $catid;
		$RsSubCat = $conn->Execute($sSql);
		if ($RsSubCat) {
			while (!$RsSubCat->EOF) {
				$sSubCatList .= "," . $RsSubCat->fields("CategoryId");
				$RsSubCat->MoveNext();
			}
		}
		$RsSubCat->Close();
	}
	return $sSubCatList;
}

// Load item id
function ewpp_GetItemId() {

	global $ewpp_Item;
	if (@$_GET[EWPP_ITEM_ID] <> "") {
		$ewpp_Item["ItemId"] = $_GET[EWPP_ITEM_ID];
		if (!is_numeric($ewpp_Item["ItemId"])) $ewpp_Item["ItemId"] = "";
	}
}

// Load product details
function ewpp_LoadProduct(&$rs) {

	global $ewpp_Item;
	if ($rs && !$rs->EOF)
		$ewpp_Item = $rs->fields;
}

// Set up pager position
function ewpp_LoadPagerPosition() {

	global $ewpp_DisplayRecs, $ewpp_PageNumber, $ewpp_StartRec, $ewpp_TotalPages;

	// Exit if DisplayRecs = 0
	if ($ewpp_DisplayRecs == 0) return;

	// Check for a START parameter
	if (@$_GET[EWPP_START_REC] <> "") {
		$nStart = $_GET[EWPP_START_REC];
		if (is_numeric($nStart)) {
			$nPage = intval($nStart/$ewpp_DisplayRecs);
			if ($nStart % $ewpp_DisplayRecs > 0) $nPage++;
		}

	// Check for a PAGE parameter
	} elseif (@$_GET[EWPP_PAGE_NO] <> "") {
		$nPage = $_GET[EWPP_PAGE_NO];

	// Restore from Session
	} else {
		$nPage = @$_SESSION[EWPP_SESSION_PAGE_NO];
	}

	// Set up page number and start record position
	if (is_numeric($nPage)) {
		if ($nPage <= 0 || $nPage > $ewpp_TotalPages) $nPage = 1;
		$ewpp_PageNumber = $nPage;
		$ewpp_StartRec = ($ewpp_PageNumber - 1) * $ewpp_DisplayRecs + 1;

		// Save to session
		$_SESSION[EWPP_SESSION_PAGE_NO] = $nPage;
	}
}

// Get normalized image file name
function ewpp_ImageName($fn, $imgtype) {

	if (trim(strval($fn)) == "")
		return "";
	$i = strrpos($fn, "\\");
	if ($i !== FALSE)
		$fn = substr($fn, $i+1);
	$path_parts = pathinfo($fn);
	$wrkext = strtolower($path_parts['extension']);
	if ($wrkext == "swf") { // swf
		$fn = preg_replace('/\s/', "_", $path_parts['basename']); // 5.0
	} else { // Assume image
		$fn = preg_replace('/\s/', "_", $imgtype . $path_parts['basename']); // 5.0
	}
	return $fn;
}

// Get image real path
function ewpp_ImageRealPath($fn, $imgtype) {

	if (trim(strval($fn)) == "")
		return FALSE;
	$imgfn = ewpp_ImageName($fn, $imgtype);
	$imgfn = ((EWPP_IMAGE_PATH == "") ? "" : EWPP_IMAGE_PATH . "/") . $imgfn;
	return realpath($imgfn);
}

// Get image relative path
function ewpp_ImageHref($fn, $imgtype) {

	global $ewpp_Item;
	if (EWPP_LOWERCASE_FILENAME) // 501
		$fn = strtolower($fn);
	if (file_exists(ewpp_ImageRealPath($fn, $imgtype))) {
		return EWPP_IMAGE_PATH . "/" . ewpp_ImageName($fn, $imgtype);
	} else {
		return "";
	}
}

// Include mobiledetect.php
include_once("mobile_detect.php");

// Check if mobile device
 function ewpp_IsMobile() {

     global $MobileDetect;
     if (!isset($MobileDetect))
         $MobileDetect = new Mobile_Detect;
     return $MobileDetect->isMobile();
 } 

// Get image tag
function ewpp_ImageTag($id, $fn, $imgtype, $width, $height, $multi=FALSE) {

	global $EWPP_PAGE_ID, $PPLanguage;
	if (EWPP_LOWERCASE_FILENAME)
		$fn = strtolower($fn);
	$wrkfn = ewpp_ImageHref($fn, $imgtype);
	if ($wrkfn <> "") {
		$path_parts = pathinfo($fn);
		$wrkext = strtolower($path_parts['extension']);
		if ($wrkext == "swf") { // handle swf
			$wrkid = "swf_" . $id;
			if ($width <= 0)
				$width = EWPP_SWF_DEFAULT_WIDTH;
			if ($height <= 0)
				$height = EWPP_SWF_DEFAULT_HEIGHT;
			$info = getimagesize(ewpp_ImageRealPath($fn, $imgtype));
			if ($info[0] > 0 && $info[1] > 0) {
				$width = $info[0];
				$height = $info[1];
			}
			$params = "{wmode: 'transparent'}";
			$cbf = "function(e) { if (e.success) { var div = jQuery('#' + e.id).parent(); var a = div.parent(); var o = {rel: a.attr('rel'), html: div.html(), innerWidth: $width, innerHeight: $height}; if (P.COLORBOX_CONFIG) P.DB.mergeObj(o, P.COLORBOX_CONFIG); a.colorbox(o); }}";
			if (!ewpp_IsMobile()) {
				return "<div><div id='" . $wrkid . "'></div></div>" .
				"<script type='text/javascript'>swfobject.embedSWF('" . $wrkfn . "', '" . $wrkid . "', '" . $width . "', '" . $height . "', P.SWF_VERSION, '', null, " . $params . ", " . $params . ", " . $cbf . ");</script>";
			}
		} else { // assume image
			if ($EWPP_PAGE_ID == "list" && EWPP_REC_PER_ROW == 0) {
				$attrs = array("alt" => $PPLanguage->Phrase("ClickToEnlarge"), "title" => $PPLanguage->Phrase("ClickToEnlarge"), "src" => $wrkfn, "class" => "ewImage hidden-xs");
			} else {
				$attrs = array("alt" => $PPLanguage->Phrase("ClickToEnlarge"), "title" => $PPLanguage->Phrase("ClickToEnlarge"), "src" => $wrkfn, "class" => "ewImage");
			}
			if ($width > 0)
				$attrs["width"] = $width;
			if ($height > 0)
				$attrs["height"] = $height;
			$imgattr = ewpp_HtmlElement("img", $attrs, "", FALSE);
			if ($EWPP_PAGE_ID == "list" && EWPP_REC_PER_ROW == 0) {
				$attrs = array("alt" => $PPLanguage->Phrase("ClickToEnlarge"), "title" => $PPLanguage->Phrase("ClickToEnlarge"), "src" => $wrkfn, "class" => "ewImage visible-xs");
				if (EWPP_IMAGE_THUMBNAIL_WIDTH_MOBILE > 0) {
					$attrs["width"] = EWPP_IMAGE_THUMBNAIL_WIDTH_MOBILE;
				} else {
					$attrs["width"] = $width;
				}
				if (EWPP_IMAGE_THUMBNAIL_HEIGHT_MOBILE > 0) {
					$attrs["height"] = EWPP_IMAGE_THUMBNAIL_HEIGHT_MOBILE;
				}		
				return $imgattr .= ewpp_HtmlElement("img", $attrs, "", FALSE);	
			} else {
				return $imgattr;
			}	
		}
	} else {
		return "";
	}
}

// Format description
function ewpp_FormatDescription($desc) {

	if (EWPP_REPLACE_CRLF) {
		$desc = str_replace("\r\n", "<br>", strval($desc));
		$desc = str_replace("\r", "<br>", $desc);
		$desc = str_replace("\n", "<br>", $desc);
	}
	return $desc;
}

// Format option
function ewpp_FormatOption($id, $optype, $op, $def) {

	switch (strtoupper($optype)) {
		case "SELECT-ONE":
			return ewpp_SelectOneView($id, $op, $def);
		case "RADIO":
			return ewpp_RadioView($id, $op, 0, $def);
		case "CHECKBOX":
			return ewpp_CheckboxView($id, $op, 0, $def);
		case "SELECT-MULTIPLE":
			return ewpp_SelectMultipleView($id, $op, 0, $def);
		case "TEXT":
			return ewpp_TextView($id, $def, EWPP_OPTION_TEXTBOX_SIZE, EWPP_OPTION_TEXTBOX_MAXLEN, 0);
		default:
			return "";
	}
}

// Display option as selection list
function ewpp_SelectView($name, $data, $type, $size, $def) {

	global $PPLanguage;
	$outstr = "<select name=\"" . $name . "\" class=\"ewUpdatePriceOnChange form-control\"";
	if ($type == EWPP_OPTION_SELECT_MULTIPLE) {
		$outstr .= " multiple=\"multiple\" size=\"" . $size . "\"";
		$defs = explode(",", $def);
	} else {
		$defs = array($def);
	}
	$outstr .= ">";
	$arOptions = explode("/", $data);
	$cnt = count($arOptions);
	if ($cnt > 0) {
		if ($type <> EWPP_OPTION_SELECT_MULTIPLE)
			$outstr .= "<option value=\"" . ewpp_HtmlEncode($PPLanguage->Phrase("OptionNone")) . "\">" . $PPLanguage->Phrase("OptionPleaseSelect") .  "</option>";
		for ($i=0; $i<$cnt; $i++) {
			$arOption = explode("=", $arOptions[$i]);
			$outstr .= "<option value=\"" . ewpp_HtmlEncode($arOptions[$i]) . "\"";
			if (in_array($arOption[0], $defs))
				$outstr .= " selected=\"selected\"";
			$outstr .= ">" . $arOption[0] .  "</option>";
		}
	}
	$outstr .= "</select>";
	return $outstr;
}

// Display option as combobox
function ewpp_SelectOneView($name, $data, $def) {

	return ewpp_SelectView($name, $data, EWPP_OPTION_SELECT_ONE, 0, $def);
}

// Display option as listbox
function ewpp_SelectMultipleView($name, $data, $size, $def) {

	if ($size < 1)
		$size = EWPP_OPTION_SELECT_MULTIPLE_SIZE;
	return ewpp_SelectView($name, $data, EWPP_OPTION_SELECT_MULTIPLE, $size, $def);
}

// Display option as input="checkbox"/"radio"
function ewpp_InputView($name, $type, $data, $col, $def) {

	$outstr = "";
	$arOptions = explode("/", $data);
	$cnt = count($arOptions);
	if ($cnt > 0) {
		if ($col < 1)
			$col = EWPP_OPTION_REPEAT_COLUMN;
		$outstr .= "<table cellspacing=\"0\" class=\"ewOptionTable\">";
		for ($i=0; $i<$cnt; $i++) {
			if ($i == 0 || ($i > 1 && $i % $col == 0))
				$outstr .= "<tr>";
			$arOption = explode("=", $arOptions[$i]); 
			$outstr .= "<td><label><input type=\"" . $type . "\" class=\"ewUpdatePriceOnClick\"";
			$outstr .= " name=\"" . $name . "\" value=\"" . ewpp_HtmlEncode($arOptions[$i]) . "\"";
			if ($arOption[0] == $def)
				$outstr .= " checked=\"checked\"";	
			$outstr .= ">" . $arOption[0] . "</label></td>";
			if (($i+1)%$col == 0 || $i == $cnt)
				$outstr .= "</tr>";
		}
		$outstr .= "</table>";
	}
	return $outstr;
}

// Display option as checkboxes 
function ewpp_CheckboxView($name, $data, $col, $def) {

	return ewpp_InputView($name, "checkbox", $data, $col, $def);
}

// Display option as radio buttons
function ewpp_RadioView($name, $data, $col, $def) {

	return ewpp_InputView($name, "radio", $data, $col, $def);
}

// Display textbox
function ewpp_TextView($name, $val, $size, $maxlen, $disable) {

	$outstr = "<input type=\"text\" name=\"" . $name . "\" value=\"" .
		ewpp_HtmlEncode($val) . "\" size=\"" . $size. "\" maxlength=\"" . $maxlen . "\"";
	if ($disable == "1")
		$outstr .= " disabled=\"disabled\"";
	$outstr .= ">";
	return $outstr;
}

// FormatCurrency
function ewpp_FormatCurrency($amount) {

	return ewpp_FormatCurrencyEx($amount, EWPP_DEFAULT_FRAC_DIGITS, -2, -2, -1);
}

// FormatCurrency (Extended)
//ewpp_FormatCurrencyEx(Expression[,NumDigitsAfterDecimal [,IncludeLeadingDigit
// [,UseParensForNegativeNumbers [,GroupDigits]]]])
//NumDigitsAfterDecimal is the numeric value indicating how many places to the
//right of the decimal are displayed
//-1 Use Default
//The IncludeLeadingDigit, UseParensForNegativeNumbers, and GroupDigits
//arguments have the following settings:
//-1 True
//0 False
//-2 Use Default
function ewpp_FormatCurrencyEx($amount, $NumDigitsAfterDecimal = -1, $IncludeLeadingDigit = -2, $UseParensForNegativeNumbers = -2, $GroupDigits = -2) {
	// Export the values returned by localeconv into the local scope

	if (!EWPP_USE_DEFAULT_LOCALE) extract(localeconv());

	// Set defaults if locale is not set
	if (empty($currency_symbol)) $currency_symbol = EWPP_DEFAULT_CURRENCY_SYMBOL;
	if (empty($mon_decimal_point)) $mon_decimal_point = EWPP_DEFAULT_MON_DECIMAL_POINT;
	if (empty($mon_thousands_sep)) $mon_thousands_sep = EWPP_DEFAULT_MON_THOUSANDS_SEP;
	if (empty($positive_sign)) $positive_sign = EWPP_DEFAULT_POSITIVE_SIGN;
	if (empty($negative_sign)) $negative_sign = EWPP_DEFAULT_NEGATIVE_SIGN;
	if (empty($frac_digits) || $frac_digits == CHAR_MAX) $frac_digits = EWPP_DEFAULT_FRAC_DIGITS;
	if (empty($p_cs_precedes) || $p_cs_precedes == CHAR_MAX) $p_cs_precedes = EWPP_DEFAULT_P_CS_PRECEDES;
	if (empty($p_sep_by_space) || $p_sep_by_space == CHAR_MAX) $p_sep_by_space = EWPP_DEFAULT_P_SEP_BY_SPACE;
	if (empty($n_cs_precedes) || $n_cs_precedes == CHAR_MAX) $n_cs_precedes = EWPP_DEFAULT_N_CS_PRECEDES;
	if (empty($n_sep_by_space) || $n_sep_by_space == CHAR_MAX) $n_sep_by_space = EWPP_DEFAULT_N_SEP_BY_SPACE;
	if (empty($p_sign_posn) || $p_sign_posn == CHAR_MAX) $p_sign_posn = EWPP_DEFAULT_P_SIGN_POSN;
	if (empty($n_sign_posn) || $n_sign_posn == CHAR_MAX) $n_sign_posn = EWPP_DEFAULT_N_SIGN_POSN;

	// Check $NumDigitsAfterDecimal
	if ($NumDigitsAfterDecimal > -1)
		$frac_digits = $NumDigitsAfterDecimal;

	// Check $UseParensForNegativeNumbers
	if ($UseParensForNegativeNumbers == -1) {
		$n_sign_posn = 0;
		if ($p_sign_posn == 0) {
			if (EWPP_DEFAULT_P_SIGN_POSN != 0)
				$p_sign_posn = EWPP_DEFAULT_P_SIGN_POSN;
			else
				$p_sign_posn = 3;
		}
	} elseif ($UseParensForNegativeNumbers == 0) {
		if ($n_sign_posn == 0)
			if (EWPP_DEFAULT_P_SIGN_POSN != 0)
				$n_sign_posn = EWPP_DEFAULT_P_SIGN_POSN;
			else
				$n_sign_posn = 3;
	}

	// Check $GroupDigits
	if ($GroupDigits == -1) {
		$mon_thousands_sep = EWPP_DEFAULT_MON_THOUSANDS_SEP;
	} elseif ($GroupDigits == 0) {
		$mon_thousands_sep = "";
	}

	// Start by formatting the unsigned number
	$number = number_format(abs($amount),
							$frac_digits,
							$mon_decimal_point,
							$mon_thousands_sep);

	// Check $IncludeLeadingDigit
	if ($IncludeLeadingDigit == 0) {
		if (substr($number, 0, 2) == "0.")
			$number = substr($number, 1, strlen($number)-1);
	}
	if ($amount < 0) {
		$sign = $negative_sign;

		// "Extracts" the boolean value as an integer
		$n_cs_precedes  = intval($n_cs_precedes  == true);
		$n_sep_by_space = intval($n_sep_by_space == true);
		$key = $n_cs_precedes . $n_sep_by_space . $n_sign_posn;
	} else {
		$sign = $positive_sign;
		$p_cs_precedes  = intval($p_cs_precedes  == true);
		$p_sep_by_space = intval($p_sep_by_space == true);
		$key = $p_cs_precedes . $p_sep_by_space . $p_sign_posn;
	}
	$formats = array(

	  // Currency symbol is after amount
	  // No space between amount and sign

	  '000' => '(%s' . $currency_symbol . ')',
	  '001' => $sign . '%s ' . $currency_symbol,
	  '002' => '%s' . $currency_symbol . $sign,
	  '003' => '%s' . $sign . $currency_symbol,
	  '004' => '%s' . $sign . $currency_symbol,

	  // One space between amount and sign
	  '010' => '(%s ' . $currency_symbol . ')',
	  '011' => $sign . '%s ' . $currency_symbol,
	  '012' => '%s ' . $currency_symbol . $sign,
	  '013' => '%s ' . $sign . $currency_symbol,
	  '014' => '%s ' . $sign . $currency_symbol,

	  // Currency symbol is before amount
	  // No space between amount and sign

	  '100' => '(' . $currency_symbol . '%s)',
	  '101' => $sign . $currency_symbol . '%s',
	  '102' => $currency_symbol . '%s' . $sign,
	  '103' => $sign . $currency_symbol . '%s',
	  '104' => $currency_symbol . $sign . '%s',

	  // One space between amount and sign
	  '110' => '(' . $currency_symbol . ' %s)',
	  '111' => $sign . $currency_symbol . ' %s',
	  '112' => $currency_symbol . ' %s' . $sign,
	  '113' => $sign . $currency_symbol . ' %s',
	  '114' => $currency_symbol . ' ' . $sign . '%s');

  // Lookup the key in the above array
	return sprintf($formats[$key], $number);
}

// Load alphanumeric index
function ewpp_LoadAlpha() {

	global $ewpp_Alpha, $ewpp_PagingIndexes;

	// Load alpha parameter
	if (strval(@$_GET[EWPP_ALPHA_ID]) <> "") {
		$ewpp_Alpha = $_GET[EWPP_ALPHA_ID];
	} else {
		$ewpp_Alpha = @$_SESSION[EWPP_SESSION_ALPHA_ID];
	}

	// Make sure alpha is non-empty
	if ($ewpp_Alpha <> "") {
		$i = strpos(EWPP_PRODUCT_ALPHANUMERIC_INDEX, $ewpp_Alpha);
		if ($i <> FALSE && $i >= 0 && $i < count($ewpp_PagingIndexes)) {
			$i = $ewpp_PagingIndexes[$i];
			if ($i <= 0) $ewpp_Alpha = "";
		} else {
			$ewpp_Alpha = "";
		}
	}

	// Load default = first non-empty entry in index if not specified
	if ($ewpp_Alpha == "") {
		for ($i = 0; $i < count($ewpp_PagingIndexes); $i++) {
			if ($ewpp_PagingIndexes[$i] > 0) {
				$ewpp_Alpha = substr(EWPP_PRODUCT_ALPHANUMERIC_INDEX, $i, 1);
				break;
			}
		}
	}

	// Save current alpha
	$_SESSION[EWPP_SESSION_ALPHA_ID] = $ewpp_Alpha;
}

// Build paging index
function ewpp_BuildPagingIndex($filter) {

	global $conn, $ewpp_PagingIndexes;
	$ewpp_PagingIndexes = array();
	for ($i = 0; $i < strlen(EWPP_PRODUCT_ALPHANUMERIC_INDEX); $i++) {
		$sAlpha = substr(EWPP_PRODUCT_ALPHANUMERIC_INDEX, $i, 1);
		$ewpp_PagingIndexes[] = intval(ewpp_ExecuteScalar(ewpp_PagingSql($sAlpha, $filter)));
	}
}

// Get paging SQL based on index (to alphanumeric index)
function ewpp_PagingSql($alpha, $filter) {

	global $ewpp_CatId;
	$sFilter = ewpp_PagingSqlFilter($alpha);
	if ($filter <> "") {
		if ($sFilter <> "") $sFilter .= " AND ";
		$sFilter .= $filter;
	}
	$ewpp_PagingSql = EWPP_PRODUCT_SELECT_COUNT_SQL;
	if ($sFilter <> "")
		$ewpp_PagingSql .= " WHERE " . $sFilter;
	return $ewpp_PagingSql;
}

// Get paging SQL filter
function ewpp_PagingSqlFilter($alpha) {

	if ($alpha == "~") {
		$sWrk = "";
		for ($i = 0; $i < strlen(EWPP_PRODUCT_ALPHANUMERIC_INDEX); $i++) {
			$sAlpha = substr(EWPP_PRODUCT_ALPHANUMERIC_INDEX, $i, 1);
			if ($sAlpha <> "~") {
				if ($sWrk <> "") $sWrk .= " OR ";
				$sWrk .= str_replace("@@alpha@@", $sAlpha, EWPP_PRODUCT_ALPHANUMERIC_FILTER);
			}
		}
		if ($sWrk <> "") $sWrk = "NOT (" . $sWrk . ")";
		return $sWrk;
	} elseif ($alpha <> "") {
		return str_replace("@@alpha@@", $alpha, EWPP_PRODUCT_ALPHANUMERIC_FILTER);
	}
}

// Get JSON by SQL // 5.0
function ewpp_TableToJson($tblname) {

	global $conn;
	$meta = NULL;
	if (EWPP_DB_TYPE == "SQLITE" || EWPP_DB_TYPE == "MYSQL") // SQLite/MySQL
		$meta = $conn->MetaColumns(EWPP_TABLENAME_PREFIX . strtolower($tblname));
	$wrkstr = "[";

	// Execute query
	if ($tblname == "Country") { // 502
		$sql = EWPP_COUNTRYLIST_SQL;
	} elseif ($tblname == "State") {
		$sql = EWPP_STATELIST_SQL;
	} elseif ($tblname == "Discount") {
		$sql = EWPP_DISCOUNTLIST_SQL;
	} elseif ($tblname == "DiscountType") {
		$sql = EWPP_DISCOUNTTYPELIST_SQL;
	} elseif ($tblname == "ShippingMethod") {
		$sql = EWPP_SHIPMETHODLIST_SQL;
	} elseif ($tblname == "ShippingType") {
		$sql = EWPP_SHIPTYPELIST_SQL;
	} elseif ($tblname == "Tax") {
		$sql = EWPP_TAXLIST_SQL;
	} else {
		$sql = "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_PREFIX . strtolower($tblname));
	}
	$rs = $conn->Execute($sql);

	// Build output
	while (!$rs->EOF) {
		if ($wrkstr <> "[") $wrkstr .= ","; // Add record separator
		$row = "{";
		foreach ($rs->fields as $key => $value) {
			if (!is_numeric($key)) {
				$fldtype = ($meta && array_key_exists(strtoupper($key), $meta)) ? strtolower($meta[strtoupper($key)]->type) : ""; // 502
				$fldsize = ($meta && array_key_exists(strtoupper($key), $meta)) ? $meta[strtoupper($key)]->max_length : -1; // 502
				if ($row <> "{") $row .= ","; // Add field separator
				$row .= $key . ":";
				if (is_null($value)) { // null
					$row .= "null";
				} elseif ($value === TRUE || ($fldtype == "bit" && $value === "1") || ($fldtype == "tinyint" && $fldsize == "1" && $value === "1")) { // boolean
					$row .= "true";
				} elseif ($value === FALSE || ($fldtype == "bit" && $value === "0") || ($fldtype == "tinyint" && $fldsize == "1" && $value === "0")) { // boolean
					$row .= "false";
				} elseif (is_int($value) || is_float($value) || $fldtype == "integer" || $fldtype == "real" || $fldtype == "int" || $fldtype == "double") { // number
					$row .= $value;
				} elseif (is_string($value)) { // string (default)
					$row .= "\"" . ewpp_JsEncode2($value) . "\"";
				} else { 
					$row .= $value;
				}
			}
		}
		$row .= "}";
		$wrkstr .= $row;
		$rs->MoveNext();
	}
	$wrkstr .= "]";	
	return $wrkstr;
}

// Connection/Query error handler
function ewpp_ErrorFn($DbType, $ErrorType, $ErrorNo, $ErrorMsg, $Param1, $Param2, $Object) {

	if ($ErrorType == 'CONNECT') {
		$msg = "Failed to connect to $Param2 at $Param1. Error: " . $ErrorMsg;
	} elseif ($ErrorType == 'EXECUTE') {
		$msg = "Failed to execute SQL: $Param1. Error: " . $ErrorMsg;
	} 
	echo $msg;
	die();
}

// Connect to database
function &ewpp_Connect() {

	$GLOBALS["ADODB_FETCH_MODE"] = ADODB_FETCH_BOTH;
	if (EWPP_DB_TYPE == "SQLITE") {
		$GLOBALS["ADODB_COUNTRECS"] = TRUE;
		$conn = ADONewConnection('pdo');
		$conn->debug = EWPP_DEBUG_ENABLED;
		$conn->raiseErrorFn = 'ewpp_ErrorFn';
		$conn->Connect("sqlite:" . realpath(EWPP_DB_PATH . "/" . EWPP_DB_FILENAME), "", "", "");
	} elseif (EWPP_DB_TYPE == "ACCESS") {
		$GLOBALS["ADODB_COUNTRECS"] = FALSE;
		$conn = ADONewConnection('ado_access');
		$conn->debug = EWPP_DEBUG_ENABLED;
		$info = "Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" . realpath(EWPP_DB_PATH . "/" . EWPP_DB_FILENAME);
		if (EWPP_CODEPAGE > 0)
			$conn->charPage = EWPP_CODEPAGE;
		$conn->raiseErrorFn = 'ewpp_ErrorFn';
		$conn->Connect($info, FALSE, FALSE);
	} elseif (EWPP_DB_TYPE == "MYSQL") {
		$GLOBALS["ADODB_COUNTRECS"] = FALSE;
		$conn = ADONewConnection('mysqlt');
		$conn->debug = EWPP_DEBUG_ENABLED;
		$conn->port = EWPP_CONN_PORT;
		$conn->raiseErrorFn = 'ewpp_ErrorFn';
		$conn->Connect(EWPP_CONN_HOST, EWPP_CONN_USER, EWPP_CONN_PASS, EWPP_CONN_DB);
		if (EWPP_MYSQL_CHARSET <> "") // 502
			$conn->Execute("SET NAMES '" . EWPP_MYSQL_CHARSET . "'");
	}
	if (!EWPP_DEBUG_ENABLED)
		$conn->raiseErrorFn = '';
	return $conn;
}

// Get server variable by name
function ewpp_ServerVar($name) {

	$str = @$_SERVER[$name];
	if (empty($str)) $str = @$_ENV[$name];
	return $str;
}

// Get POST data
function ewpp_PostVar($key) {

	return ewpp_StripSlashes(@$_POST[$key]);
}

// Get all POST data
function ewpp_PostVars() {

	if (@$HTTP_RAW_POST_DATA <> "") {
		return $HTTP_RAW_POST_DATA;
	} elseif (isset($_POST)) {
		$data = ewpp_StripSlashes($_POST);
		$str = "";
		foreach ($data as $key => $value) {
			if ($str <> "") $str .= "&";
			$str .= $key . "=" . urlencode($value);
		}
		return $str;
	}
	return "";
}

// Get GET data
function ewpp_GetVar($key) {

	return ewpp_StripSlashes(@$_GET[$key]);
}

// Get all GET data
function ewpp_GetVars() {

	if (isset($_GET)) {
		$data = ewpp_StripSlashes($_GET);
		$str = "";
		foreach ($data as $key => $value) {
			if ($str <> "") $str .= "&";
			$str .= $key . "=" . urlencode($value);
		}
		return $str;
	}
	return "";
}

// Get script name
function ewpp_ScriptName() {

	$sn = ewpp_ServerVar("PHP_SELF");
	if (empty($sn)) $sn = ewpp_ServerVar("SCRIPT_NAME");
	if (empty($sn)) $sn = ewpp_ServerVar("ORIG_PATH_INFO");
	if (empty($sn)) $sn = ewpp_ServerVar("ORIG_SCRIPT_NAME");
	if (empty($sn)) $sn = ewpp_ServerVar("REQUEST_URI");
	if (empty($sn)) $sn = ewpp_ServerVar("URL");
	if (empty($sn)) $sn = "UNKNOWN";
	return $sn;
}

// Function for debug
function ewpp_Trace($msg) {

	$filename = "debug.txt";
	if (!$handle = fopen($filename, 'a'))
		exit();
	if (is_writable($filename))
		fwrite($handle, $msg . "\n");
	fclose($handle);
}

// Executes the query, and returns the first column of the first row
function ewpp_ExecuteScalar($SQL) {

	global $conn;
	if ($conn) {
		if ($rs = $conn->Execute($SQL)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$res = $rs->fields[0];
				$rs->Close();
				return $res;
			}
		}
	}
	return NULL;
}

// Executes the query, and returns the first row
function ewpp_ExecuteRow($SQL) {

	global $conn;
	if ($conn) {
		if ($rs = $conn->Execute($SQL)) {
			if (!$rs->EOF)
				return $rs->fields;
		}
	}
	return NULL;
}

// Function to include the last delimiter for a path
function ewpp_IncludeTrailingDelimiter($Path, $PhyPath) {

	if ($PhyPath) {
		if (substr($Path, -1) <> EWPP_PATH_DELIMITER) $Path .= EWPP_PATH_DELIMITER;
	} else {
		if (substr($Path, -1) <> "/") $Path .= "/";
	}
	return $Path;
}

// Strip slashes
function ewpp_StripSlashes($value) {

	if (!get_magic_quotes_gpc())
		return $value;
	if (is_array($value)) { 
		return array_map('ewpp_StripSlashes', $value);
	} else {
		return stripslashes($value);
	}
}

// Add slashes for SQL
function ewpp_AdjustSql($val) {

	$val = trim($val);
	if (EWPP_REMOVE_XSS)
		$val = ewpp_RemoveXSS($val);
	if (EWPP_DB_TYPE == "MYSQL") {
		$val = addslashes($val);
	} elseif (EWPP_DB_TYPE == "ACCESS") {
		$val = str_replace("'", "''", $val); // Adjust for single quote
		$val = str_replace("[", "[[]", $val); // Adjust for open square bracket
	} elseif (EWPP_DB_TYPE == "SQLITE") {
		$val = str_replace("'", "''", $val); // Adjust for single quote

		//$val = ewpp_ConvertToUtf8($val); // Convert to utf-8
	}
	return $val;
}

// HTML encode
function ewpp_HtmlEncode($exp) {

	return htmlspecialchars(strval($exp));
}

// Encode string for double-quoted Javascript string
function ewpp_JsEncode($val) {

	$val = str_replace("\\", "\\\\", strval($val));
	$val = str_replace("\"", "\\\"", $val);
	return $val;
}

// Encode value for double-quoted Javascript string
function ewpp_JsEncode2($val) {

	$val = ewpp_JsEncode($val);
	$val = str_replace("\r\n", "<br>", $val);
	$val = str_replace("\r", "<br>", $val);
	$val = str_replace("\n", "<br>", $val);
	return $val;
}

// Convert boolean to integer
function ewpp_BoolToInt($val) {

	if (is_bool($val)) {
		return ($val) ? 1 : 0;
	} else {
		return ($val <> 0) ? 1 : 0;
	}
}

// Change file extension
function ewpp_ChangeFileExt($fn) {

	$ext = 'php';
	$p = strrpos($fn, ".");
	$fn = ($p !== FALSE) ? substr($fn, 0, $p+1) . $ext : $fn . "." . $ext;
	return (EWPP_LOWERCASE_FILENAME) ? strtolower($fn) : $fn;
}

/**
 * Menu class
 */
class cpMenu {
	var $Id;
	var $IsCat = FALSE;
	var $IsMLink = FALSE;
	var $IsRoot = FALSE;
	var $NoItem = NULL;
	var $ItemData = array();

	function cpMenu($id, $cat = FALSE, $link = FALSE) {
		$this->Id = $id;
		$this->IsCat = $cat;
		$this->IsMLink = $link;
	}

	// Add a menu item
	function AddMenuItem($id, $text, $gen, $fn, $url, $parentid, $src = "", $allowed = TRUE, $grouptitle = FALSE) { // 5.0

		$item = new cpMenuItem($id, $text, ($gen)?$fn:$url, $parentid, $src, $allowed, $grouptitle); // 5.0
		if ($item->ParentId < 0) {
			$this->AddItem($item);
		} else {
			if ($oParentMenu =& $this->FindItem($item->ParentId))
				$oParentMenu->AddItem($item, $this->IsCat, $this->IsMLink);
		}
	}

	// Add item to internal array
	function AddItem($item) {

		$this->ItemData[] = $item;
	}

	// Clear all menu items
	function Clear() {

		$this->ItemData = array();
	}

	// Find item
	function &FindItem($id) {

		$cnt = count($this->ItemData);
		for ($i = 0; $i < $cnt; $i++) {
			$item =& $this->ItemData[$i];
			if ($item->Id == $id) {
				return $item;
			} elseif (!is_null($item->SubMenu)) {
				if ($subitem =& $item->SubMenu->FindItem($id))
					return $subitem;
			}
		}
		$noitem = $this->NoItem;
		return $noitem;
	}

	// Get menu item count
	function Count() {

		return count($this->ItemData);
	}

	// Move item to position
	function MoveItem($Text, $Pos) {

		$cnt = count($this->ItemData);
		if ($Pos < 0) {
			$Pos = 0;
		} elseif ($Pos >= $cnt) {
			$Pos = $cnt - 1;
		}
		$item = NULL;
		$cnt = count($this->ItemData);
		for ($i = 0; $i < $cnt; $i++) {
			if ($this->ItemData[$i]->Text == $Text) {
				$item = $this->ItemData[$i];
				break;
			}
		}
		if ($item) {
			unset($this->ItemData[$i]);
			$this->ItemData = array_merge(array_slice($this->ItemData, 0, $Pos),
				array($item), array_slice($this->ItemData, $Pos));
		}
	}

	// Check if a menu item should be shown
	function RenderItem($item) {

		if (!is_null($item->SubMenu)) {
			foreach ($item->SubMenu->ItemData as $subitem) {
				if ($item->SubMenu->RenderItem($subitem))
					return TRUE;
			}
		}
		return ($item->Allowed && $item->Url <> "");
	}

	// Check if this menu should be rendered
	function RenderMenu() {

		foreach ($this->ItemData as $item) {
			if ($this->RenderItem($item))
				return TRUE;
		}
		return FALSE;
	}

	// Render the menu
	function Render($ret = TRUE) {

		if (!$this->RenderMenu())
			return;
		if ($this->IsCat) {	
			if ($this->IsRoot) {	
				$str = "<ul";
				if ($this->Id <> "") {
					if (is_numeric($this->Id)) {
						$str .= " id=\"menu_" . $this->Id . "\"";
					} else {
						$str .= " id=\"" . $this->Id . "\"";
					}
				}
				$str .= " class=\"" . EWPP_MENU_CLASSNAME . "\">\n";
			} else {
				$str = "<ul class=\"" . EWPP_SUBMENU_CLASSNAME . "\">\n";
			}
		} else {
				$str = "";
		}	
		$gtitle = FALSE;
		$gcnt = 0; // Group count
		$i = 0; // Menu item count
		foreach ($this->ItemData as $item) {
			if ($this->RenderItem($item)) {
				$i++;

				// Begin a group
				if ($gtitle && ($gcnt >= 1 || $this->IsRoot)) // add divider for previous group
					$str .= "<li class=\"" . EWPP_MENU_DIVIDER_CLASSNAME . "\"></li>\n";
				if ($item->GroupTitle && (!$this->IsRoot || !EWPP_MENU_ROOT_GROUP_TITLE_AS_SUBMENU)) { // Group title
					$gtitle = TRUE;
					$gcnt +=1;
					if (strval($item->Text) <> "") {
						$str .= "<li class=\"dropdown-header\">" . $item->Text . "</li>\n";
					}
					if (!is_null($item->SubMenu)) {
						foreach ($item->SubMenu->ItemData as $subitem) {
							$liclass = !is_null($subitem->SubMenu) ? EWPP_SUBMENU_ITEM_CLASSNAME : "";
							$aclass = "";
							if ($this->RenderItem($subitem)) {
								$str .= $subitem->Render($aclass, $liclass) . "\n"; // Create <LI>
							}
						}
					}	
				} else { // Menu item
					$gtitle = FALSE;
					$liclass = !is_null($item->SubMenu) ? ($this->IsRoot ? EWPP_MENU_ITEM_CLASSNAME : EWPP_SUBMENU_ITEM_CLASSNAME) : "";
					$aclass = !is_null($item->SubMenu) ? ($this->IsMLink ? "ewDropdown dropdown-toggle" : "") : "" ;
					if (strpos($item->Text, $GLOBALS["PPLanguage"]->Phrase("CategoryMenu")) > -1) {
						$liclass = $this->IsRoot ? EWPP_MENU_ITEM_CLASSNAME : EWPP_SUBMENU_ITEM_CLASSNAME;	
						$aclass = $this->IsMLink ? "ewDropdown dropdown-toggle" : "";
					}
					$str .= $item->Render($aclass, $liclass, $this->IsMLink) . "\n"; // Create <LI>	
				}
			}
		}
			if ($this->IsCat)
					$str .= "</ul>\n"; // End last group
		if ($ret) // Return as string
			return $str;
		echo $str; // Output
	}
}

// Menu item class
class cpMenuItem {
	var $Id;
	var $Text;
	var $Url;
	var $ParentId; 
	var $SubMenu = NULL; // Data type = cpMenu
	var $Source;
	var $Allowed = TRUE;
	var $Target;
	var $GroupTitle;

	// Constructor
	function cpMenuItem($id, $text, $url, $parentid, $src, $allowed, $grouptitle = FALSE) {

		global $PPLanguage;
		$this->Id = $id;
		$this->Text = $text;
		if (str_replace("&nbsp;", "", $text) == $PPLanguage->Phrase("CategoryMenu")) {
			$this->Text .= EWPP_SUBMENU_DROPDOWN_IMAGE;
			$this->Url = "#";
		} elseif (str_replace("&nbsp;", "", $text) == $PPLanguage->Phrase("CartMenu")) {	
			$this->Url = EWPP_CHECKOUT_PAGE;
		} else {	
			$this->Url = $url;
		}
		$this->ParentId = $parentid;
		$this->Source = $src;
		$this->Allowed = $allowed;
		$this->GroupTitle = $grouptitle;
	}

	// Add submenu item
	function AddItem($item, $cat = FALSE, $link = FALSE) {

		if (is_null($this->SubMenu))
			$this->SubMenu = new cpMenu($this->Id, TRUE, $link);
		$this->SubMenu->AddItem($item);
	}

	// Render
	function Render($aclass = "", $liclass = "", $link = FALSE) {
		// Create <A>

		if ($this->Url == "") 
			$this->Url = "#";
		$attrs = array("class" => $aclass, "href" => $this->Url, "target" => $this->Target);
		if ((!is_null($this->SubMenu)) && EWPP_SUBMENU_DROPDOWN_IMAGE) { 	// 6.0
				if ($link)
					$this->Text .= EWPP_SUBMENU_DROPDOWN_IMAGE;
				if (!$link && $this->ParentId == -1)
					$this->Text .= EWPP_SUBMENU_DROPDOWN_IMAGE;	
		}			
		$innerhtml = ewpp_HtmlElement("a", $attrs, $this->Text);
		if (!is_null($this->SubMenu)) {
			if ($link) {
				$compare = $this->Url;
				if ((strpos($compare, "menupage") === FALSE) && ($this->Url != "#")) {
					$attrs2 = array("class" => "ewMenuLink", "href" => $this->Url);
					$text2 = "<span class=\"icon-arrow-right\"></span>"; 
					$innerhtml =  ewpp_HtmlElement("a", $attrs2, $text2) . $innerhtml;
				}
			} 
			$innerhtml .= $this->SubMenu->Render(TRUE);
		}	

		// Create <LI>
		return ewpp_HtmlElement("li", array("class" => $liclass), $innerhtml);
	}
}

// Build HTML element // 5.0
function ewpp_HtmlElement($tagname, $attrs, $innerhtml = "", $endtag = TRUE) {

	$html = "<" . $tagname;
	if (is_array($attrs)) {
		foreach ($attrs as $name => $attr) {
			if (strval($attr) <> "")
				$html .= " " . $name . "=\"" . ewpp_HtmlEncode($attr) . "\"";
		}
	}
	$html .= ">";
	if (strval($innerhtml) <> "")
		$html .= $innerhtml;
	if ($endtag)
		$html .= "</" . $tagname . ">";
	return $html;
}

function ewpp_WriteDesktopMenu($desktop = FALSE) {
	global $PPLanguage;
	if ($desktop) {
		$catstr = ewpp_WriteCategories();
		$catstr = str_replace(EWPP_SUBMENU_DROPDOWN_IMAGE, "", $catstr);
		$menustr = ewpp_WriteMenu();
	} else {
		$catstr = ewpp_WriteCategories(TRUE);
		$menustr = ewpp_WriteMenu(TRUE);
	}
	$cattxt = $PPLanguage->Phrase("CategoryMenu");
	$carttxt = $PPLanguage->Phrase("CartMenu");
	$mcatpos = strpos($menustr, $cattxt);
	$mcartpos = strpos($menustr, $carttxt);
	generatemenu($menustr, $catstr, $mcartpos, $mcatpos, $desktop); 
}

function generatemenu($menustr, $catstr, $mcartpos = -1, $mcatpos = -1, $desktop = FALSE) {
	global $PPLanguage;
	$cattxt = $PPLanguage->Phrase("CategoryMenu");
	$carttxt = $PPLanguage->Phrase("CartMenu");
	$text = "</a>";
	if (($mcartpos > -1 ) && ($mcatpos > -1)) {
		$inspos = strpos($menustr, $text, $mcatpos);
		$str = substr($menustr, 0, $inspos) . $catstr . substr($menustr, $inspos);
		if ($desktop) {
			$str = "<ul class=\"nav navbar-nav hidden-xs\" id=\"ewMenu\">" . $str . "</ul>";
		} else {
			$str = "<ul class=\"nav navbar-nav visible-xs\" id=\"ewMobileMenu\">" . $str . "</ul>";	
		}	
	} elseif ($mcatpos > -1) {
		$inspos = strpos($menustr, $text, $mcatpos);
		$str = substr($menustr, 0, $inspos) . $catstr . substr($menustr, $inspos);
		if ($desktop) {
			$str = "<ul class=\"nav navbar-nav hidden-xs\" id=\"ewMenu\">" . $str . "</ul>";
			$str .= "<ul class=\"nav navbar-nav navbar-right hidden-xs\" id=\"ewCatMenu\">" . "<li><a href=\"" . EWPP_CHECKOUT_PAGE . "\">" . $carttxt . "</a></li></ul>";	
		} else {
			$str = "<ul class=\"nav navbar-nav visible-xs\" id=\"ewMobileMenu\">" . $str;
			$str .= "<li><a href=\"" . EWPP_CHECKOUT_PAGE . "\">" . $carttxt . "</a></li></ul>";
		}	
	} elseif ($mcartpos > -1) {
		if ($desktop) {
			$str = "<ul class=\"nav navbar-nav hidden-xs\" id=\"ewMenu\">" . $menustr . "</ul>";
			$str .= "<ul class=\"nav navbar-nav navbar-right hidden-xs\" id=\"ewCatMenu\">";
			$str .= "<li class=\"dropdown\"><a href=\"#\">" . $cattxt . "&nbsp;<span class=\"icon-arrow-down\"></span></a>" . $catstr . "</li></ul>";
		} else {
			$str = "<ul class=\"nav navbar-nav visible-xs\" id=\"ewMobileMenu\">" . $menustr;
			$str .= "<li class=\"dropdown\"><a href=\"#\" class=\"ewDropdown dropdown-toggle\">" . $cattxt . "&nbsp;<span class=\"icon-arrow-down\"></span></a>" . $catstr . "</li></ul>";
		}	
	} else {
		if ($desktop) {
			$str = "<ul class=\"nav navbar-nav hidden-xs\" id=\"ewMenu\">" . $menustr . "</ul>";
			$str .= "<ul class=\"nav navbar-nav navbar-right hidden-xs\" id=\"ewCatMenu\">" . "<li><a href=\"" . EWPP_CHECKOUT_PAGE . "\">" . $carttxt . "</a></li>";
			$str .= "<li class=\"dropdown\"><a href=\"#\">" . $cattxt . "&nbsp;<span class=\"icon-arrow-down\"></span></a>" . $catstr . "</li></ul>";
		} else {
			$str = "<ul class=\"nav navbar-nav visible-xs\" id=\"ewMobileMenu\">" . $menustr;
			$str .= "<li><a href=\"" . EWPP_CHECKOUT_PAGE . "\">" . $carttxt . "</a></li>";
			$str .= "<li class=\"dropdown\"><a href=\"#\" class=\"ewDropdown dropdown-toggle\">" . $cattxt . "&nbsp;<span class=\"icon-arrow-down\"></span></a>" . $catstr . "</li></ul>";
		}	
	}
	echo $str;
}

// Output menu items
function ewpp_WriteMenu($link = FALSE) {

	global $conn, $ewpp_MenuId, $ewpp_MenuLink, $ewpp_MenuGen, $ewpp_MenuFn, $ewpp_MenuUrl, $ewpp_MenuParentId;
	$oMenuRoot = new cpMenu(EWPP_MENUBAR_MENU_ID);
	$oMenuRoot->IsRoot = TRUE;
	$oMenuRoot->IsMLink = $link;
	$sql_menu = EWPP_MENU_SELECT_SQL;
	if (EWPP_MENU_DEFAULT_ORDERBY <> "")
		$sql_menu .= " ORDER BY " . EWPP_MENU_DEFAULT_ORDERBY;
	if ($rs_menu = $conn->Execute($sql_menu)) {
		while (!$rs_menu->EOF) {
			ewpp_LoadMenu($rs_menu);
			$ewpp_MenuLink .= "&nbsp;";
			$oMenuRoot->AddMenuItem($ewpp_MenuId, $ewpp_MenuLink, $ewpp_MenuGen, $ewpp_MenuFn, $ewpp_MenuUrl, $ewpp_MenuParentId);
			$rs_menu->MoveNext();
		}
		$rs_menu->Close();
	}
	return $oMenuRoot->Render();
}

// Output category items
function ewpp_WriteCategories($link = FALSE) {

	global $PPLanguage, $conn;
	$oCatRoot = new cpMenu(EWPP_MENUBAR_CAT_ID, TRUE);
	$oCatRoot->IsRoot = FALSE;
	$oCatRoot->IsMLink = $link;
	$sql_cat = EWPP_CATEGORY_SELECT_SQL;
	if (EWPP_CATEGORY_DEFAULT_ORDERBY <> "")
		$sql_cat .= " ORDER BY " . EWPP_CATEGORY_DEFAULT_ORDERBY;
	if ($rscat = $conn->Execute($sql_cat)) {
		if (EWPP_SHOW_ALL_PRODUCTS_CATEGORY || $rscat->RecordCount() <= 0) {
			$catname = $PPLanguage->Phrase("All"); // All products
			$cnt = ewpp_GetCategoryProductCount();
			if (EWPP_SHOW_CATEGORY_PRODUCT_COUNT)
				$catname .= " <span class=\"" . EWPP_PRODUCT_COUNT_CLASS_NAME . "\">" . $cnt . "</span>";
			$catname .= "&nbsp;";
			$oCatRoot->AddMenuItem(-1, $catname, TRUE,
				EWPP_CART_LIST_PAGE . "?" . EWPP_COMMAND . "=" .EWPP_COMMAND_RESETALL, "", -1);
		}
		while (!$rscat->EOF) {
			$catid = $rscat->fields("CategoryId");
			$catname = $rscat->fields("CategoryName");
			$cnt = ewpp_GetCategoryProductCount($catid);
			if (EWPP_SHOW_CATEGORY_PRODUCT_COUNT)
				$catname .= " <span class=\"" . EWPP_PRODUCT_COUNT_CLASS_NAME . "\">" . $cnt . "</span>";
			$catname .= "&nbsp;";
			if (EWPP_SHOW_EMPTY_CATEGORY || $cnt > 0)
				$oCatRoot->AddMenuItem($catid, $catname, TRUE,
					 EWPP_CART_LIST_PAGE . "?" . EWPP_CATEGORY_ID . "=" . $rscat->fields("CategoryId"),
					 "", $rscat->fields("CategoryParentId"));
			$rscat->MoveNext();
		}
		$rscat->Close();
	}
	return $oCatRoot->Render();
}

// Get URL parameters
function ewpp_GetUrlParams() {
	// Set up current category

	if (@$_GET[EWPP_COMMAND] == EWPP_COMMAND_RESETALL)
		$_SESSION[EWPP_SESSION_CATEGORY_ID] = "";

	// Reset search criteria
	if (@$_GET[EWPP_COMMAND] == EWPP_COMMAND_RESET || @$_GET[EWPP_COMMAND] == EWPP_COMMAND_RESETALL)
		$_SESSION[EWPP_SESSION_SEARCH_KEYWORD] = "";

	// Get search criteria
	if (@$_GET[EWPP_SEARCH_KEYWORD] <> "")
		$_SESSION[EWPP_SESSION_SEARCH_KEYWORD] = trim(ewpp_StripSlashes($_GET[EWPP_SEARCH_KEYWORD]));
}

// Add filter
function ewpp_AddFilter(&$where, $filter) {

	if ($filter <> "") {
		if ($where <> "") $where .= " AND ";
		$where .= "(" . $filter . ")";
	}
}

// Add default filter
function ewpp_AddDefaultFilter(&$where) {

	ewpp_AddFilter($where, EWPP_PRODUCT_DEFAULT_FILTER);
}

// Add search criteria
function ewpp_AddSearchCriteria(&$where) {

	global $sKeyword;
	$sKeyword = @$_SESSION[EWPP_SESSION_SEARCH_KEYWORD];
	if ($sKeyword <> "") { 
		$sSearchWhere = ewpp_SearchWhere($sKeyword);
		ewpp_AddFilter($where, $sSearchWhere);
	}
}

// Add item count filter
function ewpp_AddCountFilter(&$where) {

	if (EWPP_USE_ITEM_COUNT)
		ewpp_AddFilter($where, EWPP_PRODUCT_ITEMCOUNT_FILTER);
}

// Add page index filter // 5.0
function ewpp_AddPageIndexFilter(&$where) {

	global $ewpp_Alpha;
	if ($ewpp_Alpha <> "")
		ewpp_AddFilter($where, ewpp_PagingSqlFilter($ewpp_Alpha));
}

// Add default, search and item count filters
function ewpp_AddFilters(&$where) {

	ewpp_AddDefaultFilter($where);
	ewpp_AddSearchCriteria($where);
	ewpp_AddCountFilter($where);
	ewpp_AddPageIndexFilter($where); // 5.0
}

// Get category filter
function ewpp_GetCategoryFilter($catid) {

	$filter = "";
	if ($catid <> "") {
		$catlist = (EWPP_INCLUDE_SUBCATEGORY) ? ewpp_LoadSubCatList($catid) : $catid;
		$filter = str_replace("@@CategoryId@@", $catlist, EWPP_PRODUCT_CATEGORY_FILTER);
	}
	return $filter;
}

// Get category product count
function ewpp_GetCategoryProductCount($catid = "") {
	// Get URL parameters // 5.0

	ewpp_GetUrlParams();

	// Get the count
	$cnt = -1;
	$sql = str_replace("SELECT *", "SELECT COUNT(*)", EWPP_PRODUCT_SELECT_SQL);
	$filter = ewpp_GetCategoryFilter($catid);
	ewpp_AddFilters($filter);
	if ($filter <> "")
		$sql .= " WHERE " . $filter;
	return ewpp_ExecuteScalar($sql);
}

// Return search SQL
function ewpp_SearchSQL($keyword) {

	$sKeyword = ewpp_AdjustSql($keyword);
	return str_replace("@" . EWPP_SEARCH_KEYWORD . "@", $sKeyword, EWPP_PRODUCT_SEARCH_FILTER);
}

// Return search WHERE clause based on search keyword and type
function ewpp_SearchWhere($keyword) {

	$sSearchStr = "";
	if ($keyword <> "") {
		$sSearch = trim($keyword);
		if (EWPP_SEARCH_TYPE <> "") {
			while (strpos($sSearch, "  ") !== FALSE)
				$sSearch = str_replace("  ", " ", $sSearch);
			$arKeyword = explode(" ", trim($sSearch));
			foreach ($arKeyword as $sKeyword) {
				if ($sSearchStr <> "")
					$sSearchStr .= " " . EWPP_SEARCH_TYPE . " ";
				$sSearchStr .= "(" . ewpp_SearchSQL($sKeyword) . ")";
			}
		} else {
			$sSearchStr = ewpp_SearchSQL($sSearch);
		}
	}
	if ($sSearchStr <> "")
		$sSearchStr = "(" . $sSearchStr . ")";
	return $sSearchStr;
}

// Check if there is valid discount code
function ewpp_UseDiscountCode() {

	if (EWPP_DB_TYPE == "MYSQL") {
		$date = "CURDATE()";
	} elseif (EWPP_DB_TYPE == "SQLITE") {
		$date = "DATE('now')";
	} else {
		$date = "DATE()";
	}
	$sql = "SELECT COUNT(*) FROM " . ewpp_DbQuote(EWPP_TABLENAME_DISCOUNTCODE) .
		" WHERE CodeStart <= " . $date . " AND CodeEnd >= " . $date;
	return intval(ewpp_ExecuteScalar($sql)) > 0;
}

// Output page title
function ewpp_WriteTitle() {

	global $PPLanguage, $EWPP_PAGE_ID, $ewpp_CatName, $ewpp_Item;
	if ($EWPP_PAGE_ID == "list") {
		if (strval($ewpp_CatName) <> "") {
			echo $ewpp_CatName;
		} else {
			echo $PPLanguage->Phrase("Products");
		}
	} elseif ($EWPP_PAGE_ID == "view") {
		echo @$ewpp_Item["ItemName"];
	} else {
		echo EWPP_BODY_TITLE;
	}
}

// Output cart config JavaScript
function ewpp_WriteScript() {

	global $PPLanguage, $EWPP_PAGE_ID;
	$str = "(function(P) {";
	$str .= "P.pageID['" . $EWPP_PAGE_ID . "'] = true;";
	$str .= "P.SESSION_ID='" . ewpp_TeaEncrypt(session_id(), EWPP_RANDOM_KEY) . "';";
	if (in_array($EWPP_PAGE_ID, array("checkout", "shipping", "confirm"))) {
		$discount = ewpp_UseDiscountCode();
	} else {
		$discount = FALSE;
	}

	// Overwrite cartconfig.js
	$str .= "P.DISCOUNT_CODE=" . ((!EWPP_IS_JSDB && $discount) ? 1 : 0) . ";";
	$str .= "P.USE_PAYPAL=" . ((EWPP_IS_JSDB || EWPP_USE_PAYPAL) ? 1 : 0) . ";";
	$str .= "P.DEFAULT_CURRENCY_SYMBOL='" . EWPP_DEFAULT_CURRENCY_SYMBOL . "';";
	$str .= "P.DEFAULT_MON_DECIMAL_POINT='" . EWPP_DEFAULT_MON_DECIMAL_POINT . "';";
	$str .= "P.DEFAULT_MON_THOUSANDS_SEP='" . EWPP_DEFAULT_MON_THOUSANDS_SEP . "';";
	$str .= "P.DEFAULT_FRAC_DIGITS=" . EWPP_DEFAULT_FRAC_DIGITS . ";";

	// JavaScript tables
	$str .= "P.tblCategory=P.CreateTable(" . ewpp_TableToJson("Category") . ");";
	$str .= "P.tblCategoryItem=P.CreateTable(" . ewpp_TableToJson("CategoryItem") . ");";
	$str .= "P.tblState=P.CreateTable(" . ewpp_TableToJson("State") . ");";
	$str .= "P.tblCountry=P.CreateTable(" . ewpp_TableToJson("Country") . ");";
	$str .= "P.tblDiscount=P.CreateTable(" . ewpp_TableToJson("Discount") . ");";
	$str .= "P.tblDiscountType=P.CreateTable(" . ewpp_TableToJson("DiscountType") . ");";
	$str .= "P.tblShipping=P.CreateTable(" . ewpp_TableToJson("Shipping") . ");";
	$str .= "P.tblShippingMethod=P.CreateTable(" . ewpp_TableToJson("ShippingMethod") . ");";
	$str .= "P.tblShippingType=P.CreateTable(" . ewpp_TableToJson("ShippingType") . ");";
	$str .= "P.tblTax=P.CreateTable(" . ewpp_TableToJson("Tax") . ");";
	$str .= "P.tblMenu=P.CreateTable(" . ewpp_TableToJson("Menu") . ");";
	$str .= "P.shipCostList0=P.tblShipping({\"ShippingTypeCalcId\":0}).order(\"ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingQty\").get();";
	$str .= "P.shipCostList1=P.tblShipping({\"ShippingTypeCalcId\":1}).order(\"ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingPrice\").get();";
	$str .= "P.shipCostList2=P.tblShipping({\"ShippingTypeCalcId\":2}).order(\"ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingWeight\").get();";
	$str .= "})(PAYPALSHOPMAKER);";
	echo $str;
}

// Output meta tag for view page
function ewpp_WriteMeta() {

	global $PPLanguage, $EWPP_PAGE_ID, $ewpp_Item, $ewpp_CatName, $keywords, $desc;
	if ($EWPP_PAGE_ID == "view") { // View page
		$keywords[] = $ewpp_Item["ItemName"]; // Item name
		$keywords[] = $ewpp_Item["ItemNumber"]; // Item number

		//$keywords[] = "keyword"; // Add other keywords for the page
		$desc = substr(strip_tags($ewpp_Item["ItemDescription"]), 0, 150); // Description
		$desc = str_replace(array("\r\n", "\n", "\r", "\t"), " ", $desc);
	} elseif ($EWPP_PAGE_ID == "list") { // List page
		if (strval($ewpp_CatName) <> "")
			$keywords[] = $ewpp_CatName;

		//$keywords[] = "keyword"; // Add other keywords for the page
		//$desc = "description";

	} else { //  Other pages

		//$keywords[] = "keyword";
		//$desc = "description";

	}

	//$keywords[] = "keyword"; // Add keywords for all page
	$cnt = count($keywords);
	if ($cnt > 0)
		echo "<meta name=\"keywords\" content=\"" . ewpp_HtmlEncode(implode(", ", $keywords)) . "\">\n";
	if ($desc <> "")
		echo "<meta name=\"description\" content=\"" . ewpp_HtmlEncode($desc) . "\">\n";
}

// Check if an option should be displayed
function ewpp_ShowOption($item, $i) {

	$type = $item["ItemOption" . $i . "Type"];
	$name = $item["ItemOption" . $i . "FieldName"];
	$options = $item["ItemOption" . $i];
	$write = (strval($name) <> "" && strval($type) <> "");
	return $write && ($type == "TEXT" || ($type <> "TEXT" && $options <> ""));
}

// Submit button text
function ewpp_SubmitButtonText($typeid) {

	global $PPLanguage;
	if ($typeid == 1) {
		$text = $PPLanguage->Phrase("BuyNow");
	} elseif ($typeid == 2) {
		$text = $PPLanguage->Phrase("Subscribe");
	} else {
		$text = $PPLanguage->Phrase("AddToCart");
	}
	return ewpp_HtmlEncode($text);
}

// Submit button class name
function ewpp_SubmitButtonClass($item) {

	global $PPLanguage;
	$typeid = intval($item["ItemButtonTypeId"]);
	$hidden = ($typeid == 2 && !ewpp_ValidSubscribe($item, 3));
	if ($hidden) {
		$classname = "ewHidden";
	} elseif ($typeid == 1) {
		$classname = "ewBuyNow";
	} elseif ($typeid == 2) {
		$classname = "ewSubscribe";
	} else {
		$classname = "ewAddToCart";
	}
	return ewpp_HtmlEncode($classname);
}

/**
 * Functions for TEA encryption/decryption
 */

function ewpp_Long2Str($v, $w) {
	$len = count($v);
	$s = array();
	for ($i = 0; $i < $len; $i++)
	{
		$s[$i] = pack("V", $v[$i]);
	}
	if ($w) {
		return substr(join('', $s), 0, $v[$len - 1]);
	}	else {
		return join('', $s);
	}
}

function ewpp_Str2Long($s, $w) {
	$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
	$v = array_values($v);
	if ($w) {
		$v[count($v)] = strlen($s);
	}
	return $v;
}

function ewpp_Int32($n) {
	while ($n >= 2147483648) $n -= 4294967296;
	while ($n <= -2147483649) $n += 4294967296;
	return (int)$n;
}

// encrypt
function ewpp_TeaEncrypt($str, $key) {

	if ($str == "") {
		return "";
	}
	$v = ewpp_Str2Long($str, true);
	$k = ewpp_Str2Long($key, false);
	if (count($k) < 4) {
		for ($i = count($k); $i < 4; $i++) {
			$k[$i] = 0;
		}
	}
	$n = count($v) - 1;
	$z = $v[$n];
	$y = $v[0];
	$delta = 0x9E3779B9;
	$q = floor(6 + 52 / ($n + 1));
	$sum = 0;
	while (0 < $q--) {
		$sum = ewpp_Int32($sum + $delta);
		$e = $sum >> 2 & 3;
		for ($p = 0; $p < $n; $p++) {
			$y = $v[$p + 1];
			$mx = ewpp_Int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ ewpp_Int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$p] = ewpp_Int32($v[$p] + $mx);
		}
		$y = $v[0];
		$mx = ewpp_Int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ ewpp_Int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
		$z = $v[$n] = ewpp_Int32($v[$n] + $mx);
	}
	return ewpp_UrlEncode(ewpp_Long2Str($v, false));
}

// decrypt
function ewpp_TeaDecrypt($str, $key) {

	$str = ewpp_UrlDecode($str);
	if ($str == "") {
		return "";
	}
	$v = ewpp_Str2Long($str, false);
	$k = ewpp_Str2Long($key, false);
	if (count($k) < 4) {
		for ($i = count($k); $i < 4; $i++) {
			$k[$i] = 0;
		}
	}
	$n = count($v) - 1;
	$z = $v[$n];
	$y = $v[0];
	$delta = 0x9E3779B9;
	$q = floor(6 + 52 / ($n + 1));
	$sum = ewpp_Int32($q * $delta);
	while ($sum != 0) {
		$e = $sum >> 2 & 3;
		for ($p = $n; $p > 0; $p--) {
			$z = $v[$p - 1];
			$mx = ewpp_Int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ ewpp_Int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[$p] = ewpp_Int32($v[$p] - $mx);
		}
		$z = $v[$n];
		$mx = ewpp_Int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ ewpp_Int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
		$y = $v[0] = ewpp_Int32($v[0] - $mx);
		$sum = ewpp_Int32($sum - $delta);
	}
	return ewpp_Long2Str($v, true);
}

function ewpp_UrlEncode($string) {
	$data = base64_encode($string);
	return str_replace(array('+','/','='), array('-','_','.'), $data);
}

function ewpp_UrlDecode($string) {
	$data = str_replace(array('-','_','.'), array('+','/','='), $string);
	return base64_decode($data);
}

// Get the cart items as an array
function ewpp_RawDataToCart($ar) {

	$arrCart = array();
	$nCartItems = @$ar["num_cart_items"];
	$item = @$ar["item_number"];
	if ($item <> "") {
		$nCartItems = 1;
		$temp = array("item_number" => $item,
			"item_name" => @$ar["item_name"],
			"quantity" => @$ar["quantity"],
			"mc_gross" => @$ar["mc_gross"]);
			for ($i = 1; $i <= 7; $i++) {
				$temp["option_name" . $i] = @$ar["option_name" . $i];
				$temp["option_selection" . $i] = @$ar["option_selection" . $i];
			}
		$arrCart[] = $temp;
	} else {
		for ($i = 1; $i <= $nCartItems; $i++) {
			$temp = array("item_number" => @$ar["item_number".$i],
				"item_name" => @$ar["item_name".$i],
				"quantity" => @$ar["quantity".$i],
				"mc_gross" => @$ar["mc_gross_".$i]);
			for ($j = 1; $j <= 7; $j++) {
				$temp["option_name" . $j] = @$ar["option_name" . $j . "_" . $i];
				$temp["option_selection" . $j] = @$ar["option_selection" . $j . "_" . $i];
			}
			$arrCart[] = $temp;
		}
	}
	return $arrCart;
}

// Display cart items
function ewpp_CartToText($arrCart, $html) {

	global $PPLanguage;
	$str = "";
	$delim = ($html) ? "<br>" : "\n";
	foreach ($arrCart as $cartItem) {
		if ($str <> "")
			$str .= $delim; // separate between items
		$str .= $PPLanguage->Phrase("ItemNumber") . " " . $cartItem["item_number"] . $delim .
				$PPLanguage->Phrase("ItemName") . " " . $cartItem["item_name"] . $delim .
				$PPLanguage->Phrase("Quantity") . " " . $cartItem["quantity"] . $delim;
		for ($j = 1; $j <= 7; $j++) {
			if (@$cartItem["option_name" . $j] <> "" && @$cartItem["option_selection" . $j] <> "")
				$str .= $cartItem["option_name" . $j] . $PPLanguage->Phrase("OptionSep") . $cartItem["option_selection" . $j] . $delim;
		}

		//$str .= $PPLanguage->Phrase("Total") . ewpp_FormatCurrency($cartItem["mc_gross"]) . $delim;
		if (EWPP_DIGITAL_DOWNLOAD && isset($cartItem["download_file"])) {
			$url = $cartItem["download_file"]; // URL from ewpp_GetDownloadFiles()
			if (EWPP_USE_PAYPAL) { // Paid
				if ($url === FALSE) {
					$str .= $PPLanguage->Phrase("DownloadUrl") . " " . EWPP_MISSING_URL_START . $cartItem["item_number"] . EWPP_MISSING_URL_END . $delim;
				} elseif ($url <> "") {
					if ($html) {
						$str .= "<a href=\"" . $url . "\" target=\"_blank\">" . $PPLanguage->Phrase("Download") . "</a>" . $delim;
					} else {
						$str .= $PPLanguage->Phrase("DownloadUrl") . " " . $url . $delim;
					}
				}
			}
		}
	}
	return $str;
}

/**
 * Functions for converting encoding
 */

function ewpp_ConvertToUtf8($str) {
	return ewpp_Convert(EWPP_ENCODING, "UTF-8", $str);
}

function ewpp_ConvertFromUtf8($str) {
	return ewpp_Convert("UTF-8", EWPP_ENCODING, $str);
}

function ewpp_Convert($from, $to, $str) {
	if ($from != "" && $to != "" && strtoupper($from) != strtoupper($to)) {
		if (function_exists("iconv")) {
			return iconv($from, $to, $str);
		} elseif (function_exists("mb_convert_encoding")) {
			return mb_convert_encoding($str, $to, $from);
		} else {
			return $str;
		}
	} else {
		return $str;
	}
}

/**
 * Langauge class
 */
class cpLanguage {
	var $Phrases = NULL;

	// Constructor
	function cpLanguage($langfile) {

		global $EWPP_LANG_PATH;
		if ($langfile == "")
			$langfile = "english.xml";
		$f = $EWPP_LANG_PATH . "/" . $langfile;
		if (!file_exists($f))
			die("Language file does not exists.");
		if (EWPP_USE_DOM_XML) {
			$this->Phrases = new cpXMLDocument();
			$this->Phrases->Load($f);
		} else {
			if (is_array(@$_SESSION[EWPP_PROJECT_NAME . "_" . $langfile])) {
				$this->Phrases = $_SESSION[EWPP_PROJECT_NAME . "_" . $langfile];
			} else {
				$this->Phrases = ewpp_Xml2Array(file_get_contents(realpath($f)));
			}
		}
	}

	// Get node attribute
	function GetNodeAtt($Nodes, $Att) {

		$value = ($Nodes) ? $this->Phrases->GetAttribute($Nodes, $Att) : "";
		return $value;
	}

	// Get phrase
	function Phrase($Id) {

		if (is_object($this->Phrases)) {
			return $this->GetNodeAtt($this->Phrases->SelectSingleNode("//global/phrase[@id='" . strtolower($Id) . "']"), "value");
		} elseif (is_array($this->Phrases)) {
			return ewpp_ConvertFromUtf8(@$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['value']);
		}
	}

	// Set phrase
	function setPhrase($Id, $Value) {

		if (is_array($this->Phrases)) {
			$this->Phrases['ew-language']['global']['phrase'][strtolower($Id)]['attr']['value'] = $Value;
		}
	}

	// Output XML as JSON
	function XmlToJSON($XPath) {

		$NodeList = $this->Phrases->SelectNodes($XPath);
		$Str = "{";
		foreach ($NodeList as $Node) {
			$Id = $this->GetNodeAtt($Node, "id");
			$Value = $this->GetNodeAtt($Node, "value");
			$Str .= "\"" . strtolower(ewpp_JsEncode2($Id)) . "\":\"" . ewpp_JsEncode2($Value) . "\","; // 5.0
		}
		if (substr($Str, -1) == ",") $Str = substr($Str, 0, strlen($Str)-1);
		$Str .= "}";
		return $Str;
	}

	// Output array as JSON
	function ArrayToJSON($client) {

		$ar = @$this->Phrases['ew-language']['global']['phrase'];
		$Str = "{";
		if (is_array($ar)) {
			foreach ($ar as $id => $node) {
				$is_client = TRUE; // Output all phrases // 5.0
				$value = ewpp_ConvertFromUtf8(@$node['attr']['value']);
				if (!$client || ($client && $is_client))
					$Str .= "\"" . strtolower(ewpp_JsEncode2($id)) . "\":\"" . ewpp_JsEncode2($value) . "\","; // 5.0
			}
		}
		if (substr($Str, -1) == ",") $Str = substr($Str, 0, strlen($Str)-1);
		$Str .= "}";
		return $Str;
	}

	// Output client phrases as JSON
	function ToJSON() {

		if (is_object($this->Phrases)) {
			return "var ewppLanguage = new ewpp_Language(" . $this->XmlToJSON("//phrase[@client='1']") . ");";
		} elseif (is_array($this->Phrases)) {
			return "var ewppLanguage = new ewpp_Language(" . $this->ArrayToJSON(TRUE) . ");";
		}
	}
}

/**
 * XML document class
 */
class cpXMLDocument {
	var $Encoding = "utf-8";
	var $XmlDoc = FALSE;

	function cpXMLDocument($encoding = "") {
		if ($encoding <> "")
			$this->Encoding = $encoding;
		if ($this->Encoding <> "") {
			$this->XmlDoc = new DOMDocument("1.0", strval($this->Encoding));
		} else {
			$this->XmlDoc = new DOMDocument("1.0");
		}
	}

	function Load($filename) {
		$filepath = realpath($filename);
		return $this->XmlDoc->load($filepath);
	}

	function &DocumentElement() {
		return $this->XmlDoc->documentElement;
	}

	function GetAttribute($element, $name) {
		return ($element) ? ewpp_ConvertFromUtf8($element->getAttribute($name)) : "";
	}

	function SelectSingleNode($query) {
		$elements = $this->SelectNodes($query);
		return ($elements->length > 0) ? $elements->item(0) : NULL;
	}

	function SelectNodes($query) {
		$xpath = new DOMXPath($this->XmlDoc);
		return $xpath->query($query);
	}

	function XML() {
		return $this->XmlDoc->saveXML();
	}
}

// Convert XML to array
function ewpp_Xml2Array($contents) {

	if (!$contents) return array(); 
	if (!function_exists('xml_parser_create')) return FALSE;
	$get_attributes = 1; // Always get attributes. DO NOT CHANGE!

	// Get the XML Parser of PHP
	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); // Always return in utf-8
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	if (!$xml_values) return;
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	$current = &$xml_array;
	$repeated_tag_index = array(); // Multiple tags with same name will be turned into an array
	foreach ($xml_values as $data) {
		unset($attributes, $value); // Remove existing values

		// Extract these variables into the foreach scope
		// tag(string), type(string), level(int), attributes(array)

		extract($data);
		$result = array();
		if (isset($value))
			$result['value'] = $value; // Put the value in a assoc array

		// Set the attributes
		if (isset($attributes) and $get_attributes) {
			foreach ($attributes as $attr => $val)
				$result['attr'][$attr] = $val; // Set all the attributes in a array called 'attr'
		} 

		// See tag status and do the needed
		if ($type == "open") { // The starting of the tag '<tag>'
			$parent[$level-1] = &$current;
			if (!is_array($current) || !in_array($tag, array_keys($current))) { // Insert New tag
				if ($tag <> 'ew-language' && @$result['attr']['id'] <> '') { // 
					$last_item_index = $result['attr']['id'];
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					$current = &$current[$tag][$last_item_index];
				} else {
					$current[$tag] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 0;
					$current = &$current[$tag];
				}
			} else { // Another element with the same tag name
				if ($repeated_tag_index[$tag.'_'.$level] > 0) { // If there is a 0th element it is already an array
					if (@$result['attr']['id'] <> '') {
						$last_item_index = $result['attr']['id'];
					} else {
						$last_item_index = $repeated_tag_index[$tag.'_'.$level];
					}
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level]++;
				} else { // Make the value an array if multiple tags with the same name appear together
					$temp = $current[$tag];
					$current[$tag] = array();
					if (@$temp['attr']['id'] <> '') {
						$current[$tag][$temp['attr']['id']] = $temp;
					} else {
						$current[$tag][] = $temp;
					}
					if (@$result['attr']['id'] <> '') {
						$last_item_index = $result['attr']['id'];
					} else {
						$last_item_index = 1;
					}
					$current[$tag][$last_item_index] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 2;
				} 
				$current = &$current[$tag][$last_item_index];
			}
		} elseif ($type == "complete") { // Tags that ends in one line '<tag />'
			if (!isset($current[$tag])) { // New key
				$current[$tag] = array(); // Always use array for "complete" type
				if (@$result['attr']['id'] <> '') {
					$current[$tag][$result['attr']['id']] = $result;
				} else {
					$current[$tag][] = $result;
				}
				$repeated_tag_index[$tag.'_'.$level] = 1;
			} else { // Existing key
				if (@$result['attr']['id'] <> '') {
			  	$current[$tag][$result['attr']['id']] = $result;
				} else {
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
				}
			  $repeated_tag_index[$tag.'_'.$level]++;
			}
		} elseif ($type == 'close') { // End of tag '</tag>' 
			$current = &$parent[$level-1];
		}
	}
	return($xml_array);
}

// Write global debug message
function ewpp_DebugMsg() {

	global $gsDebugMsg;
    	$msg = preg_replace('/^\n/', "", $gsDebugMsg);
    	$gsDebugMsg = "";
	return ($msg <> "") ? "<div class=\"alert alert-info ewAlert\">" . $msg . "</div>" : "";
}

// Write global debug message
function ewpp_SetDebugMsg($v, $newline = TRUE) {

	global $gsDebugMsg;
	$gsDebugMsg .= $v;
}

// Get Transaction Id
function ewpp_GetTxnId() {

	return date("YmdHis");
}

// Replace email template variables by PayPal variables
function ewpp_ApplyPayPalVars($txt) {

	global $arPPVars;
	if (is_array($arPPVars)) {
		$txt = preg_replace_callback('|<!--\{[\w]+\}-->|', create_function(	
			'$matches',
			'return @$GLOBALS["arPPVars"][substr($matches[0], 5, strlen($matches[0])-9)];'
			), $txt);
	}
	return $txt;
}

// Get shipping method from IPN/PDT variables
function ewpp_GetShipMethod() {

	global $arPPVars;
	if (EWPP_CUSTOM_AS_TEXTAREA) {
		$sm = @$arPPVars["invoice"];
	} else {
		$sm = @$arPPVars["custom"];
		if ($sm == "") $sm = @$arPPVars["invoice"];
	}
	return $sm;
}

// Check if valid subscription settings
function ewpp_ValidSubscribe($item, $i) {

	return (floatval(@$item["ItemSubscribeA" . $i]) > 0 &&
						intval(@$item["ItemSubscribeP" . $i]) > 0 &&
						strval(@$item["ItemSubscribeT" . $i]) <> "");
}

// Remove XSS
function ewpp_RemoveXSS($val) {
	// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed 
	// this prevents some character re-spacing such as <java\0script> 
	// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs 

	$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val); 

	// straight replacements, the user should never need these since they're normal characters 
	// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29> 

	$search = 'abcdefghijklmnopqrstuvwxyz'; 
	$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	$search .= '1234567890!@#$%^&*()'; 
	$search .= '~`";:?+/={}[]-_|\'\\'; 
	for ($i = 0; $i < strlen($search); $i++) { 

	   // ;? matches the ;, which is optional 
	   // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 
	   // &#x0040 @ search for the hex values 

	   $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ; 

	   // &#00064 @ 0{0,7} matches '0' zero to seven times 
	   $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ; 
	} 

	// now the only remaining whitespace attacks are \t, \n, and \r 
	$ra = $GLOBALS["EWPP_XSS_ARRAY"]; // Note: Customize $EWPP_XSS_ARRAY in the config file
	$found = true; // keep replacing as long as the previous round replaced something 
	while ($found == true) { 
	   $val_before = $val; 
	   for ($i = 0; $i < sizeof($ra); $i++) { 
	      $pattern = '/'; 
	      for ($j = 0; $j < strlen($ra[$i]); $j++) { 
	         if ($j > 0) { 
	            $pattern .= '('; 
	            $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?'; 
	            $pattern .= '|(&#0{0,8}([9][10][13]);?)?'; 
	            $pattern .= ')?'; 
	         } 
	         $pattern .= $ra[$i][$j]; 
	      } 
	      $pattern .= '/i'; 
	      $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag 
	      $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags 
	      if ($val_before == $val) { 

	         // no replacements were made, so exit the loop 
	         $found = false; 
	      } 
	   } 
	} 
	return $val; 
}

// HTTP header
function ewpp_Header() {
	// No cache

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
	header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0
	header("X-UA-Compatible: IE=edge");
}

// Create language object
$PPLanguage = new cpLanguage(@$_SESSION["EWPP_LANG_FILE"]);
?>
