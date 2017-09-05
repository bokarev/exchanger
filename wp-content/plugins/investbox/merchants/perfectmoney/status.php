<?php
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}

include_once('../../../../../wp-load.php');
header('Content-Type: text/html; charset=utf-8');
global $wpdb;
	
$sPayeeAccount = isset( $_POST['PAYEE_ACCOUNT'] ) ? trim( $_POST['PAYEE_ACCOUNT'] ) : null;
$iPaymentID = isset( $_POST['PAYMENT_ID'] ) ? $_POST['PAYMENT_ID'] : null;
$dPaymentAmount = isset( $_POST['PAYMENT_AMOUNT'] ) ? trim( $_POST['PAYMENT_AMOUNT'] ) : null;
$sPaymentUnits = isset( $_POST['PAYMENT_UNITS'] ) ? trim( $_POST['PAYMENT_UNITS'] ) : null;
$iPaymentBatch = isset( $_POST['PAYMENT_BATCH_NUM'] ) ? trim( $_POST['PAYMENT_BATCH_NUM'] ) : null;
$sPayerAccount = isset( $_POST['PAYER_ACCOUNT'] ) ? trim( $_POST['PAYER_ACCOUNT'] ) : null;
$sTimeStampGMT = isset( $_POST['TIMESTAMPGMT'] ) ? trim( $_POST['TIMESTAMPGMT'] ) : null;
$sV2Hash = isset( $_POST['V2_HASH'] ) ? trim( $_POST['V2_HASH'] ) : null;

if( !in_array( $sPaymentUnits, array( 'USD', 'EUR' ) ) ){
	the_inex_warning('The wrong type of currency');
}

if( $sV2Hash != strtoupper( md5( $iPaymentID.':'.$sPayeeAccount.':'.$dPaymentAmount.':'.$sPaymentUnits.':'.$iPaymentBatch.':'.$sPayerAccount.':'.strtoupper( md5( THE_PM_ALTERNATE_PHRASE ) ).':'.$sTimeStampGMT ) ) ){
	the_inex_warning( 'Incorrect control signature' );
}

if( $sPayeeAccount != constant( 'THE_PM_'.substr( $sPayeeAccount, 0, 1 ).'_ACCOUNT' ) ){
    the_inex_warning( 'Invalid account Seller' );
}

$theid = intval($iPaymentID);
the_payed_deposit($theid,$dPaymentAmount,$sPaymentUnits, __('Archived','inex'),__('Uncompleted','inex'));