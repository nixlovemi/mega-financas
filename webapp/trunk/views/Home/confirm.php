<?php
$body = $viewModel->get("body");
?>

<html>
	<head>
		<meta charset=utf-8>
		<title><?php echo $viewModel->get('MT_Page_Title'); ?></title>
		<meta name=viewport content="width=device-width">
		
		<link rel="shortcut icon" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/icons/favicon.ico" />
		<link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/climacons-font.249593b4.css" />
		<link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/rickshaw.min.css" />
		<link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/app.min.4582c0b0.css" />
		<link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/fonts/font-awesome-4.3.0/css/font-awesome.min.css" />
		<link href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/master.css" rel="stylesheet" />
		
		<script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.min.js"></script>
		<script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.migrate.js"></script>
	</head>
	<body>
		<?php
		echo $body;
		?>
	</body>
</html>