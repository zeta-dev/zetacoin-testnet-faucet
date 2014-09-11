<?php

require_once('jsonRPCClient.php');
include('config.php');
include('functions.php');

$balance = $rpcQuery->getbalance();
$difficulty = $rpcQuery->getdifficulty();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title><?php echo $currency; ?> Faucet</title>
  <meta name="viewport" content="width=1000, initial-scale=1.0, maximum-scale=1.0">

  <!-- Loading Bootstrap -->
  <link href="css/vendor/bootstrap.min.css" rel="stylesheet">
  <!-- Loading Flat UI -->
  <link href="css/flat-ui.min.css" rel="stylesheet">
  <link href="css/main.css" rel="stylesheet">
  <link rel="shortcut icon" href="img/favicon.png">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
  <!--[if lt IE 9]>
    <script src="dist/js/vendor/html5shiv.js"></script>
    <script src="dist/js/vendor/respond.min.js"></script>
  <![endif]-->
</head>
<body>
  <div class="container">
    <h1><img id="logo" src="img/zetacoin-logo.png"/><?php echo $currency;?> Faucet</h1>
<?php
// check if an address has been submitted, then if it's valid & if we've already paid it
if (isset($_POST['a'])) {
   $address = trim($_POST['a']);
   $test = test_address($address);
   switch ($test) {
      case 0:
         $pay = payout($address);
         if (is_array($pay))
            die ('<p>Paid ' . $pay['amount'] . ' to ' . $address . ' in transaction id ' . $pay['tid'] . '</p>' . $footer);
         die ('<p>Faucet is dry, please donate!<p>' . $footer);
         break;
      case $test < 0: echo '<p>Invalid ' . $currency . ' address, please try again. ' . $test . '</p>'; break;
   }
}

?>
    <div class="row demo-row">
      <div class="col-xs-6 box-center">
        <form id="faucet" method="post">
          <div class="form-group">
            <label class="form-label" for="a">Enter your <?php echo $currency; ?> address</label>
            <input type="text" name="a" id="a" maxlength="<?php echo $maxaddrlength; ?>" size="<?php echo $maxaddrlength;?>" pattern="<?php echo $pattern;?>" value="" placeholder="<?php echo $currency;?> Address" class="form-control" />
            <br/>
            <input class="btn btn-block btn-lg btn-primary" type="submit" value="Get coins">
          </div>
        </form>
      </div> 
    </div>
    <p>Faucet balance: <?php echo  $balance;?><br/>
    Testnet difficulty: <?php echo $difficulty;?><br/>
    Send coin to the Faucet: ciRusE2SjCUYZXtQz7oF7kBYDgH8ZL2XSd</p><br/>
    <p>Donate Zetacoin to: <?php echo  $donatezet;?><br/>
    Donate Bitcoin to: <?php echo  $donatebtc;?></p>
    </div>
    <script src="js/vendor/jquery.min.js"></script>
    <script src="js/flat-ui.min.js"></script>
  </body>
</html>