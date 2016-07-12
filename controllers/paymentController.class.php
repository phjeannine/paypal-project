<?php

class paymentController
{
  public function indexAction($args)
  {
    $v = new view("createIndex");
    $v->assign("mesargs", $args);
  }

  public function createAction($args)
  {
    //product + price from the form
    $price = $_POST['price'];
    $product = $_POST['name'];
    $currency = $_POST['currency'];

    $token = $this->accessToken();

    $createPayment = $this->createPayment($token, $price, $product, $currency);

    $payID = $createPayment['id'];
    $payURL = $createPayment['url'];
    $clientID = $createPayment['clientID'];

    header('Location:' . $payURL);
  }

  public function payAction($args)
  {

    $_SESSION['paymentID'] = $_GET['paymentId'];
    $_SESSION['tokenClient'] = $_GET['token'];
    $_SESSION['$payerID'] = $_GET['PayerID'];

    $paymentID = $_SESSION['paymentID'];
    //Token Client si on veut géré les remboursements ou autre plus tard
    $tokenClient = $_SESSION['tokenClient'];
    $payerID = $_SESSION['$payerID'];

    $token = $this->accessToken();
    $payment = $this->executePay($paymentID, $payerID, $token);

    if (isset ($payment)) {
      $pay = $this->getPay($paymentID, $token);

      $v = new view("paymentSuccess");
      $v->assign("args", $pay);
    } else {
      header('Location: /payment/fail');
    }
  }

  /**
   * @return mixed
   * Get the accessToken for using RESTAPI from PayPal
   */
  public function accessToken()
  {
    $ch = curl_init();

    //
    // A REMPLIR AVEC VOS ID ! Trouvable sur paypal dev avec votre compte
    //

    $clientId = "";
    $secret = "";

    curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $result = curl_exec($ch);

    if (empty($result)) die("cURL error : Please check the data and curl setopt. Or PayPal is unreachable");
    else {
      $json = json_decode($result);
      $token = ($json->access_token);

    }
    curl_close($ch);
    return $token;
  }

  /**
   * @param $token
   * @param $price
   * @param $product
   * @param $currency
   * @return array
   * Create the payment for redirecting the user to PayPal Website and accept the pay
   */
  public function createPayment($token, $price, $product, $currency)
  {
    $ch = curl_init();

    $data = '{
			"intent":"sale",
			"redirect_urls":{
				"return_url":"http://' . $_SERVER["SERVER_NAME"] . '/payment/pay/args",
				"cancel_url":"http://' . $_SERVER["SERVER_NAME"] . '/payment/fail/token"
			},
			"payer":{
				"payment_method":"paypal"
			},
			"transactions":[
				{
					"amount":{
						"total":"' . $price . '",
						"currency":"' . $currency . '"
					},
					"description":"Payment from Project ESGI",
					"item_list":{
                      "items":[
                        {
                          "name":"' . $product . '",
                          "quantity":"1",
                          "price":"' . $price . '",
                          "currency":"' . $currency . '"
                        }
                      ]
                    }
				}
			]
		}';

    curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $token)
    );

    $result = curl_exec($ch);

    if (empty($result)) die("cURL error : Please check the data and curl setopt. Or PayPal is unreachable");
    else {
      $json = json_decode($result, true);
      if ($json['state'] == 'created') {
        $payID = ($json['id']);
        $payURL = ($json['links'][1]['href']);
//			$clientID = preg_split('/[^=]+$/', $json['links'][1]['href']);
        $clientID = substr(strrchr($json['links'][1]['href'], "="), 1);
      }
      else {
        var_dump($json);
        die("cURL error : JSON malformed");
      }
    }
    curl_close($ch);
    return array(
      'id' => $payID,
      'url' => $payURL,
      'clientID' => $clientID
    );
  }

  /**
   * @param $paymentID
   * @param $payerID
   * @param $token
   * @return mixed
   * Execute the pay and return the state. Return for Redirect to success or fail function.
   */
  public function executePay($paymentID, $payerID, $token)
  {

    $ch = curl_init();

    $data = '{ "payer_id" : "' . $payerID . '" }';

    curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/" . $paymentID . "/execute/");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $token)
    );

    $result = curl_exec($ch);

    if (empty($result)) die("cURL error : Please check the data and curl setopt. Or PayPal is unreachable");
    else {
      $json = json_decode($result, true);

      if (isset ($json['state'])) {
        $state = $json['state'];
      }
    }
    curl_close($ch);

    if (isset ($json['state'])) {
      return $state;
    }
  }

  /**
   * @param $paymentID
   * @param $token
   * @return array
   * Function for retrieving the pay to resume the order for the buyer
   */
  public function getPay($paymentID, $token)
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment/" . $paymentID);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $token)
    );

    $result = curl_exec($ch);

    if (empty($result)) die("cURL error : Please check the data and curl setopt. Or PayPal is unreachable");
    else {
      $json = json_decode($result, true);
      $email = $json['payer']['payer_info']['email'];
      $firstName = $json['payer']['payer_info']['first_name'];
      $lastName = $json['payer']['payer_info']['last_name'];
      $address = $json['payer']['payer_info']['shipping_address'];
    }
    curl_close($ch);

    return array(
      'email' => $email,
      'firstName' => $firstName,
      'lastName' => $lastName,
      'address' => $address
    );

  }

  /**
   * @param $args
   * Redirect for canceling the payment or when the pay fail.
   */
  public function failAction($args)
  {
    $v = new view("paymentFail");
    $v->assign("mesargs", $args);
  }

  /**
   * @param $args
   * Redirect when the state of the pay is successfull
   */
  public function successAction($args)
  {
    $v = new view("paymentSuccess");
    $v->assign("mesargs", $args);
  }
}