<?php 
	require 'MonCashBtn.php';
	$MonCashBtn = new MonCashBtn(
		'', // client_id
		''  // client_secret
	);

	$qte = intval($_POST['Qte']);

	$orderId = time(); // Set the Order ID to identify the sale 
	$price = $qte*1000; // Set the price for the sale 

	// var_dump($price);

	$paydata = $MonCashBtn->GetPayment($orderId);
	// var_dump($paydata);
	$result = (isset($paydata['status'])) ? $paydata['status'] : 404 ;
	$isPayed = boolval(in_array($result, array('200','202')));

	echo '<div class="FW MX600 DIB VC P10 TC FH PT100 lshad95">';
	echo '<div class="TL">';
	echo '<a href="./">Retour</a><br><br>';
	echo '</div>';

	if (!$isPayed) {
		$btndata = $MonCashBtn->SetPayment($price);
		// var_dump($btndata['message']);
		$result = (isset($btndata['payment_token'])) ? $btndata['payment_token'] : array() ;
		$payment_token = (isset($result['token'])) ? $result['token'] : '' ;
		$url = $MonCashBtn->URL($payment_token);
		// var_dump($url);

		$styles = array(
			'background-color: #FF0000',
			'color: #FFF',
			'text-align: center',
			'text-decoration: none',
			'text-transform: uppercase',
			'display: inline-block',
			'width: min(200px, 75%)',
			'padding: 5px 10px',
			'margin: 5px;border-radius: 3px'
		);
		// var_dump($payment_token);

		if ($payment_token == "") {
			echo '<i>Désolé connexion impossible. { '.$btndata['message'].' }</i>';
		}else{
			echo 'Votre commande a été enregistré avec succès<br>';
			echo 'Achat de '.$qte.' ticket<br>';
			echo 'Pou la somme de : '.number_format($price).'gdes<br>';
			echo '<a style="'.implode(';', $styles).'" href="'.$url.'">Payer avec MonCash</a>';
		}
	}else{
		echo '<i>Produit déjà vendu, merci.</i>';
	}
	echo '</div>';
 ?>