<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

  <head>
    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width" />

    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>

    <?php include_stylesheets() ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    <meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/pinned-favicon.png">
		<meta name="application-name" content="Citation ou Proverbe">
		<link rel="author" href="/humans.txt" />
    <link rel="alternate" type="application/rss+xml" title="flux de citations" href="<?php echo url_for('@feed') ?>" />
    <link rel="alternate" type="application/rss+xml" title="flux d'œuvres" href="<?php echo url_for('@feed-sources') ?>" />
    <?php if (has_slot('header')): ?>
      <?php include_slot('header') ?>
    <?php endif; ?>


  </head>
  <body>
  	<!--[if lte IE 10]>
      <link rel="stylesheet" href="/css/styles-ie.css" />
      <div class="alert-ie">
      	<p><strong>Attention ! </strong> Votre navigateur présente de sérieuses lacunes en terme de sécurité et de performances, dues à son obsolescence.<br>En conséquence, ce site sera consultable mais de manière moins optimale qu'avec un navigateur récent (<a href="http://www.browserforthebetter.com/download.html" >Internet Explorer</a>, <a href="http://www.mozilla-europe.org/fr/firefox/" >Firefox</a>, <a href="http://www.google.com/chrome?hl=fr" >Chrome</a>, <a href="http://www.apple.com/fr/safari/download/" >Safari</a>,...)</p>
      </div>
  	<![endif]-->

	  <?php include_partial('global/header')?>

  	<div class="page grid-container">


			<?php if ($sf_user->hasFlash('confirmation')): ?>
				<div class="grid-80 prefix-10">
			    <div id="confirmation">
			        <?php echo $sf_user->getFlash('confirmation') ?>
			    </div>
			  </div>
			  <div class="clear"></div>
			<?php endif ?>

	    <?php echo $sf_content ?>

    </div>

	  <?php include_partial('global/footer')?>

    <?php include_partial('global/piwik')?>
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <?php include_javascripts() ?>
  </body>
</html>
