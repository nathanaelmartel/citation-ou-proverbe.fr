<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <?php include_javascripts() ?>
  </head>
	<body>
	  <?php include_component('sfAdminDash','header'); ?>
	  <?php echo $sf_content ?>
	  <?php include_partial('sfAdminDash/footer'); ?>
	</body>
</html>
