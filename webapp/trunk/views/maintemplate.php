<?php
$pageTitle = $viewModel->get('MT_Page_Title');
$displayName = $viewModel->get('displayName');
?>

<!DOCTYPE html>
<html class="no-js">
  <head>
    <meta charset=utf-8>
    <title><?php echo $pageTitle; ?></title>
    <meta name=viewport content="width=device-width">
    
    <link rel="shortcut icon" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/icons/favicon.ico" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/climacons-font.249593b4.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/rickshaw.min.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/app.min.4582c0b0.css" />
    <link rel=stylesheet href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/fonts/font-awesome-4.3.0/css/font-awesome.min.css" />
    <link href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/css/sweetalert.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/remodal-1.0/remodal.css" />
	<link rel="stylesheet" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/remodal-1.0/remodal-default-theme.css" />
	<link rel="stylesheet" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/bootstrap-color-picker/css/bootstrap-colorpicker.min.css" />
	<link rel="stylesheet" href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/bootstrap-date-picker/css/bootstrap-datepicker.min.css" />
    <link href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/master.css" rel="stylesheet" />
    
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.migrate.js"></script>
  </head>
  <body>
    <!-- quick-launch-panel -->
    <div class="quick-launch-panel">
      <div class="container">
        <div class="quick-launcher-inner">
          <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);" class=close data-toggle=quick-launch-panel>×</a>
          <div class="css-table-xs">
            <div class="col">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-calendar.html">
              	<i class="icon-marquee"></i> <span>Calendar</span>
              </a>
            </div>
            <div class="col">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-gallery.html">
              	<i class="icon-drop"></i> <span>Gallery</span>
              </a>
            </div>
            <div class="col">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-messages.html">
              	<i class="icon-mail"></i> <span>Messages</span>
              </a>
            </div>
            <div class="col">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-social.html">
              	<i class="icon-speech-bubble"></i> <span>Social</span>
              </a>
            </div>
            <div class="col">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-flot.html">
              	<i class="icon-pie-graph"></i> <span>Analytics</span>
              </a>
            </div>
            <div class="col">
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="icon-esc"></i> <span>Documentation</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ================== -->
    
    <div class="app layout-fixed-header">
      <div class="sidebar-panel offscreen-left">
        <div class="brand">
          <div class="brand-logo">
          	<?php
          	// html/images/logo.c5cdf202.png
          	?>
            <a href="http://app.megafinancas.com.br/home/index">
            	<img src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/logo-index.png" height="26" alt="" />
            </a>
          </div>
          <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);" class="toggle-sidebar hidden-xs hamburger-icon v3" data-toggle="layout-small-menu">
          	<span></span>
          	<span></span>
          	<span></span>
          	<span></span>
          </a>
        </div>
        
        <nav role="navigation">
        	<ul class="nav">
        		<li class="menu-accordion">
	            	<a href="javascript:void(0);">
	            		<i class="fa fa-university"></i> <span>Contas</span>
	              	</a>
	              	
	              	<ul class="sub-menu">
	              		<li>
	              			<a href="javascript:void(0);" class="mvc-ajax" data-controller="Conta" data-action="index" data-target=".main-content">
	              				Minhas Contas
	              			</a>
	              		</li>
	              		<li>
	              			<a href="javascript:void(0);" class="mvc-ajax" data-controller="Movimentacao" data-action="indexTransferencias" data-target=".main-content">
	              				Transfer&ecirc;ncias
	              			</a>
	              		</li>
	              	</ul>
	            </li>
	            <li>
	            	<a href="javascript:void(0);" class="mvc-ajax" data-controller="MovimentacaoCat" data-action="index" data-target=".main-content">
	            		<i class="fa fa-tags"></i> <span>Categorias</span>
	              	</a>
	            </li>
	            <li>
	            	<a href="javascript:void(0);" class="mvc-ajax" data-controller="Movimentacao" data-action="indexReceitas" data-target=".main-content">
	            		<i class="fa fa-money"></i> <span>Receitas</span>
	              	</a>
	            </li>
	            <li>
	            	<a href="javascript:void(0);" class="mvc-ajax" data-controller="Movimentacao" data-action="indexDespesas" data-target=".main-content">
	            		<i class="fa fa-shopping-cart"></i> <span>Despesas</span>
	              	</a>
	            </li>
	            
	            <li class="menu-accordion">
	            	<a href="javascript:void(0);">
	            		<i class="fa fa-credit-card"></i> <span>Cart&otilde;es</span>
	              	</a>
	              	
	              	<ul class="sub-menu">
	              		<li>
	              			<a href="javascript:void(0);" class="mvc-ajax" data-controller="CartaoCredito" data-action="index" data-target=".main-content">
	              				Meus Cart&otilde;es
	              			</a>
	              		</li>
	              		<li>
	              			<a href="javascript:void(0);" class="mvc-ajax" data-controller="CartaoCredito" data-action="indexLancamentos" data-target=".main-content">
	              				Lan&ccedil;amentos
	              			</a>
	              		</li>
	              	</ul>
	            </li>
        	</ul>
        </nav>
        
        <?php
        /*
        <nav role="navigation">
          <ul class="nav">
            <li>
            	<a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/index.html">
            		<i class="fa fa-flask"></i> <span>Dashboard</span>
              	</a>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-toggle-on"></i> <span>UI Elements</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-buttons.html">
                  <span>Buttons</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-general.html">
                  <span>General</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-tabs-accordion.html">
                  <span>Tabs &amp; Accordions</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-progressbars.html">
                  <span>Progress Bars</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-pagination.html">
                  <span>Pagination</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-sliders.html">
                  <span>Sliders</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-portlets.html">
                  <span>Portlets</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-notifications.html">
                  <span>Notifications</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-alert.html">
                  <span>Alerts</span>
                  </a>
                </li>
                <li>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <i class=toggle-accordion></i> <span>Icons</span>
                  </a>
                  <ul class="sub-menu">
                    <li><a
                      href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-fontawesome.html">
                      <span>Fontawesome</span>
                      </a>
                    </li>
                    <li><a
                      href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-feather.html">
                      <span>Feather</span>
                      </a>
                    </li>
                    <li><a
                      href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/ui-climacon.html">
                      <span>Climacon</span>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-tint"></i> <span>Forms</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-basic.html">
                  <span>Basic Forms</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-advanced.html">
                  <span>Advanced Components</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-wizard.html">
                  <span>Wizard</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-editors.html">
                  <span>Editors</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-validation.html">
                  <span>Validation</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/form-masks.html">
                  <span>Input Masks</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-tag"></i> <span>Tables</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/table-basic.html">
                  <span>Basic Tables</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/table-responsive.html">
                  <span>Responsive Table</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/table-datatable.html">
                  <span>Data Tables</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/table-editable.html">
                  <span>Editable Table</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-pie-chart"></i> <span>Charts</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-flot.html">
                  <span>Flot Charts</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-easypie.html">
                  <span>EasyPie</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-chartjs.html">
                  <span>ChartJs</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-rickshaw.html">
                  <span>Rickshaw</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-nvd3.html">
                  <span>Nvd3</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/charts-c3.html">
                  <span>C3js</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-map-marker"></i> <span>Maps</span> <span
                class="label label-success pull-right">2</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/map-google.html">
                  <span>Google Maps</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/map-vector.html">
                  <span>Vector Maps</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-send"></i> <span>Apps</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-calendar.html">
                  <span>Calendar</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-gallery.html">
                  <span>Gallery</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-messages.html">
                  <span>Messages</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/app-social.html">
                  <span>Social</span>
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-trophy"></i> <span>Extras</span> <span
                class="label label-danger pull-right">new</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-invoice.html">
                  <span>Invoice</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-timeline.html">
                  <span>Timeline</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-sortable.html">
                  <span>Sortable Lists</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-nestable.html">
                  <span>Nestable Lists</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-search.html">
                  <span>Search</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-signin.html">
                  <span>Signin</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-signup.html">
                  <span>Signup</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-forgot.html">
                  <span>Forgot Password</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-lockscreen.html">
                  <span>Lockscreen</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-404.html">
                  <span>404 Page</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-500.html">
                  <span>500 Page</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/extras-blank.html">
                  <span>Starter Page</span>
                  </a>
                </li>
              </ul>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/widgets.html">
              <i class="fa fa-toggle-on"></i> <span>Widgets</span>
              </a>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-level-down"></i> <span>Menu Levels</span>
              </a>
              <ul class="sub-menu">
                <li>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <i class=toggle-accordion></i> <span>Level 1.1</span>
                  </a>
                  <ul class="sub-menu">
                    <li>
                      <a
                        href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                      <i class=toggle-accordion></i> <span>Level 2.1</span>
                      </a>
                      <ul class="sub-menu">
                        <li><a
                          href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                          <span>Level 3.1</span>
                          </a>
                        </li>
                        <li><a
                          href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                          <span>Level 3.2</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li><a
                      href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                      <span>Level 2.2</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span>Level 1.2</span>
                  </a>
                </li>
              </ul>
            </li>
            <li data-ng-class="{open: $state.includes('app.extras')}">
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:;">
              <i class="fa fa-envelope"></i> <span>Transational Emails</span>
              </a>
              <ul class="sub-menu">
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/action.html"
                  target="_blank"> <span>Action</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/alert.html"
                  target="_blank"> <span>Alert</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/billing.html"
                  target="_blank"> <span>Billing</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/progress.html"
                  target="_blank"> <span>Progress</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/survey.html"
                  target="_blank"> <span>Survey</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/email/welcome.html"
                  target="_blank"> <span>Welcome</span>
                  </a>
                </li>
              </ul>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://customizer.nyasha.me/#urban"
              target="_blank"> <i class="fa fa-sliders"></i> <span>Customizer</span>
              <span class="label label-danger pull-right">hot</span>
              </a>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/docs.html"> <i
              class="fa fa-folder"></i> <span>Documentation</span>
              </a>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/http://urban.nyasha.me/angular">
              <i class="fa fa-circle"></i> <span>Angular Version</span>
              </a>
            </li>
          </ul>
        </nav>
        */
        ?>
        
      </div>
      <div class="main-panel">
        <header class="header navbar">
          <div class="brand visible-xs">
            <div class=toggle-offscreen>
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#" class="hamburger-icon visible-xs" data-toggle="offscreen" data-move="ltr">
              	<span></span>
              	<span></span>
              	<span></span>
              </a>
            </div>
            <div class="brand-logo">
              <a href="http://app.megafinancas.com.br/home/index">
              	<img src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/logo-index-dark.png" height="28" alt="" />
              </a>
            </div>
            <?php
            /*
            <div class="toggle-chat">
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);" class="hamburger-icon v2 visible-xs" data-toggle="layout-chat-open">
              	<span></span>
              	<span></span>
              	<span></span>
              </a>
            </div>
            */
            ?>
          </div>
          <ul class="nav navbar-nav hidden-xs">
            <?php
            /*
            <li>
              <p class="navbar-text">Dashboard</p>
            </li>
            */
            ?>
          </ul>
          <ul class="nav navbar-nav navbar-right hidden-xs">
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);"
              data-toggle="quick-launch-panel"> <i class="fa fa-circle-thin"></i>
              </a>
            </li>
            <li>
              <a
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);"
                data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <div class="status bg-danger border-danger animated bounce"></div>
              </a>
              <ul class="dropdown-menu notifications">
                <li class="notifications-header">
                  <p class="text-muted small">You have 3 new messages</p>
                </li>
                <li>
                  <ul class="notifications-list">
                    <li>
                      <a
                        href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                        <span class="pull-left mt2 mr15"> <img
                          src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/avatar.21d1cc35.jpg"
                          class="avatar avatar-xs img-circle" alt="">
                        </span>
                        <div class="overflow-hidden">
                          <span>Sean launched a new application</span> <span
                            class="time">2 seconds ago</span>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a
                        href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                        <div class="pull-left mt2 mr15">
                          <div class="circle-icon bg-danger">
                            <i class="fa fa-chain-broken"></i>
                          </div>
                        </div>
                        <div class="overflow-hidden">
                          <span>Removed chrome from app list</span> <span class="time">4
                          Hours ago</span>
                        </div>
                      </a>
                    </li>
                    <li>
                      <a
                        href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                        <span class="pull-left mt2 mr15"> <img
                          src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/face3.0306ffff.jpg"
                          class="avatar avatar-xs img-circle" alt="" />
                        </span>
                        <div class="overflow-hidden">
                          <span class="text-muted">Jack Hunt has registered</span> <span
                            class="time">9 hours ago</span>
                        </div>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="notifications-footer"><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">See
                  all messages</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);" data-toggle="dropdown">
              	<?php
                // html/images/avatar.21d1cc35.jpg
                ?>
              	<img src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/avatar-128.jpg" class="header-avatar img-circle ml10" alt="user" title="user" />
              	<span class="pull-left"><?php echo $displayName; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li>
                	<a class="mvc-ajax" data-controller="Usuario" data-action="configs" data-target=".main-content" href="javascript:void(0);">Configura&ccedil;&otilde;es</a>
                </li>
                <li>
                	<a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>home/logout">Sair</a>
                </li>
                
                <?php
                /*
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">Settings</a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">Upgrade</a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="label bg-danger pull-right">34</span> <span>Notifications</span>
                  </a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:;">Help</a>
                </li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/signin.html">Logout</a>
                </li>
                */
                ?>
              </ul>
            </li>
            <?php
            /*
            <li>
            	<a href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:;" class="hamburger-icon v2" data-toggle="layout-chat-open">
            		<span></span>
              		<span></span>
              		<span></span>
              	</a>
            </li>
            */
            ?>
          </ul>
        </header>
        <div class="main-content">
          <?php require($this->viewFile); ?>
        </div>
      </div>
      <footer class="content-footer">
        <nav class="footer-right">
        </nav>
        <nav class="footer-left">
          <ul class="nav">
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              Copyright <i class="fa fa-copyright"></i> <span>Urban</span>
              2015. All rights reserved
              </a>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">Careers</a>
            </li>
            <li><a
              href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">Privacy
              Policy</a>
            </li>
          </ul>
        </nav>
      </footer>
      <?php
      /* 
      <div class="chat-panel">
        <div class="chat-inner">
          <div class="chat-users">
            <div class="nav-justified-xs">
              <ul class="nav nav-tabs nav-justified">
                <li class="active"><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#chat-list"
                  data-toggle="tab">Chat</a></li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#market"
                  data-toggle="tab">Favourites</a></li>
                <li><a
                  href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#activity"
                  data-toggle="tab">Activity</a></li>
              </ul>
            </div>
            <div class="tab-content">
              <div class="tab-pane active" id="chat-list">
                <div class="chat-group">
                  <div class="chat-group-header">Favourites</div>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-online"></span> <span>Catherine Moreno</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-online"></span> <span>Denise Peterson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-away"></span> <span>Charles Wilson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-away"></span> <span>Melissa Welch</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-no-disturb"></span> <span>Vincent Peterson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Pamela Wood</span>
                  </a>
                </div>
                <div class="chat-group">
                  <div class="chat-group-header">Online Friends</div>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-online"></span> <span>Tammy Carpenter</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-away"></span> <span>Emma Sullivan</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-no-disturb"></span> <span>Andrea Brewer</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-online"></span> <span>Sean Carpenter</span>
                  </a>
                </div>
                <div class="chat-group">
                  <div class="chat-group-header">Offline</div>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Denise Peterson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Jose Rivera</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Diana Robertson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>William Fields</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Emily Stanley</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Jack Hunt</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Sharon Rice</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Mary Holland</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Diane Hughes</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Steven Smith</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Emily Henderson</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Wayne Kelly</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Jane Garcia</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Jose Jimenez</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Rachel Burton</span>
                  </a> <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
                  <span class="status-offline"></span> <span>Samantha Ruiz</span>
                  </a>
                </div>
              </div>
              <div class="tab-pane" id="market">
                <div class="favourite-list">
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/avatar.21d1cc35.jpg"
                        alt="avatar" />
                      <div class="status bg-success border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Catherine Moreno</span> <span
                        class="text-muted">Online</span>
                    </div>
                  </a>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/face1.75317f48.jpg"
                        alt=avatar />
                      <div class="status bg-success border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Denise Peterson</span> <span
                        class="text-muted">Online</span>
                    </div>
                  </a>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/face3.0306ffff.jpg"
                        alt="avatar" />
                      <div class="status bg-default border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Charles Wilson</span> <span class="text-muted">Busy</span>
                    </div>
                  </a>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/face4.cea90747.jpg"
                        alt="avatar" />
                      <div class="status bg-danger border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Melissa Welch</span> <span class="text-muted">Offline</span>
                    </div>
                  </a>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/face5.535c103a.jpg"
                        alt="avatar" />
                      <div class="status bg-danger border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Vincent Peterson</span> <span class="text-muted">Offline</span>
                    </div>
                  </a>
                  <a
                    href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/#">
                    <div 
                      class="media-left relative">
                      <img
                        class="img-circle avatar avatar-xs"
                        src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/images/avatar.21d1cc35.jpg"
                        alt="avatar" />
                      <div class="status bg-danger border-white mr10"></div>
                    </div>
                    <div class="media-body">
                      <span class="block">Pamela Wood</span> <span class="text-muted">Offline</span>
                    </div>
                  </a>
                </div>
              </div>
              <div class="tab-pane" id="activity">
                <ol class="activity-feed">
                  <li class="feed-item"><span>Launched a new application</span> <time
                    datetime="2015-01-30 00:00">2 seconds ago</time></li>
                  <li class="feed-item inactive"><span>Removed chrome from app list</span>
                    <time datetime="2015-01-20 00:00">Jan 20</time>
                  </li>
                  <li class="feed-item"><span>Approved new user "Jack hunt"</span> <time
                    datetime="2015-01-02 00:00">Jan 02</time></li>
                  <li class="feed-item active"><span>Executed new cron jobs on server
                    with id 67gyu789</span> <time datetime="2014-12-12 00:00">Dec 12</time>
                  </li>
                  <li class="feed-item"><span class="text">Added paypal to list payment
                    options</span> <time datetime="2014-12-01 00:00">Dec 01</time>
                  </li>
                  <li class="feed-item"><span>Added 6 new calendar events</span> <time
                    datetime="2014-08-30 00:00">Aug 30</time></li>
                </ol>
              </div>
            </div>
          </div>
          <div class="chat-conversation">
            <div class="chat-header">
              <a class="chat-back"
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-angle-left"></i>
              </a>
              <div class="chat-header-title">Charles Wilson</div>
              <a class="chat-right"
                href="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/javascript:void(0);">
              <i class="fa fa-circle-thin"></i>
              </a>
            </div>
            <div class="chat-conversation-content">
              <p class="text-center text-muted small text-uppercase bold pb15">Yesterday</p>
              <div class="chat-conversation-user them">
                <div class="chat-conversation-message">
                  <p>Hey.</p>
                </div>
              </div>
              <div class="chat-conversation-user them">
                <div class="chat-conversation-message">
                  <p>How are the wife and kids, Taylor?</p>
                </div>
              </div>
              <div class="chat-conversation-user me">
                <div class="chat-conversation-message">
                  <p>Pretty good, Samuel.</p>
                </div>
              </div>
              <p class="text-center text-muted small text-uppercase bold pb15">Today</p>
              <div class="chat-conversation-user them">
                <div class="chat-conversation-message">
                  <p>Curabitur blandit tempus porttitor.</p>
                </div>
              </div>
              <div class="chat-conversation-user me">
                <div class="chat-conversation-message">
                  <p>Goodnight!</p>
                </div>
              </div>
              <div class="chat-conversation-user them">
                <div class="chat-conversation-message">
                  <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula,
                    eget lacinia odio sem nec elit.
                  </p>
                </div>
              </div>
            </div>
            <div class="chat-conversation-footer">
              <button class="chat-input-tool">
              <i class="fa fa-camera"></i>
              </button>
              <div class="chat-input" contenteditable=""></div>
              <button class="chat-send">
              <i class="fa fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      */
      ?>
    </div>
    
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/app.min.7f09e133.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/d3.min.js"></script>
    
    <!--
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/rickshaw.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.resize.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.categories.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.flot.pie.js"></script>
    -->
    
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/dashboard.fe7e077d.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/sweetalert.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/remodal-1.0/remodal.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/autoNumeric-min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/bootstrap-color-picker/js/bootstrap-colorpicker.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.nestable.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/jquery.mouse.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/js/perfect-scrollbar.jquery.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/bootstrap-date-picker/js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/bootstrap-date-picker/locales/bootstrap-datepicker.pt-BR.min.js"></script>
    <script src="<?php echo $_SERVER['BIRDS_HOME_URL']; ?>html/master.js"></script>
    
  </body>
</html>