<?php /**
 * Class de gestion du boutton MonCash
 */
class MonCashBtn
{
	private $token;
	private $live;
	private $url;
	private $btnurl;

	public function URL(string $value='')
	{
		return $this->btnurl.$value;
	}

	private function UseAPI(string $url='', string $method="GET", array $headers=array(), $data=array())
	{
		// Initialize curl
		$curl = curl_init();

		// Set curl options
		curl_setopt($curl, CURLOPT_URL, $url); // Set URL
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); // Set method
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Set headers
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Set data
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as string
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		// Execute curl and get response
		$response = curl_exec($curl);
		// var_dump($response);

		// var_dump(curl_errno($curl));
		// Check for errors
		if (curl_errno($curl)) {
			// Return error message
			return 'Curl error: #'. curl_errno($curl) . ' : {' . curl_error($curl).'} ';
		} else {
			// Close curl and return response
			curl_close($curl);
			return $response;
		}
	}

	function __construct(string $client_id, string $client_secret, false $live=false)
	{
		$this->live = $live;
		$pref = ($live) ? '' : 'sandbox.' ;
		$srv = $pref.'moncashbutton.digicelgroup.com/Api';
		$this->url = 'https://'.$client_id.':'.$client_secret.'@'.$srv;

		$gburl = $pref.'moncashbutton.digicelgroup.com/Moncash-middleware' ;
		$this->btnurl = 'https://'.$gburl.'/Payment/Redirect?token=';

		$this->token = $this->GetToken();
	}

	private function GetToken()
	{
		$res = $this->UseAPI($this->url.'/oauth/token', 'POST', 
			array(
				"Content-Type"=>"application/json"
			),
			array(
				"scope"=>"read,write",
				"grant_type"=>"client_credentials"
			)
		);
		$res = json_decode($res, true);
		// var_dump($res);
		$api_token = (isset($res['access_token'])) ? $res['access_token'] : '' ;
		// var_dump($api_token);
		return $api_token;
	}

	public function SetPayment(int $amount, int $orderId=0)
	{
		$orderId = ($orderId == 0) ? time() : $orderId ;
		if ($this->token == "") {
			$res = array();
		}else{
			$res = $this->UseAPI($this->url.'/v1/CreatePayment', 'POST', 
				array(
					"Accept: application/json",
					"Content-Type: application/json",
					"Authorization: Bearer $this->token",
				),
				json_encode(array(
					"amount"=>$amount,
					"orderId"=>$orderId,
				))
			);
			$res = json_decode($res, true);
			// var_dump($res);
		}
		return $res;
	}

	public function GetPayment(int $id, bool $ByTransactionId=false)
	{
		$sufx = ($ByTransactionId) ? '/v1/RetrieveTransactionPayment' : '/v1/RetrieveOrderPayment' ;
		$keyname = ($ByTransactionId) ? 'transactionId' : 'orderId' ;
		if ($this->token == "") {
			$res = array();
		}else{
			$res = $this->UseAPI($this->url.$sufx, 'POST', 
				array(
					"Accept: application/json",
					"Content-Type: application/json",
					"Authorization: Bearer $this->token",
				),
				json_encode(array(
					"$keyname"=>$id,
				))
			);
			$res = json_decode($res, true);
			// var_dump($res);
		}
		return $res;
	}

	public function SetTransfer(int $amount, string $receiver, string $desc='')
	{
		$desc = ($desc == "") ? "Transfer de $amount au numero $receiver" : $desc ;
		$orderId = ($orderId == 0) ? time() : $orderId ;
		if ($this->token == "") {
			$res = array();
		}else{
			$res = $this->UseAPI($this->url.'/v1/Transfert', 'POST', 
				array(
					"Accept: application/json",
					"Content-Type: application/json",
					"Authorization: Bearer $this->token",
				),
				json_encode(array(
					"amount"=>$amount,
					"receiver"=>$receiver,
					"desc"=>$desc,
				))
			);
			$res = json_decode($res, true);
			// var_dump($res);
		}
		return $res;
	}
} ?>