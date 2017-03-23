<?php
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/connection/Connection.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/library/obj-status/ObjStatus.class.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Usuario.entity.php';
require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Validation.class.php';

class UsuarioService{

	const SENHA_TAMANHO_MIN = 8;

	private $objStatus;

	private $objConn;

	private $objValidation;

	function __construct(){

		$this->objStatus = new ObjStatus();
		$this->objConn = new Connection();
		$this->objValidation = new Validation();
	
	}

	/**
	 * Retorna a entidade; busca por id (PK)
	 * Chave para entidade = ent
	 *
	 * @param VARIANT $id        	
	 * @return ObjStatus
	 */
	public function buscaPorId($id){

		if (! is_numeric($id)) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'id inválido para busca!');
			return $this->objStatus;
		}
		
		$sql = 'SELECT usu_id, usu_nome, usu_sobrenome, usu_email, usu_senha, usu_salt, usu_cad_liberado
  				FROM tb_usuario
				WHERE usu_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($id);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse id (' . $id . ')');
			return $this->objStatus;
		}
		else {
			$usu_id = $rs ['usu_id'];
			$usu_nome = $rs ['usu_nome'];
			$usu_sobrenome = $rs ['usu_sobrenome'];
			$usu_email = $rs ['usu_email'];
			$usu_senha = $rs ['usu_senha'];
			$usu_salt = $rs ['usu_salt'];
			$usu_cad_liberado = $rs ['usu_cad_liberado'];
			
			$usuario = new Usuario($usu_id, $usu_nome, $usu_sobrenome, $usu_email, $usu_senha, $usu_salt, $usu_cad_liberado);
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuario);
			return $this->objStatus;
		}
	
	}
	
	/**
	 * 
	 * @param string $email
	 * @return ObjStatus
	 */
	public function buscaPorEmail($email){
	
		$sql = 'SELECT usu_id
  				FROM tb_usuario
				WHERE usu_email = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($email);
		$rs = $this->objConn->selectRow();
	
		if (! count($rs) > 0) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Nenhum registro encontrado com esse email (' . $email . ')');
			return $this->objStatus;
		}
		else {
			$usu_id = $rs ['usu_id'];
			$objResp = $this->buscaPorId($usu_id);
			
			if($objResp->isErro()){
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
				$this->objStatus->addRet('msg', 'Erro ao buscar usu&aacute;rio com id = ' . $usu_id);
				return $this->objStatus;
			}
			else{
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
				$this->objStatus->addRet('ent', $objResp->getRetByKey("ent"));
				return $this->objStatus;
			}
		}
	
	}

	/**
	 * Insere o Usuario no BD.
	 * Chaves resposta: msg, ent
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	public function insere(Usuario $usuario){

		if (! is_a($usuario, 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Usuario');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaInsere($usuario);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		// senha
		$salt = $this->geraRandomSalt();
		$password = $this->geraHashedPass($usuario->getSenha(), $salt);
		
		$usuario->setSenha($password);
		$usuario->setSalt($salt);
		// -----
		
		// prepara SQL
		if (is_numeric($usuario->getId())) {
			$sql = 'INSERT INTO tb_usuario(usu_id, usu_nome, usu_sobrenome, usu_email, usu_senha, usu_salt, usu_cad_liberado)
    				VALUES (?, ?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
			$this->objConn->addParameter($usuario->getId());
		}
		else {
			$sql = 'INSERT INTO tb_usuario(usu_nome, usu_sobrenome, usu_email, usu_senha, usu_salt, usu_cad_liberado)
    				VALUES (?, ?, ?, ?, ?, ?)';
			$this->objConn->setSQL($sql);
		}
		
		$this->objConn->addParameter($usuario->getNome());
		$this->objConn->addParameter($usuario->getSobrenome());
		$this->objConn->addParameter($usuario->getEmail());
		$this->objConn->addParameter($usuario->getSenha());
		$this->objConn->addParameter($usuario->getSalt());
		$this->objConn->addParameter($usuario->getCadLiberado());
		
		$returnField = $this->objConn->insert('usu_id');
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuario = new Usuario();
			$objRet = $this->buscaPorId($returnField [0]);
			if ($objRet->isOk()) {
				$usuario = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuario);
			$this->objStatus->addRet('msg', 'Usuário incluído com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao inserir Usuário.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a insercao.
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	private function validaInsere(Usuario $usuario){
		
		$eh_twitter = ((substr($usuario->getEmail(), 0, 1) == "@") && ($usuario->getCadLiberado() == "t"));
		if ((!$eh_twitter) && ((strlen($usuario->getEmail()) <= 3) || (! $this->objValidation->validateEmail($usuario->getEmail())))) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Email inválido!');
			return $this->objStatus;
		}
		
		$objStatus = $this->validaSenha($usuario->getSenha());
		if ($objStatus->isErro()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', $objStatus->getRetByKey('msg'));
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * faz a edicao do Usuario
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	public function edita(Usuario $usuario){

		if (! is_a($usuario, 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Usuario');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaEdita($usuario);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'UPDATE tb_usuario
   				SET usu_nome = ?, usu_sobrenome = ?, usu_email = ?, usu_cad_liberado = ?, usu_senha = ?
 				WHERE usu_id = ?';
		
		$this->objConn->setSQL($sql);
		
		$this->objConn->addParameter($usuario->getNome());
		$this->objConn->addParameter($usuario->getSobrenome());
		$this->objConn->addParameter($usuario->getEmail());
		$this->objConn->addParameter($usuario->getCadLiberado());
		$this->objConn->addParameter($usuario->getSenha());
		$this->objConn->addParameter($usuario->getId());
		
		$returnField = $this->objConn->update();
		// -----------
		
		if (! $this->objConn->getError()) {
			// versao atual da Entidade
			$usuario = new Usuario();
			$objRet = $this->buscaPorId($usuario->getId());
			if ($objRet->isOk()) {
				$usuario = $objRet->getRetByKey('ent');
			}
			// ------------------------
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuario);
			$this->objStatus->addRet('msg', 'Usuário editado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao editar Usuario.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 * Valida a edicao
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	private function validaEdita(Usuario $usuario){

		if (! is_numeric($usuario->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválido!');
			return $this->objStatus;
		}
		
		$eh_twitter = ((substr($usuario->getEmail(), 0, 1) == "@") && ($usuario->getCadLiberado() == "t"));
		if ((!$eh_twitter) && ((strlen($usuario->getEmail()) <= 3) || (! $this->objValidation->validateEmail($usuario->getEmail())))) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Email inválido!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	public function deleta(Usuario $usuario){

		if (! is_a($usuario, 'Usuario')) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Variável não é Usuario');
			return $this->objStatus;
		}
		
		// valida
		$statusValidacao = $this->validaDeleta($usuario);
		if ($statusValidacao->isErro()) {
			return $statusValidacao;
		}
		// ------
		
		$sql = 'DELETE FROM tb_usuario
 				WHERE usu_id = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($usuario->getId());
		
		$qtLinhas = $this->objConn->delete();
		// -----------
		
		if (! $this->objConn->getError()) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet('ent', $usuario);
			$this->objStatus->addRet('msg', 'Usuário deletado com sucesso!');
			return $this->objStatus;
		}
		else {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Erro ao deletar Usuario.<br />Msg: ' . $this->objConn->getErros());
			return $this->objStatus;
		}
	
	}

	/**
	 *
	 * @param Usuario $usuario        	
	 * @return ObjStatus
	 */
	private function validaDeleta(Usuario $usuario){

		if (! is_numeric($usuario->getId())) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'ID inválida!');
			return $this->objStatus;
		}
		
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		return $this->objStatus;
	
	}

	/**
	 * gera o salt para ajudar na seguranca da senha
	 *
	 * @param number $lenght        	
	 * @return string
	 */
	private function geraRandomSalt($lenght = 8){

		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`~!@#$%^&*()-=_+';
		$l = strlen($chars) - 1;
		$str = '';
		for($i = 0; $i < $lenght; ++ $i) {
			$str .= $chars [rand(0, $l)];
		}
		return $str;
	
	}

	/**
	 * gera o password criptografado
	 *
	 * @param string $password        	
	 * @param string $salt        	
	 * @param string $hash_method        	
	 * @return string
	 */
	private function geraHashedPass($password, $salt, $hash_method = 'sha1'){

		if (function_exists('hash') && in_array($hash_method, hash_algos())) {
			return strtoupper(hash($hash_method, $salt . $password));
		}
		return strtoupper(sha1($salt . $password));
	
	}
	
	/**
	 * faz a validacao do email e senha digitados na tela de register
	 * retorna array
	 * 
	 * @param string $email
	 * @param string $senha
	 */
	public function validaEmailSenhaRegister($email, $senha){
		$validEmail = $this->objValidation->validateEmail($email); // t ou f
		$str_email = (!$validEmail) ? "Informe um email v&aacute;lido": "";
		
		$str_senha = "";
		$objStatus = $this->validaSenha($senha);
		if($objStatus->isErro()){
			$str_senha = $objStatus->getRetByKey("msg");
		}
		
		$arr_return = array(
			"ret_email" => $str_email,
			"ret_senha" => $str_senha,
		);
		
		return $arr_return;
		
	}

	/**
	 * Faz a validação da senha digitada
	 *
	 * A senha precisa ter no mínimo 8 caracteres, não ter caracteres consecutivos,
	 * e conter letras e números.
	 *
	 * @return ObjStatusServico
	 */
	private function validaSenha($senhaDigitada){

		$tamanho = strlen($senhaDigitada);
		if ($tamanho < UsuarioService::SENHA_TAMANHO_MIN) {
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Senha precisa ter 8 caracteres no m&iacute;nimo!');
			return $this->objStatus;
		}
		else {
			$tem_espacos = (preg_match('/\s/', $senhaDigitada));
			$letras_e_numeros = preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $senhaDigitada);
			
			if ($tem_espacos || ! $letras_e_numeros) {
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
				$this->objStatus->addRet('msg', 'A senha precisa ter letras e n&uacute;meros!');
				return $this->objStatus;
			}
			else {
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
				return $this->objStatus;
			}
		}
	
	}
	
	/**
	 * 
	 * @param string $email
	 * @return boolean|string
	 */
	private function geraCodConfirmacao($email){
		$sql = 'SELECT usu_id
  				FROM tb_usuario
				WHERE usu_email = ?';
		$this->objConn->setSQL($sql);
		$this->objConn->addParameter($email);
		$rs = $this->objConn->selectRow();
		
		if (! count($rs) > 0) {
			return false;
		}
		else{
			$codConfirmacao = mt_rand(100000,999999) . "W" . $rs["usu_id"] . "W" . substr(md5(microtime()),rand(0,26),5);
			return $codConfirmacao;
		}
	}
	
	/**
	 * 
	 * @param string $codConfirm
	 * @return boolean
	 */
	private function codConfirmacaoToUsuId($codConfirm){
		$arrAux = explode("W", $codConfirm);
		return ( isset($arrAux[1]) ) ? $arrAux[1]: false;
	}
	
	/**
	 * 
	 * @param string $email
	 * @return boolean
	 */
	public function enviaEmailRegister($email){
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Mailer.class.php';
		$Mailer = new Mailer();
		
		$Mailer->addToMail($email);
		$Mailer->setSubject("Confirmação de Cadastro - Mega Finanças");
		
		$codConfirmacao = $this->geraCodConfirmacao($email);
		if($codConfirmacao === false){
			return false;
		}
		else{
			$url_confirm = "http://app.megafinancas.com.br/home/confirm/$codConfirmacao";
			$link_confirm = "<a href='$url_confirm'>$url_confirm</a>";
			$body = "<p>Olá. Para acessar e começar a usar o Mega Finanças, antes precisamos confirmar seu cadastro.</p>
					 <p>Para confirmar o cadastro, basta clicar no link abaixo:</p>
					 <p align='center'>$link_confirm</p>
					 <p align='center'><small>se o link não funcionar, copie e cole ele no navegador</small></p>
					 <p>Esperamos você no Mega Finanças!</p>
					 <p>Qualquer dúvida, entre em contato pelo email: contato@megafinancas.com.br</p>";
			$Mailer->setBody($body);
			
			$send = $Mailer->send();
			return $send;
		}
	}
	
	/**
	 * 
	 * @param string $codConfirm
	 * @return ObjStatus
	 */
	public function confirmaCadastro($codConfirm){
		$id = $this->codConfirmacaoToUsuId($codConfirm);
		
		if($id === false){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'C&oacute;digo de confirma&ccedil;&atilde;o inv&aacute;lido.');
			return $this->objStatus;
		}
		else{
			$Usuario = new Usuario();
			$objRet = $this->buscaPorId($id);
			
			if( $objRet->isErro() ){
				return $objRet;
			}
			else{
				$Usuario = $objRet->getRetByKey("ent");
				
				if($Usuario->getCadLiberado() == "t"){
					$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
					$this->objStatus->addRet('msg', 'Cadastro j&aacute; confirmado! <a class="link-blue" href="http://app.megafinancas.com.br/">Clique aqui</a> para voltar pra p&aacute;gina de login.');
					return $this->objStatus;
				}
				
				$Usuario->setCadLiberado(TRUE);
				$objRet = $this->edita($Usuario);
				
				if( $objRet->isErro() ){
					return $objRet;
				}
				else{
					$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
					$this->objStatus->addRet('msg', 'Cadastro confirmado com sucesso! <a class="link-blue" href="http://app.megafinancas.com.br/">Clique aqui</a> para acessar a p&aacute;gina de login e fazer seu primeiro acesso.');
					return $this->objStatus;
				}
			}
		}
	}
	
	/**
	 * 
	 * @param string $email
	 * @param string $senha
	 * @return ObjStatus|ObjStatusServico
	 */
	public function validaLogin($email, $senha){
		if(! $this->objValidation->validateEmail($email)){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet('msg', 'Email inv&aacute;lido.');
			return $this->objStatus;
		}
		else{
			$objStatus = new ObjStatus();
			$objStatus = $this->validaSenha($senha);
			
			if($objStatus->isErro()){
				return $objStatus;
			}
			else{				
				$sql = 'SELECT usu_id
		  				FROM tb_usuario
						WHERE usu_email = ?';
				$this->objConn->setSQL($sql);
				$this->objConn->addParameter($email);
				$rs = $this->objConn->selectRow();
				
				if (! count($rs) > 0) {
					$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
					$this->objStatus->addRet('msg', 'Email n&atilde;o cadastrado.');
					return $this->objStatus;
				}
				else{
					$id = $rs["usu_id"];
					$Usuario = new Usuario();
					$objStatus = $this->buscaPorId($id);
					
					if($objStatus->isErro()){
						$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
						$this->objStatus->addRet('msg', 'Usu&aacute;rio n&atilde;o cadastrado.');
						return $this->objStatus;
					}
					else{
						$Usuario = $objStatus->getRetByKey("ent");
						$senhaHashed = $this->geraHashedPass($senha, $Usuario->getSalt());
						
						if($senhaHashed != $Usuario->getSenha()){
							$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
							$this->objStatus->addRet('msg', 'Senha inv&aacute;lida.');
							return $this->objStatus;
						}
						else{
							if($Usuario->getCadLiberado() == "f"){
								$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
								$this->objStatus->addRet('msg', 'Cadastro n&atilde;o confirmado.');
								$this->objStatus->addRet('acao', 'reenviar_confirmacao');
								return $this->objStatus;
							}
							else{
								$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
								$this->objStatus->addRet('Usuario', $Usuario);
								return $this->objStatus;
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * 
	 * @param Usuario $Usuario
	 * @return ObjStatus
	 */
	public function loginTwitter(Usuario $Usuario){
		// tenta cadastrar o user usando as info do twitter
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->insere($Usuario);
		
		if($objResp->isOk()){
			$Usuario = $objResp->getRetByKey("ent");
		}
		else{
			$objResp = $UsuarioServ->buscaPorEmail($Usuario->getEmail());
				
			if($objResp->isErro()){
				$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
				$this->objStatus->addRet('msg', 'N&atilde;o foi poss&iacute;vel carregar as informa&ccedil;&otilde;es do Twitter para fazer o login.');
				return $this->objStatus;
			}
			else{
				$Usuario = $objResp->getRetByKey("ent");
			}
		}
		// =================================================
		
		// se chegou ate aqui, td ok
		$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
		$this->objStatus->addRet('Usuario', $Usuario);
		return $this->objStatus;
	}
	
	/**
	 * 
	 * @return string
	 */
	private function randomPassword() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 10; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); //turn the array into a string
	}
	
	/**
	 * 
	 * @param string $email
	 * @return ObjStatus
	 */
	public function forgetPassword($email){
		$eh_email_valido = $this->objValidation->validateEmail($email);
		
		if(!$eh_email_valido){
			$this->objStatus->
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet("msg", "Email inv&aacute;lido.");
			return $this->objStatus;
		}
		
		$Usuario = new Usuario();
		$objResp = $this->buscaPorEmail($email);
		
		if($objResp->isErro()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet("msg", "Usu&aacute;rio n&atilde;o encontrado.");
			return $this->objStatus;
		}
		else{
			$Usuario = $objResp->getRetByKey("ent");
		}
		
		$randomPass = $this->randomPassword();
		$saltedRandomPass = $this->geraHashedPass($randomPass, $Usuario->getSalt());
		$Usuario->setSenha($saltedRandomPass);
		$objResp = $this->edita($Usuario);
		
		if($objResp->isErro()){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet("msg", "Erro ao processar recupera&ccedil;&atilde;o de senha.");
			return $this->objStatus;
		}
		else{
			require_once $_SERVER ['BIRDS_HOME'] . 'classes-general/Mailer.class.php';
			$Mailer = new Mailer();
			
			$Mailer->addToMail($email);
			$Mailer->setSubject("Recuperação de Senha - Mega Finanças");
			
			$body = "<p>Olá. Recebemos sua solicitação de recuperação de senha e já concluímos o processo.</p>
					<p>Sua senha foi temporáriamente substituida pela senha abaixo.</p>
					<p align='center'>Nova senha temporária: <strong>$randomPass</strong></p>
					<p>Utilize essa senha temporária para acessar o <a href='http://app.megafinancas.com.br/'>Mega Finanças</a> e, após o login, fique a vontade para alterar a senha temporária.</p>
					<p>Qualquer dúvida, entre em contato pelo email: contato@megafinancas.com.br</p>";
			$Mailer->setBody($body);
					
			$send = $Mailer->send();
			
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
			$this->objStatus->addRet("send", $send);
			return $this->objStatus;
		}
	}

	/**
	 * 
	 * @param Usuario $Usuario
	 * @param string $senha
	 * @param string $senha_rep
	 * @return ObjStatus|ObjStatusServico
	 */
	public function alteraSenha(Usuario $Usuario, $senha, $senha_rep){
		if($senha != $senha_rep){
			$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
			$this->objStatus->addRet("msg", "Senha repetida n&atilde;o confere.");
			return $this->objStatus;
		}
		else{
			$objResp = $this->validaSenha($senha);
			if($objResp->isErro()){
				return $objResp;
			}
			else{
				$hashedPass = $this->geraHashedPass($senha, $Usuario->getSalt());
				$Usuario->setSenha($hashedPass);
				$objResp = $this->edita($Usuario);
				
				if($objResp->isErro()){
					$this->objStatus->setStatus(ObjStatus::COD_STATUS_ERRO);
					$this->objStatus->addRet("msg", "Erro ao confirma nova senha.");
					return $this->objStatus;
				}
				else{
					$this->objStatus->setStatus(ObjStatus::COD_STATUS_OK);
					$this->objStatus->addRet("msg", "Nova senha confirmada com sucesso!");
					return $this->objStatus;
				}
			}
		}
	}
}
?>