<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/PHPMailer/PHPMailerAutoload.php';

class Mailer{

	private $phpMailer;

	public function __construct(){

		$this->phpMailer = new PHPMailer();
		
		// alguns padroes
		$this->isHTML(true);
		$this->useSMTP();
		$this->useAuthSMTP(true);
		$this->setHost('vps.pessoahost.com.br');
		$this->setUsername($_SERVER['BIRDS_SYSTEM_MAIL_ADDR']);
		$this->setPassword($_SERVER['BIRDS_SYSTEM_MAIL_PASS']);
		$this->setSecureSMTP('ssl');
		$this->setPort(465);
		$this->setFromName('Mega Finanças');
		$this->setFromMail($_SERVER['BIRDS_SYSTEM_MAIL_ADDR']);
		// --------------
	
	}

	/**
	 * informa se email sera HTML
	 *
	 * @param string $ehHTML        	
	 * @return void
	 */
	public function isHTML($isHTML = true){

		$this->phpMailer->isHTML($isHTML);
	
	}

	/**
	 * seta a conexao sendo SMTP
	 *
	 * @return void
	 */
	private function useSMTP(){

		$this->phpMailer->isSMTP();
	
	}

	/**
	 * usa ou nao autenticacao no smtp
	 *
	 * @param boolean $authSMTP        	
	 * @return void
	 */
	private function useAuthSMTP($authSMTP = true){

		$this->phpMailer->SMTPAuth = $authSMTP;
	
	}

	/**
	 * seta o host - pode separar com ;
	 *
	 * @param string $host        	
	 * @return void
	 */
	private function setHost($host){

		$this->phpMailer->Host = $host;
	
	}

	/**
	 * seta o nome de usuario da conexao
	 *
	 * @param string $username        	
	 * @return void
	 */
	private function setUsername($username){

		$this->phpMailer->Username = $username;
	
	}

	/**
	 * seta a senha da conexao
	 *
	 * @param string $password        	
	 * @return void
	 */
	private function setPassword($password){

		$this->phpMailer->Password = $password;
	
	}

	/**
	 * define o tipo de seguranca SMTP
	 *
	 * @param string $secure
	 *        	('tls' || 'ssl')
	 * @return void
	 */
	private function setSecureSMTP($secure){

		switch ($secure) {
			case 'tls' :
			case 'ssl' :
				$this->phpMailer->SMTPSecure = $secure;
				break;
			default :
				$this->phpMailer->SMTPSecure = "tls";
				break;
		}
	
	}

	/**
	 * seta a porta de conecao tcp
	 *
	 * @param integer $port        	
	 * @return void
	 */
	private function setPort($port){

		$this->phpMailer->Port = $port;
	
	}

	/**
	 * seta email do remetente
	 *
	 * @param string $from_mail        	
	 * @return void
	 */
	private function setFromMail($from_mail){

		$this->phpMailer->From = $from_mail;
	
	}

	/**
	 * seta nome do remetente
	 *
	 * @param string $from_name        	
	 * @return void
	 */
	private function setFromName($from_name){

		$this->phpMailer->FromName = $from_name;
	
	}

	/**
	 * adiciona email/nome para resposta
	 *
	 * @param string $reply_mail        	
	 * @param string $reply_name        	
	 */
	public function setReplyTo($reply_mail, $reply_name){

		$this->phpMailer->addReplyTo($reply_mail, $reply_name);
	
	}

	/**
	 * adiciona um destinatario
	 *
	 * @param string $to_mail        	
	 * @param string $to_name        	
	 * @return void
	 */
	public function addToMail($to_mail, $to_name = ""){

		if ($to_name != "") {
			$this->phpMailer->addAddress($to_mail, $to_name);
		}
		else {
			$this->phpMailer->addAddress($to_mail);
		}
	
	}

	/**
	 * seta email copia
	 *
	 * @param string $cc_mail        	
	 * @return void
	 */
	public function addCcMail($cc_mail){

		$this->phpMailer->addCC($cc_mail);
	
	}

	/**
	 * seta email copia oculta
	 *
	 * @param string $bcc_mail        	
	 * @return void
	 */
	public function addBccMail($bcc_mail){

		$this->phpMailer->addBCC($bcc_mail);
	
	}

	/**
	 * adiciona um anexo
	 *
	 * @param string $attach_path        	
	 * @param string $attach_name        	
	 */
	public function addAttachment($attach_path, $attach_name = ""){

		if ($attach_name != "") {
			$this->phpMailer->addAttachment($attach_path, $attach_name);
		}
		else {
			$this->phpMailer->addAttachment($attach_path);
		}
	
	}

	/**
	 * define o assunto
	 *
	 * @param string $subject        	
	 * @return void
	 */
	public function setSubject($subject){

		$this->phpMailer->Subject = $subject;
	
	}

	/**
	 * define o corpo
	 *
	 * @param string $body        	
	 * @return void
	 */
	public function setBody($body){

		$this->phpMailer->Body = $body;
	
	}

	/**
	 * envia o email
	 * 
	 * @return boolean
	 */
	public function send(){

		return $this->phpMailer->send();
	
	}

}

?>