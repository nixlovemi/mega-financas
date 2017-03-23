<?php
class objEncrypt {
	// private $secretkey = '[very bad Italian accent] Arriverderci';
	
	// Encrypts a string
	public function encrypt($text) {
		// $data = mcrypt_encrypt ( MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, 'keee' );
		// return base64_encode ( $data );
		return base64_encode($text);
	}
	
	// Decrypts a string
	public function decrypt($text) {
		// $text = base64_decode ( $text );
		// return mcrypt_decrypt ( MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, 'keee' );
		return base64_decode($text);
	}
}
?>