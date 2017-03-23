<?php 
// VARIAVEIS DO MAINTEMPLATE
// MT_Page_Title, MT_H3_Title, MT_Breadcrumb
require_once $_SERVER['BIRDS_HOME'] . 'classes-general/ServerInfo.class.php';
$objServerInfo = new ServerInfo();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <link rel="shortcut icon" href="#" type="image/png">

  <title>Birds - Acesso Negado</title>

  <link href="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/css/style.css" rel="stylesheet">
  <link href="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/css/style-responsive.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
  <script src="js/respond.min.js"></script>
  <![endif]-->
</head>

<body class="error-page">

<section>
    <div class="container ">

        <section class="error-wrapper text-center">
            <h1><img alt="" src="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/images/404-error.png"></h1>
            <h2>p&aacute;gina n&atilde;o encontrada</h2>
            <h3>N&atilde;o conseguimos encontrar a p&aacute;gina requisitada</h3>
            <a class="back-btn" href="/birds"> Voltar ao in&iacute;cio</a>
        </section>

    </div>
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/js/jquery-1.10.2.min.js"></script>
<script src="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/js/jquery-migrate-1.2.1.min.js"></script>
<script src="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/js/bootstrap.min.js"></script>
<script src="<?php echo $objServerInfo->getHomeUrl("/birds/"); ?>html/js/modernizr.min.js"></script>


<!--common scripts for all pages-->
<!--<script src="js/scripts.js"></script>-->

</body>
</html>
