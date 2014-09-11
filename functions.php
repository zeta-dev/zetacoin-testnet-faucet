<?php


$base58 = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
$hex = '0123456789ABCDEF';
$pattern = '^' . $addressprefix . '{1}[' . $base58 . ']{'. $addresslength . '}';
$rpcurl = 'http://' . $rpcuser . ':' . $rpcpass . '@' . $rpchost . ':' . $rpcport;
$ip = $_SERVER['REMOTE_ADDR'];

$rpcQuery = new jsonRPCClient("http://".$rpcuser.":".$rpcpass."@".$rpchost.":".$rpcport);

function dec2base($dec,$chars,$base) {
   $out = '';
   while (bccomp($dec,0) == 1) {
      $mod = bcmod($dec,$base);
      $dec = bcdiv($dec,$base,0);
      $out = $chars[$mod] . $out;
   }
   return $out;
}

function base2base($in,$inchars,$outchars) {
   $inbase = strlen($inchars);
   $outbase = strlen($outchars);
   $inlen = strlen($in);
   $out = '0';
   for ($i=0;$i<$inlen;$i++) {
      $pos = strpos($inchars,$in[$i]);
      $out = bcmul($out,$inbase,0);
      $out = bcadd($out,$pos,0);
   }
   return dec2base($out,$outchars,$outbase);
}

function test_address($address) {
   global $addressversion,$base58,$self,$hex,$link,$pattern,$period;
   global $rpcQuery;
   // check it's a valid address
   if ($address == $self)
      return -1;
   if (!preg_match( '/'.$pattern.'/', $address))
      return -2;
   $addrhex = base2base($address,$base58,$hex);
   if (strlen($addrhex) !== 50)
      return -3;
   if (substr($addrhex,0,2) !== $addressversion)
      return -4;
   $check = substr($addrhex,0,42);
   $check = pack('H*' , $check);
   $check = strtoupper(hash('sha256',hash('sha256',$check,true)));
   $check = substr($check,0,8);
   if ($check !== substr($addrhex,42))
      return -5;
   $result = $rpcQuery->validateaddress($address);
   if (!$result['isvalid'])
      return -6;
   if ($result['ismine'])
      return -1;
   // returns 0 for success, negative for error
   return 0;
}

function tosatoshi($amount) {
   return bcmul($amount,'100000000',0);
}

function fromsatoshi($amount) {
   return bcdiv($amount,'100000000',8);
}

function payout($address) {
   global $currency,$link,$minbal,$minpay,$maxpay,$walletpass;
   global $rpcQuery;
   $bal = $rpcQuery->getbalance();

   if ($bal <= $minbal)
      return false;
   $pay = rand($minpay,$maxpay);
	
   if($walletpass != '')
      $res = $rpcQuery->walletpassphrase($walletpass);

   $paid = $rpcQuery->sendtoaddress($address,$pay);
   if ($paid === false)
      return false;
   return array('amount' => $pay, 'tid' => $paid);
}

?>
