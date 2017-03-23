<?php
class HomeController extends BaseController{
	// add to the parent constructor
	public function __construct($action, $urlValues){

		parent::__construct($action, $urlValues);
		
		// create the model object
		require ("models/home.php");
		$this->model = new HomeModel();
	
	}
	
	// default method
	protected function index(){

		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
		$objSession = new Session();
		$userId = $objSession->getLoggedUserId();
		
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->buscaPorId($userId);
		
		if($objResp->isErro()){
			session_destroy();
			$_SESSION = array ();
			$objSession->checkSession('home');
		}
		else{
			require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/entities/Usuario.entity.php";
			$Usuario = new Usuario();
			$Usuario = $objResp->getRetByKey("ent");
			
			// logica pro nome do user
			$displayName = "An&ocirc;nimo";
			
			if($Usuario->getNome() != "" && $Usuario->getNome() != "Nome"){
				$displayName = $Usuario->getNome();
				
				if($Usuario->getSobrenome() != "" && $Usuario->getSobrenome() != "Sobrenome"){
					$displayName .= " " . $Usuario->getSobrenome();
				}
			}
			// =======================
			
			$viewModel = new ViewModel();
			$viewModel = $this->model->index();
			$viewModel->set ("displayName", $displayName);
			
			$this->view->output($this->model->index());
		}
	}

	protected function login(){

		$this->view->output($this->model->login(), "");
	
	}
	
	protected function loginTelaRegister(){
		// @todo FAZER UM JEITO PRA APENAS ACESSAR VIA POST
		$this->view->output($this->model->loginTelaRegister(), "");
	}
	
	protected function forgetPassword(){
		$this->view->output($this->model->forgetPassword(), "");
	}
	
	protected function confirm(){
		$viewModel = new ViewModel();
		$viewModel = $this->model->confirm();
		
		$codConfirm = (isset($_REQUEST["id"])) ? trim($_REQUEST["id"]): "";
		
		if($codConfirm == ""){
			$body = "<p>N&atilde;o conseguimos identificar o c&oacute;digo de confirma&ccedil;o. Tente novamente mais tarde ou entre em contato pelo email <strong>contato@megafinancas.com.br</strong>.</p>
					 <p><a class='link-blue' href='http://app.megafinancas.com.br/'>Clique aqui</a> para voltar.</p>";
			$viewModel->set ( "body", $body );
		}
		else{
			require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
			$UsuarioServ = new UsuarioService();
			$objResp = $UsuarioServ->confirmaCadastro($codConfirm);
			
			$body = "<p>".$objResp->getRetByKey("msg")."</p>";
			$viewModel->set ( "body", $body );
		}
		
		$this->view->output($this->model->confirm(), "");
	}
	
	protected function loginFcbk(){
		// recebe as variáveis
		$fcbk_id = base64_decode($_POST["i"]);
		$fcbk_name = base64_decode($_POST["n"]);
		$fcbk_first_name = base64_decode($_POST["f"]);
		$fcbk_last_name = base64_decode($_POST["l"]);
		$fcbk_email = base64_decode($_POST["e"]);
		$fcbk_gender = base64_decode($_POST["g"]);
		$fcbk_picture = base64_decode($_POST["p"]);
		// ===================
		
		// faz o processo de login
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/UsuarioFcbk.entity.php';
		$UsuarioFcbk = new UsuarioFcbk();
		$UsuarioFcbk->setFbUsuId($fcbk_id);
		$UsuarioFcbk->setFbNomecompleto($fcbk_name);
		$UsuarioFcbk->setFbPrimNome($fcbk_first_name);
		$UsuarioFcbk->setFbSobrenome($fcbk_last_name);
		$UsuarioFcbk->setFbEmail($fcbk_email);
		$UsuarioFcbk->setFbSexo($fcbk_gender);
		$UsuarioFcbk->setFbFoto($fcbk_picture);
		
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/UsuarioFcbk.service.php';
		$UsuarioFcbkServ = new UsuarioFcbkService();
		$objResp = $UsuarioFcbkServ->loginFcbk($UsuarioFcbk);
		
		# trata resposta =========
		if($objResp->isErro()){
			echo "ERRO#@#O servidor informou um erro no processo de login: " . $objResp->getRetByKey("msg");
		}
		else{
			$UsuarioFcbk = $objResp->getRetByKey("UsuarioFcbk");
			$idUf = $UsuarioFcbk->getId();
			
			echo "OK#@#$idUf";
		}
		// =======================
		
	}
	
	protected function loginTwttr(){
		include($_SERVER ['BIRDS_HOME'].'classes-general/Twitter-OAuth/EpiCurl.php');
		include($_SERVER ['BIRDS_HOME'].'classes-general/Twitter-OAuth/EpiOAuth.php');
		include($_SERVER ['BIRDS_HOME'].'classes-general/Twitter-OAuth/EpiTwitter.php');
		
		// variaveis da app do twitter
		$consumer_key = "CJZYLYpdwbfcyf6fPKPv0jdiW";
		$consumer_secret = "0IBiludQFy91jfFjWho3ujDWzwSjII6XxlyeHwyAaYAkcueYuU";
		// ===========================
		
		// verifica se teve retorno do twitter
		if( isset($_GET["denied"]) ){
			header("Location: http://app.megafinancas.com.br/?tw=f");
			die();
		}
		else if( isset($_GET['oauth_token']) ){
			try {
				$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
					
				// Use the setToken method to set the "access token" and "access token secret key" by using the "request token".
				// We need these to later access the users information such as user name
				$twitterObj->setToken($_GET['oauth_token']);
				$token = $twitterObj->getAccessToken();
				$twitterObj->setToken($token->oauth_token, $token->oauth_token_secret);
					
				// Get user data from the twitter account
				$userdata = $twitterObj->get_accountVerify_credentials();
				$v_id = $userdata->id;
				$v_name = $userdata->name;
				$v_screen_name = $userdata->screen_name;
					
				// faz o processo de login twitter
				require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/entities/Usuario.entity.php';
				$Usuario = new Usuario();
				$Usuario->setCadLiberado(TRUE);
				$Usuario->setEmail("@$v_screen_name");
				$Usuario->setNome($v_name);
				$Usuario->setSenha(md5(rand()));
				$Usuario->setSobrenome("");
					
				require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
				$UsuarioServ = new UsuarioService();
				$objResp = $UsuarioServ->loginTwitter($Usuario);
				
				if($objResp->isErro()){
					header("Location: http://app.megafinancas.com.br/?tw=f");
					die();
				}
				else{
					require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
					$objSession = new Session();
					
					$objSession->initSession($objResp->getRetByKey("Usuario")->getId());
					$url = "http://app.megafinancas.com.br/home/index";
					header('Location: ' . $url, true, 303);
					die();
				}
			} catch (Exception $e) {
				header("Location: http://app.megafinancas.com.br/?tw=f");
				die();
			}
		}
		// ===================================
			
		$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
		$authenticateUrl = $twitterObj->getAuthenticateUrl();
		
		echo "REDIRECT#@#$authenticateUrl";
	}

	protected function postForgetPassword(){
		$email = (isset($_POST["e"])) ? base64_decode($_POST["e"]): "";
		
		require_once $_SERVER ['BIRDS_HOME'] . 'classes-birds/services/Usuario.service.php';
		$UsuarioServ = new UsuarioService();
		$objResp = $UsuarioServ->forgetPassword($email);
		
		if($objResp->isErro()){
			echo "ERRO#@#Erro no processo. Mensagem: " . $objResp->getRetByKey("msg");
			return;
		}
		else{
			if($objResp->getRetByKey("send") == false){
				echo "ERRO#@#Erro ao enviar email com a senha tempor&aacute;ria. Tente novamente em breve.";
				return;
			}
			else{
				echo "OK#@#Processo de recupera&ccedil;&atilde;o conclu&iacute;do. Voc&ecirc; receber&aacute; um email com a senha tempor&aacute;ria.";
			}
		}
	}

	protected function logout(){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
		$objSession = new Session();
		
		session_destroy();
		$_SESSION = array ();
		$objSession->checkSession('home');
	}
}

?>
