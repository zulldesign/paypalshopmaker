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
$sCmd = ewpp_PostVar("cmd");
$sSql = ewpp_PostVar("sql");
if ($sSql <> "") {
	$sSql = ewpp_BlowFishDecrypt($sSql, EWPP_RANDOM_KEY); // Decrypt
	$sSql = trim($sSql); // Remove white space
}

// Open connection to the database
$conn = ewpp_Connect();

// Execute command
switch (strtolower($sCmd)) {
	case "lock":
		echo LockApp();
		break;
	case "unlock":
		echo UnlockApp();
		break;
	case "backupdb":
	case "test":
		echo "OK";
		break;
	case "execsql":
		echo ExecSql($sSql);
		break;
	case "loadrs":
		echo LoadRs($sSql);
		break;
	default:
		echo ewpp_PostVars();
}

// Close connection
$conn->Close();
exit();

// Lock application
function LockApp() {

	return EnableApp(FALSE);
}

// Unlock application
function UnlockApp() {

	return EnableApp(TRUE);
}

// Enable/Disable application
function EnableApp($bool) {

	global $conn;
	$value = ($bool) ? 1 : 0;
	$wrksql = "UPDATE " . ewpp_DbQuote(EWPP_TABLENAME_APPSTATUS) .
		" SET AppEnabled = $value, AppLastUpdated = " . EWPP_NOW_FUNC;
	$conn->Execute($wrksql);
	return GetExecuteMsg($wrksql);
}

// Execute SQL
function ExecSql($sql) {

	global $conn;
	$wrksql = ConvertToCrLf($sql);
	$conn->Execute($wrksql);
	return GetExecuteMsg($wrksql);
}

// Load Record
function LoadRs($sql) {

	global $conn;
	$rs = $conn->Execute($sql);
	$LoadRs = GetExecuteMsg($sql);
	if ($LoadRs == "OK") {
		if ($rs && !$rs->EOF) {
			for ($i = 0; $i < $rs->FieldCount(); $i++) {
				$LoadRs .= "\r" . ConvertFromCrLf($rs->fields[$i]);
			}
		}
	}
	return $LoadRs;
}

// Get error
function GetExecuteMsg($sql) {

	global $conn;
	$err = $conn->ErrorMsg();
	if ($err <> "") {
		ewpp_WriteLog("CONNECT", "sql", $sql);
		ewpp_WriteLog("CONNECT", "error", $err);
		return "Error: " . $err . ", SQL: " . $sql;
	} else {
		return "OK";
	}
}

// Convert \r\n to CrLf
function ConvertToCrLf($str) {

	if (is_null($str)) {
		return $str;
	} elseif (strpos($str, "\\r\\n") !== FALSE) {
		return str_replace("\\r\\n", "\r\n", $str);
	} else {
		return $str;
	}
}

// Convert CrLf to \r\n
function ConvertFromCrLf($str) {

	if (is_null($str)) {
		return $str;
	} elseif (strpos($str, "\r\n") !== FALSE) {
		return str_replace("\r\n", "\\r\\n", $str);
	} else {
		return $str;
	}
}

// Blowfish decrypt
function ewpp_BlowFishDecrypt($data, $key) {

	$iv = substr(md5($key), 0, 8);
	$key = substr($key, 0, 16);
	$td = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
	$data = mcrypt_cbc(MCRYPT_BLOWFISH, $key, base64_decode($data), MCRYPT_DECRYPT, $iv);
	mcrypt_module_close($td);
	return $data;
}
?>
