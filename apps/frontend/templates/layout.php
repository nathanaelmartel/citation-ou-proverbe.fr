<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

  <head>
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width" />
    
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    
    <?php use_helper('swCombine'); sw_include_stylesheets() ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/pinned-favicon.png">
		<meta name="application-name" content="Citation ou Proverbe">
    <link rel="alternate" type="application/rss+xml" title="flux" href="<?php echo url_for('@feed') ?>" />
    <?php if (has_slot('header')): ?>
      <?php include_slot('header') ?>
    <?php endif; ?>
    

  </head>
  <body>
  	<div class="page container_16">
	   	<?php include_partial('global/header')?>
	    <?php echo $sf_content ?>
	    <?php include_partial('global/footer')?>
    </div>
    <?php include_partial('global/piwik')?>
    <?php sw_include_javascripts() ?>
  </body>
</html>