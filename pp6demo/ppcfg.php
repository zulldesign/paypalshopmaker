<?php

/**
 * PayPal Shop Maker 6
 * (C) 2008-2014 e.World Technology Limited. All rights reserved.
 */

// Show SQL for debug
define("EWPP_DEBUG_ENABLED", FALSE, TRUE); // Debug
if (EWPP_DEBUG_ENABLED) {
	@ini_set("display_errors", "1"); // Display errors
	error_reporting(E_ALL ^ E_NOTICE); // Report all errors except E_NOTICE
}
define("EWPP_IS_PHP5", ((float)phpversion() >= 5), TRUE); // Is PHP5
if (!EWPP_IS_PHP5)
	die("PHP 5 or later is required. You are running " . phpversion() . " only.");
define("EWPP_PROJECT_NAME", "paypalshopmaker", TRUE); // Project name
define("EWPP_IS_WINDOWS", (strtolower(substr(PHP_OS, 0, 3)) === 'win'), TRUE); // Is Windows OS
define("EWPP_PATH_DELIMITER", ((EWPP_IS_WINDOWS) ? "\\" : "/"), TRUE); // Physical path delimiter
define("EWPP_PHPMAILER_PATH", "phpmailer527" . EWPP_PATH_DELIMITER . "class.phpmailer.php", TRUE); 
define("EWPP_PHPMAILER_LANG_PATH", "phpmailer527" . EWPP_PATH_DELIMITER . "language", TRUE); 
define("EWPP_PHPMAILER_LANG", "en", TRUE);
if (!isset($ADODB_OUTP)) $ADODB_OUTP = 'ewpp_SetDebugMsg';
define("EWPP_DOWNLOAD_PAGE", "ewdnld.php", TRUE); // download page
define("EWPP_APPROVAL_PAGE", "approval.php", TRUE); // approval page
define("EWPP_FINISH_PAGE", "finish.php", TRUE); // finish page
define("EWPP_CART_VIEW_PAGE", "cartview.php", TRUE); // cartview page
define("EWPP_CHECKOUT_PAGE", "checkout.php", TRUE); // checkout page
define("EWPP_SHIP_PAGE", "shipping.php", TRUE); // shipping page
define("EWPP_CONFIRM_PAGE", "confirm.php", TRUE); // confirm page
define("EWPP_MENU_PAGE", "menupage.php", TRUE); // menu page
define("EWPP_CART_LIST_PAGE", "cartlist.php", TRUE); // cartlist page
define("EWPP_QUERY_PAGE", "ewquery.php", TRUE); // query page
define("EWPP_LOWERCASE_FILENAME", TRUE, TRUE);
define("EWPP_PROJECT_CHARSET", "utf-8", TRUE); // Project charset
define("EWPP_BODY_TITLE", "PayPal Shop Maker 6 Demo Project", TRUE); // Project title

// Note: Only licensed users are allowed to remove or change the following copyright statement.
define("EWPP_FOOTER_TEXT", "&copy;2014 e.World Technology Ltd. All rights reserved.", TRUE); // Project footer text

/**
 * Remove XSS
 * Note: If you want to allow these keywords, remove them from the following EWPP_XSS_ARRAY at your own risks.
*/
define("EWPP_REMOVE_XSS", TRUE, TRUE); // Remove XSS
$EWPP_XSS_ARRAY = array('javascript', 'vbscript', 'expression', '<applet', '<meta', '<xml', '<blink', '<link', '<style', '<script', '<embed', '<object', '<iframe', '<frame', '<frameset', '<ilayer', '<layer', '<bgsound', '<title', '<base',
'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

// Database settings
define("EWPP_DB_TYPE", strtoupper("ACCESS"), TRUE);
define("EWPP_IS_JSDB", (EWPP_DB_TYPE == "JAVASCRIPT"), TRUE); // 5.0
if (EWPP_DB_TYPE == "MYSQL") {
	define("EWPP_CONN_HOST", "localhost", TRUE);
	define("EWPP_CONN_PORT", 3306, TRUE);
	define("EWPP_CONN_USER", "root", TRUE);
	define("EWPP_CONN_PASS", "", TRUE);
	define("EWPP_CONN_DB", "paypal", TRUE);
	define("EWPP_NOW_FUNC", "NOW()", TRUE);
} elseif (EWPP_DB_TYPE == "ACCESS") {
	define("EWPP_CODEPAGE", 65001, TRUE); // Code page
	define("EWPP_NOW_FUNC", "NOW()", TRUE);
	define("EWPP_DB_FILENAME", "paypal.mdb", TRUE);
} elseif (EWPP_DB_TYPE == "SQLITE") {
	define("EWPP_NOW_FUNC", "datetime('now')", TRUE);
	define("EWPP_DB_FILENAME", "paypal.sqlite", TRUE);
}
define("EWPP_DB_QUOTE_START", ((EWPP_DB_TYPE == "ACCESS") ? "[" : "`"), TRUE);
define("EWPP_DB_QUOTE_END", ((EWPP_DB_TYPE == "ACCESS") ? "]" : "`"), TRUE);
define("EWPP_SELECT_LIMIT", (EWPP_DB_TYPE <> "ACCESS"), TRUE);

// Language file
define("EWPP_USE_DOM_XML", FALSE, TRUE);

// IPN
define("EWPP_MISSING_URL_START", '<--$url_', TRUE);
define("EWPP_MISSING_URL_END", "-->", TRUE);
define("EWPP_EMAIL_CONTENT_START", "*** Begin Email Content ***", TRUE);
define("EWPP_EMAIL_CONTENT_END", "*** End Email Content ***", TRUE);
define("EWPP_IPN_EMAIL_HTML", FALSE, TRUE);

// Option settings
define("EWPP_OPTION_SELECT_ONE", 0, TRUE);
define("EWPP_OPTION_SELECT_MULTIPLE", 1, TRUE);
define("EWPP_OPTION_RADIO", 2, TRUE);
define("EWPP_OPTION_CHECKBOX", 3, TRUE);
define("EWPP_OPTION_TEXT", 4, TRUE);
define("EWPP_OPTION_REPEAT_COLUMN", 5, TRUE);
define("EWPP_OPTION_SELECT_MULTIPLE_SIZE", 4, TRUE);
define("EWPP_OPTION_TEXTBOX_SIZE", 25, TRUE);
define("EWPP_OPTION_TEXTBOX_MAXLEN", 200, TRUE);
define("EWPP_REC_PER_ROW", 0, TRUE);
define("EWPP_CUSTOM_AS_TEXTAREA", FALSE, TRUE);

// PayPal settings
// Use PayPal

define("EWPP_USE_PAYPAL", TRUE, TRUE); 
define("EWPP_BUSINESS", "admin@zulldesign.ml", TRUE);
define("EWPP_SENDER_EMAIL", "sales@mycompany.com", TRUE);
define("EWPP_RECIPIENT_EMAIL", "sales@mycompany.com", TRUE);
if (EWPP_USE_PAYPAL) {
	define("EWPP_PAYPAL_URL", "https://www.paypal.com/cgi-bin/webscr", TRUE);
} else {
	define("EWPP_PAYPAL_URL", "ipn.php", TRUE);
}
define("EWPP_DEFAULT_PAGE", "index.html", TRUE);
define("EWPP_IDENTITY_TOKEN", "", TRUE);

// Locale/Currency settings
define("EWPP_CURRENCY_CODE", 'MYR', TRUE);
define("EWPP_USE_DEFAULT_LOCALE", TRUE, TRUE);
define("EWPP_DEFAULT_CURRENCY_SYMBOL", '$', TRUE);
define("EWPP_DEFAULT_MON_DECIMAL_POINT", '.', TRUE);
define("EWPP_DEFAULT_MON_THOUSANDS_SEP", ',', TRUE);
define("EWPP_DEFAULT_FRAC_DIGITS", 2, TRUE);
define("EWPP_DEFAULT_POSITIVE_SIGN", '', TRUE);
define("EWPP_DEFAULT_NEGATIVE_SIGN", '-', TRUE);
define("EWPP_DEFAULT_P_CS_PRECEDES", TRUE, TRUE);
define("EWPP_DEFAULT_P_SEP_BY_SPACE", FALSE, TRUE);
define("EWPP_DEFAULT_N_CS_PRECEDES", TRUE, TRUE);
define("EWPP_DEFAULT_N_SEP_BY_SPACE", FALSE, TRUE);
define("EWPP_DEFAULT_P_SIGN_POSN", 3, TRUE);
define("EWPP_DEFAULT_N_SIGN_POSN", 3, TRUE);

// SMTP settings
define("EWPP_SMTPSERVER", "localhost", TRUE);
define("EWPP_SMTPSERVER_PORT", 25, TRUE);
define("EWPP_SMTPSERVER_USERNAME", "", TRUE);
define("EWPP_SMTPSERVER_PASSWORD", "", TRUE);

// IPN / PDT / Digital Download settings
define("EWPP_IPN_ENABLED", FALSE, TRUE);
define("EWPP_PDT_ENABLED", FALSE, TRUE);
define("EWPP_DIGITAL_DOWNLOAD", FALSE, TRUE);
define("EWPP_DOWNLOAD_APPROVAL", FALSE, TRUE);
define("EWPP_DOWNLOAD_BINARY_WRITE", FALSE, TRUE);

// Other settings
define("EWPP_FOLDER_LEVEL", 0, TRUE); // script folder is root folder by default
define("EWPP_LOG_PATH", "download/log_02C69C4D3817FDC4600DA3FEAD5B3C25", TRUE); // Log folder relative to root
define("EWPP_DB_PATH", "download/db_02C69C4D3817FDC4600DA3FEAD5B3C25", TRUE); // Database folder relative to root
define("EWPP_RANDOM_KEY", "02C69C4D3817FDC4600DA3FEAD5B3C25", TRUE);
define("EWPP_WRITE_LOG_FILE", FALSE, TRUE);

// Download settings
define("EWPP_DOWNLOAD_TIMEOUT_UNIT", "h", TRUE); // Hour
define("EWPP_DOWNLOAD_TIMEOUT_INTERVAL", 24, TRUE); // Download timeout period
define("EWPP_DOWNLOAD_SRC_PATH", "download_02C69C4D3817FDC4600DA3FEAD5B3C25", TRUE); // Download source path relative to root
define("EWPP_DOWNLOAD_PATH", "download/", TRUE); // Download path relative to root
define("EWPP_UNREGISTERED_DOWNLOAD", "unregistered.jpg", TRUE);

// Parameters and Session names
define("EWPP_MENU_ID", "menu", TRUE);
define("EWPP_CATEGORY_ID", "cat", TRUE);
define("EWPP_SESSION_CATEGORY_ID", EWPP_PROJECT_NAME . "_categoryid", TRUE);
define("EWPP_ITEM_ID", "id", TRUE);
define("EWPP_START_REC", "start", TRUE);
define("EWPP_PAGE_NO", "page", TRUE);
define("EWPP_SESSION_PAGE_NO", EWPP_PROJECT_NAME . "_pagenumber", TRUE);

// Search keyword
define("EWPP_COMMAND", "cmd", TRUE);
define("EWPP_COMMAND_RESET", "reset", TRUE);
define("EWPP_COMMAND_RESETALL", "resetall", TRUE);
define("EWPP_SEARCH_KEYWORD", "keyword", TRUE);
define("EWPP_SESSION_SEARCH_KEYWORD", EWPP_PROJECT_NAME . "_searchkeyword", TRUE);

// Page index
define("EWPP_ALPHA_ID", "alpha", TRUE);
define("EWPP_SESSION_ALPHA_ID", EWPP_PROJECT_NAME . "_alphaid", TRUE);

// Function to quote table/field name
function ewpp_DbQuote($name) {

	$name = str_replace(EWPP_DB_QUOTE_END, EWPP_DB_QUOTE_END . EWPP_DB_QUOTE_END, $name);
	return EWPP_DB_QUOTE_START . $name . EWPP_DB_QUOTE_END;
}

// Note: Do NOT change the field names in the database!
define("EWPP_TABLENAME_PREFIX", "", TRUE);

// Table names (lowercase)
define("EWPP_TABLENAME_APPSTATUS", EWPP_TABLENAME_PREFIX . "appstatus", TRUE);
define("EWPP_TABLENAME_MENU", EWPP_TABLENAME_PREFIX . "menu", TRUE);
define("EWPP_TABLENAME_CATEGORY", EWPP_TABLENAME_PREFIX . "category", FALSE);
define("EWPP_TABLENAME_ITEM", EWPP_TABLENAME_PREFIX . "item", TRUE);
define("EWPP_TABLENAME_REGION", EWPP_TABLENAME_PREFIX . "region", TRUE);
define("EWPP_TABLENAME_COUNTRY", EWPP_TABLENAME_PREFIX . "country", TRUE);
define("EWPP_TABLENAME_STATE", EWPP_TABLENAME_PREFIX . "state", TRUE);
define("EWPP_TABLENAME_DISCOUNT", EWPP_TABLENAME_PREFIX . "discount", TRUE);
define("EWPP_TABLENAME_SHIPPINGMETHOD", EWPP_TABLENAME_PREFIX . "shippingmethod", TRUE);
define("EWPP_TABLENAME_SHIPPING", EWPP_TABLENAME_PREFIX . "shipping", TRUE);
define("EWPP_TABLENAME_SHIPPINGTYPE", EWPP_TABLENAME_PREFIX . "shippingtype", TRUE);
define("EWPP_TABLENAME_TAX", EWPP_TABLENAME_PREFIX . "tax", TRUE);
define("EWPP_TABLENAME_TXN", EWPP_TABLENAME_PREFIX . "transaction", TRUE);
define("EWPP_TABLENAME_LOG", EWPP_TABLENAME_PREFIX . "log", TRUE);
define("EWPP_TABLENAME_TXNDETAIL", EWPP_TABLENAME_PREFIX . "transactiondetail", TRUE); // 4.0
define("EWPP_TABLENAME_CATEGORYITEM", EWPP_TABLENAME_PREFIX . "categoryitem", TRUE); // 4.0
define("EWPP_TABLENAME_DISCOUNTTYPE", EWPP_TABLENAME_PREFIX . "discounttype", TRUE); // 4.0
define("EWPP_TABLENAME_DISCOUNTCODE", EWPP_TABLENAME_PREFIX . "discountcode", TRUE); // 4.0

// Custom queries
define("EWPP_COUNTRYLIST_SQL", "SELECT * FROM " .
	ewpp_DbQuote(EWPP_TABLENAME_COUNTRY) . " INNER JOIN " . ewpp_DbQuote(EWPP_TABLENAME_REGION) . " ON " .
	ewpp_DbQuote(EWPP_TABLENAME_COUNTRY) . ".CountryRegionId = " .
	ewpp_DbQuote(EWPP_TABLENAME_REGION) . ".RegionId WHERE CountryShow <> 0 AND RegionShow <> 0 ORDER BY CountryName", TRUE);

// State details (StateList) 
define("EWPP_STATELIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_STATE) . " WHERE StateShow <> 0 ORDER BY StateCountryId, StateCode", TRUE);

// Discount details (DiscountList) 
define("EWPP_DISCOUNTLIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_DISCOUNT) . " ORDER BY DiscountTypeId, DiscountQuantity", TRUE);

// Discount type details (DiscountTypeList) 
define("EWPP_DISCOUNTTYPELIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_DISCOUNTTYPE) . " ORDER BY DiscountTypeId", TRUE);

// - Shipping method (ShipMethodList) 
define("EWPP_SHIPMETHODLIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_SHIPPINGMETHOD) . " ORDER BY ShippingMethodDisplayOrder, ShippingMethodId", TRUE);

// - Shipping cost - quantity (ShipcostList0) 
define("EWPP_SHIPCOSTLIST0_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_SHIPPING) . " WHERE ShippingTypeCalcId = 0 ORDER BY ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingQty", TRUE);

// - Shipping cost - price (ShipcostList1) 
define("EWPP_SHIPCOSTLIST1_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_SHIPPING) . " WHERE ShippingTypeCalcId = 1 ORDER BY ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingPrice", TRUE);

// - Shipping cost - quantity (ShipcostList2) 
define("EWPP_SHIPCOSTLIST2_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_SHIPPING) . " WHERE ShippingTypeCalcId = 2 ORDER BY ShippingTypeId, ShippingMethodId, ShippingRegionId, ShippingCountryId, ShippingStateId, ShippingWeight", TRUE);

// - Shipping type (ShipTypeList) 
define("EWPP_SHIPTYPELIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_SHIPPINGTYPE) . " ORDER BY ShippingTypeId", TRUE);

// Tax details (TaxList) 
define("EWPP_TAXLIST_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_TAX) . " ORDER BY TaxTypeId, TaxRegionId, TaxCountryId, TaxStateId", TRUE);

// Menu SQL
define("EWPP_MENU_SELECT_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_MENU), TRUE);
define("EWPP_MENU_MENUID_FILTER", "MenuId = @@MenuId@@", TRUE);
define("EWPP_MENU_DEFAULT_ORDERBY", "MenuLevel, MenuDisplayOrder, MenuId", TRUE);

// Category SQL
define("EWPP_CATEGORY_SELECT_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_CATEGORY), TRUE);
define("EWPP_CATEGORY_CATEGORYID_FILTER", "CategoryId = @@CategoryId@@", TRUE);
define("EWPP_CATEGORY_DEFAULT_ORDERBY", "CategoryLevel, CategoryDisplayOrder, CategoryParentId", TRUE);

// Product SQL
define("EWPP_PRODUCT_SELECT_SQL", "SELECT * FROM " . ewpp_DbQuote(EWPP_TABLENAME_ITEM), TRUE);
define("EWPP_PRODUCT_ORDER_BY", "ItemDisplayOrder, ItemId", TRUE);
define("EWPP_PRODUCT_DEFAULT_FILTER", "ItemSelected <> 0", TRUE); // 4.0
define("EWPP_SEARCH_FIELDS", "ItemName", TRUE); // 5.0 // Can be comma separated field names
define("EWPP_INCLUDE_SUBCATEGORY", TRUE, TRUE); // Option to include sub categories
define("EWPP_PRODUCT_CATEGORY_FILTER", "ItemId IN (SELECT DISTINCT Item FROM " .
	ewpp_DbQuote(EWPP_TABLENAME_CATEGORYITEM) . " WHERE Category IN (@@CategoryId@@))", TRUE);
define("EWPP_PRODUCT_SELECT_SUBCATEGORY_SQL", "SELECT DISTINCT CategoryId FROM " . ewpp_DbQuote(EWPP_TABLENAME_CATEGORY), TRUE);
define("EWPP_PRODUCT_SUBCATEGORY_FILTER", "CategoryParentId IN (@@CategoryParentId@@)", TRUE);
define("EWPP_PRODUCT_ITEM_FILTER", "ItemId = @@ItemId@@", TRUE);
define("EWPP_USE_ITEM_COUNT", FALSE, TRUE); // Hide items with ItemCount <= 0
define("EWPP_PRODUCT_ITEMCOUNT_FILTER", "", TRUE);
define("EWPP_SHOW_SOLD_OUT", TRUE, TRUE); // Show sold out items (if use item count)
define("EWPP_SHOW_CATEGORY_PRODUCT_COUNT", FALSE, TRUE); // Show category product count
define("EWPP_SHOW_EMPTY_CATEGORY", TRUE, TRUE); // Show empty category
define("EWPP_SHOW_ALL_PRODUCTS_CATEGORY", FALSE, TRUE); // Show a category for all products
define("EWPP_PRODUCT_COUNT_CLASS_NAME", "badge", TRUE); // ewProductCount

// Search filter
define("EWPP_PRODUCT_SEARCH_FILTER", "ItemName LIKE '%@" . EWPP_SEARCH_KEYWORD . "@%' OR " .
	"ItemNumber LIKE '%@" . EWPP_SEARCH_KEYWORD . "@%'", TRUE);

// Search setting, possible options as follow
// - "" => Exact Match
// - "OR" => Any words
// - "AND" => All words

define("EWPP_SEARCH_TYPE", "OR", TRUE); // Any words
define("EWPP_SEARCH_ALL_CATEGORIES", FALSE, TRUE); // Search all categories

// Product SQL to build alphanumeric index
define("EWPP_PRODUCT_SELECT_COUNT_SQL",	"SELECT COUNT(*) FROM " . ewpp_DbQuote(EWPP_TABLENAME_ITEM), TRUE); // SQL for all categeories
$ewpp_AlphaNumericIndex = "";
$ewpp_AlphaNumericIndex  .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$ewpp_AlphaNumericIndex  .= "0123456789";
$ewpp_AlphaNumericIndex  .= "~";
define("EWPP_PRODUCT_ALPHANUMERIC_INDEX", $ewpp_AlphaNumericIndex, TRUE);

// Row classes
//###define("EWPP_ROW_CLASS_NAME", "ewTableRow", TRUE);
//define("EWPP_ALT_ROW_CLASS_NAME", "ewTableAltRow", TRUE);
// Image path

define("EWPP_IMAGE_PATH", "images", TRUE);

// Image type
define("EWPP_IMAGE_THUMBNAIL_LIST", "l_", TRUE);
define("EWPP_IMAGE_THUMBNAIL_VIEW", "v_", TRUE);
define("EWPP_IMAGE_FULL_VIEW", "", TRUE);

// Image width/height
define("EWPP_IMAGE_THUMBNAIL_WIDTH", 200, TRUE);
define("EWPP_IMAGE_THUMBNAIL_HEIGHT", 0, TRUE);
define("EWPP_IMAGE_THUMBNAIL_WIDTH_MOBILE", , TRUE);
define("EWPP_IMAGE_THUMBNAIL_HEIGHT_MOBILE", , TRUE);
define("EWPP_IMAGE_THUMBNAIL_WIDTH_VIEW", 250, TRUE);
define("EWPP_IMAGE_THUMBNAIL_HEIGHT_VIEW", 0, TRUE);

// embedSWF arguments: version, expressInstallSwfurl, flashvars, params, attributes, callbackFn
// Read: http://code.google.com/p/swfobject/wiki/documentation

define("EWPP_SWF_VERSION", "9.0", TRUE); 
define("EWPP_SWF_DEFAULT_WIDTH", 300, TRUE); // Default swf width if thumbnail width not specified
define("EWPP_SWF_DEFAULT_HEIGHT", 150, TRUE); // Default swf height if thumbnail height not specified
define("EWPP_AMOUNT_DIV_PREFIX", "pp_amount_", TRUE);
define("EWPP_REPLACE_CRLF", TRUE, TRUE);

/**
 * MySQL charset (for SET NAMES statement, use utf8 by default)
 * Note: Read http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
 * before changing this setting.
 */
define("EWPP_MYSQL_CHARSET", "utf8", TRUE);

// Menu
define("EWPP_MENUBAR_MENU_ID", "ewMenu", TRUE);
define("EWPP_MENUBAR_CAT_ID", "ewCategory", TRUE);
define("EWPP_MENUBAR_ROOTMENU_ID", "ewRootMenu", TRUE);
define("EWPP_MENUBAR_ROOTCAT_ID", "ewRootCat", TRUE);
define("EWPP_MENUBAR_CLASSNAME", "navbar-nav", TRUE);
define("EWPP_MENUBAR_ITEM_CLASSNAME", "navbar-nav", TRUE);
define("EWPP_MENUBAR_ITEM_LABEL_CLASSNAME", "navbar-nav", TRUE);
define("EWPP_SUBMENU_ITEM_CLASSNAME", "dropdown-submenu", TRUE); 
define("EWPP_SUBMENU_CLASSNAME", "dropdown-menu", TRUE);	
define("EWPP_MENULINK_DROPDOWN_IMAGE", "<span class=\"icon-arrow-right\"></span>", TRUE);
define("EWPP_SUBMENU_DROPDOWN_IMAGE", "<span class=\"icon-arrow-down\"></span>", TRUE);
define("EWPP_MENU_CLASSNAME", "dropdown-menu", TRUE);
define("EWPP_MENU_ITEM_CLASSNAME", "dropdown", TRUE); 
define("EWPP_MENU_ITEM_LABEL_CLASSNAME", "dropdown-submenu", TRUE); 
define("EWPP_MENU_ROOT_GROUP_TITLE_AS_SUBMENU", TRUE, TRUE);

/**
 * Character encoding
 * Note: If you use non English languages, you need to set character encoding
 * for some features. Make sure either iconv functions or multibyte string
 * functions are enabled and your encoding is supported. See PHP manual for
 * details.
 */
define("EWPP_ENCODING", "UTF-8", TRUE); // Character encoding

/**
 * Time zone (Note: Requires PHP 5 >= 5.1.0)
 * Read http://www.php.net/date_default_timezone_set for details
 * and http://www.php.net/timezones for supported time zones
*/
if (function_exists("date_default_timezone_set"))
	date_default_timezone_set("GMT"); // Note: Change the timezone_identifier here

/**
 * Global variables
*/

// Page ID
$EWPP_PAGE_ID = "";

// Email
$ewpp_EmailFrom = "";
$ewpp_EmailTo = "";
$ewpp_EmailCc = "";
$ewpp_EmailBcc = "";
$ewpp_EmailSubject = "";
$ewpp_EmailFormat = "";
$ewpp_EmailContent = "";
$ewpp_EmailError = "";

// Menu details
$ewpp_MenuId = NULL;
$ewpp_MenuLink = NULL;
$ewpp_MenuGen = NULL;
$ewpp_MenuFn = NULL;
$ewpp_MenuUrl = NULL;
$ewpp_MenuParentId = NULL;
$ewpp_MenuPageContent = NULL;

// Category
$ewpp_CatId = NULL;
$ewpp_CatName = "";
$ewpp_CatPath = "";

// Cart details
$ewpp_Item = array();

// Mobile detect
$MobileDetect = NULL;

// Debug message
$gsDebugMsg = "";

// Language
$PPLanguage = NULL;
$EWPP_LANG_PATH = "pplang"; // language file folder
$_SESSION["EWPP_LANG_FILE"] = "english.xml"; // Change language file here

// Connection
$conn = NULL;
?>
