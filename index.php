<?php

require_once('jsonRPCClient.php');
include('config.php');
include('functions.php');

$balance = $rpcQuery->getbalance();
$difficulty = $rpcQuery->getdifficulty();

$page = '<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8"/>
 <title>' . $currency . ' Faucet</title>
</head>
<body>';
$footer = '
 <p>Faucet balance: '. $balance .'<br/>
 Testnet difficulty: '.$difficulty.'<br/>
 Send coin to the Faucet: ciRusE2SjCUYZXtQz7oF7kBYDgH8ZL2XSd</p>
 <p>Donate Zetacoin to: '. $donatezet .'<br/>Donate Bitcoin to: '. $donatebtc .'</p>
</body>
</html>';

// check if an address has been submitted, then if it's valid & if we've already paid it
if (isset($_POST['a'])) {
   $address = trim($_POST['a']);
   $test = test_address($address);
   switch ($test) {
      case 0:
         $pay = payout($address);
         if (is_array($pay))
            die ($page. '<p>Paid ' . $pay['amount'] . ' to ' . $address . ' in transaction id ' . $pay['tid'] . '</p>' . $footer);
         die ($page . '<p>Faucet is dry, please donate!<p>' . $footer);
         break;
      case $test < 0: $page .= '<p>Invalid ' . $currency . ' address, please try again. ' . $test . '</p>'; break;
   }
}

$page .= '
 <form id="faucet" method="post">
  <label for="a">Enter your ' . $currency .' address</label>
  <input type="text" name="a" id="a" maxlength="' . $maxaddrlength . '" size="' . $maxaddrlength . '" pattern="' . $pattern . '">
  <input type="submit" value="Get coins">
 </form>';

echo $page . $footer;

?>
