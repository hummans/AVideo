<?php

header('Content-Type: application/json');

if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../../../../';
}
require_once $global['systemRootPath'] . 'videos/configuration.php';
require_once $global['systemRootPath'] . 'objects/user.php';

$plugin = YouPHPTubePlugin::loadPluginIfEnabled("PayPalYPT");
$pluginS = YouPHPTubePlugin::loadPluginIfEnabled("YPTWallet");
$objS = $pluginS->getDataObject();
$options = json_decode($objS->addFundsOptions);

$obj= new stdClass();
$obj->error = true;

if(empty($_REQUEST['interval'])){
    $interval = 1;
}else{
    $interval = $_REQUEST['interval'];
}
if(empty($_POST['value']) || !in_array($_POST['value'], $options)){ 
    $obj->msg = "Invalid Value";
    die(json_encode($obj));
}
$invoiceNumber = uniqid();
if(empty($_REQUEST['paymentName'])){
    $paymentName = "Recurrent Payment";
}else{
    $paymentName = $_REQUEST['paymentName'];
}

//setUpSubscription($invoiceNumber, $redirect_url, $cancel_url, $total = '1.00', $currency = "USD", $frequency = "Month", $interval = 1, $name = 'Base Agreement')
$payment = $plugin->setUpSubscription($invoiceNumber, $objS->RedirectURL, $objS->CancelURL, $_POST['value'], $objS->currency, "Day",$interval, $paymentName);
if (!empty($payment)) {
    $obj->error = false;
    $obj->approvalLink = $payment->getApprovalLink();
}
die(json_encode($obj));