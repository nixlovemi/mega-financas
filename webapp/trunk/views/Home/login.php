<?php
require_once $_SERVER ['BIRDS_HOME'] . "classes-general/Session.class.php";
$objSession = new Session();

// zera a session sempre que vier pra esse arquivo
session_start();
session_destroy();
$_SESSION = array ();
// -----------------------------------------------

$titulo = "";
$msg = "";
$acao = "";
$email = "";

if( isset($_POST["hddn-login"]) && $_POST["hddn-login"] == "login" ){
	$email = $_POST["loginEmail"];
	$senha = $_POST["loginPassword"];
	$idFcbk = $_POST["hddn-fcbk"];
	$Usuario = null;
	
	if(is_numeric($idFcbk)){
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/UsuarioFcbk.service.php";
		$UsuarioFcbkServ = new UsuarioFcbkService();
		$objResp = $UsuarioFcbkServ->buscaPorId($idFcbk);
		
		if($objResp->isOk()){
			$UsuarioFcbk = $objResp->getRetByKey("ent");
			$Usuario = $UsuarioFcbk->getUsuario();
		}
	}
	else{
		require_once $_SERVER ['BIRDS_HOME'] . "classes-birds/services/Usuario.service.php";
		$UsuarioServ = new UsuarioService();
		$objStatus = $UsuarioServ->validaLogin($email, $senha);
		
		if($objStatus->isErro()){
			$erroMsg = $objStatus->getRetByKey("msg");
			$acao = $objStatus->getRetByKey("acao");
		
			$titulo = "Resposta Login";
			$msg = "Ocorreu um erro ao efetuar o login. Mensagem: $erroMsg";
		}
		else{
			$Usuario = $objStatus->getRetByKey("Usuario");
		}
	}
	
	if(!is_a($Usuario, "Usuario")){
		if($msg == ""){
			$titulo = "Resposta Login";
			$msg = "Ocorreu um erro ao efetuar o login. Mensagem: Usu&aacute;rio n&atilde;o localizado.";
		}
	}
	else{
		$objSession->initSession($Usuario->getId());
		$url = "http://app.megafinancas.com.br/home/index";
		header('Location: ' . $url, true, 303);
		die();
	}
}
else if( isset($_GET["tw"]) && $_GET["tw"] == "f" ){
	$titulo = "Resposta Login Twitter";
	$msg = "Ocorreu um erro ao efetuar o login pelo Twitter =(";
}
?>

<!DOCTYPE html>
<html class=no-js>
  <head>
    <meta charset=utf-8>
    <title><?php echo $viewModel->get('MT_Page_Title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link rel="shortcut icon" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/icons/favicon.ico" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/climacons-font.249593b4.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/rickshaw.min.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/app.min.4582c0b0.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/fonts/font-awesome-4.3.0/css/font-awesome.min.css" />
    <link href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/master.css" rel="stylesheet" />
    <link href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/sweetalert.css" rel="stylesheet" />
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.migrate.js"></script>
  </head>
  <body class="login-bg">
  
  	<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId      : '1656914017927623',
	      xfbml      : true,
	      version    : 'v2.5'
	    });
	  };
	
	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = "//connect.facebook.net/en_US/sdk.js";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>
  
    <div class="login-wrapper">
    	<div class="login-container">
    		<h1 class="login-logo">
    			<img alt="Mega Finan&ccedil;as" src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/login-logo.png" />
    		</h1>

			<form method="post" class="login-form" action="" role="form" id="loginForm" novalidate="novalidate">
			  <div class="login-form-group">
			    <input type="email" class="form-control input-lg" placeholder="Email" name="loginEmail" id="loginEmail" />
			  </div>
			  <div class="login-form-group">
			    <input type="password" class="form-control input-lg" placeholder="Senha" name="loginPassword" id="loginPassword" />
			  </div>
			  <button class="btn btn-dark btn-block btn-login" type="submit">Entrar</button>
			  
			  <input type="hidden" name="hddn-login" value="login" />
			  <input type="hidden" name="hddn-fcbk" id="hddn-fcbk" value="" />
			  
			  <div class="login-social">
			    <div class="l-span-md-12">
			      <div class="or"><span>- OU -</span></div>
			    </div>
			    <div class="l-col-sm-6">
			    	<a class="btn btn-facebook btn-block" id="btn-login-fcbk" href="javascript:void(0);"><i class="fa fa-facebook"></i>&nbsp;Entrar com Facebook</a>
			    </div>
			    <div class="l-col-sm-6">
			    	<a class="btn btn-twitter btn-block" id="btn-login-twttr" href="javascript:void(0);"><i class="fa fa-twitter"></i>&nbsp;Entrar com Twitter</a>
			    </div>
			  </div>
			  <div class="login-options">
		    	<a id="forget-password-button" class="fl" href="javascript:void(0);">Recuperar Senha</a>
		    	<a id="login-register-button" class="fr" href="javascript:void(0);">Cadastrar</a>
		      </div>
			</form>
    	</div>
    </div>
    
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/app.min.7f09e133.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/sweetalert.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/master.js"></script>
    
    <?php
    if( $titulo != "" && $msg != "" ){
    	if($acao == "reenviar_confirmacao"){
    		$msg .= "<br />Foi reenviado um email de confirmação para você."
    		?>
    		
    		<script>
    		enviaEmailRegister('<?php echo $email; ?>');
    		</script>
    		
    		<?php
    	}
    	?>
    	
    	<script>
	    showAlert('<?php echo $titulo; ?>', '<?php echo $msg; ?>');
	    </script>
	    
    	<?php
    }

    /*
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/d3.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/rickshaw.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.resize.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.categories.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.pie.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/dashboard.fe7e077d.js"></script> 
    */
    ?>
    
  </body>
</html>