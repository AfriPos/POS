<?php
class serviceRenewal{
  public static function renewService(){
    // if(isset($_POST['payment'])){
        $element="mpesa";
        $table="customers";
        $countAll=null;
        $organisationcode=$_SESSION["organizationcode"];

        $show=packagevalidateController::ctrPackageValidate($element,$table,$countAll, $organisationcode);
        // var_dump($show);


      date_default_timezone_set('Africa/Nairobi');

      # access token
      $consumerKey = 'Unj2vn2hGwhCwYmgP48t0iwLxrgTU2pB'; //Fill with your app Consumer Key
      $consumerSecret = 'UXQ0thnWDyAYrXIE'; // Fill with your app Secret

      # define the variales
      # provide the following details, this part is found on your test credentials on the developer account
      $BusinessShortCode = '174379';
      $Passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';  
      
      $PartyA =$show["phone"]; // This is your phone number, 
      $AccountReference =$show["phone"];
      $TransactionDesc = $organisationcode;
      $Amount = $show["price"];
    
      # Get the timestamp, format YYYYmmddhms -> 20181004151020
      $Timestamp = date('YmdHis');    
      
      # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
      $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);

      # header for access token
      $headers = ['Content-Type:application/json; charset=utf8'];

        # M-PESA endpoint urls
      $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
      $initiate_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

      # callback url
      $CallBackURL = 'https://test1.afripos.co.ke/paymentapi/callback_url.php';  

      $curl = curl_init($access_token_url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($curl, CURLOPT_HEADER, FALSE);
      curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
      $result = curl_exec($curl);
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      $result = json_decode($result);
      $access_token = $result->access_token;  
      curl_close($curl);

      # header for stk push
      $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];

      # initiating the transaction
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $initiate_url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

      $curl_post_data = array(
        //Fill in the request parameters with valid values
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $Amount,
        'PartyA' => $PartyA,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $PartyA,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => $AccountReference,
        'TransactionDesc' => $TransactionDesc
      );

      $data_string = json_encode($curl_post_data);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
      $curl_response = curl_exec($curl);

      echo $curl_response;
    // };

  }
  

}
?>