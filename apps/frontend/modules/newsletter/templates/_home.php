<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('newsletter/create') ?>" method="post" >
	<?php echo $form->renderHiddenFields(false) ?>
	<?php echo $form['email']->render(array('placeholder' => 'Evadez-vous : _Votre email')) ?>
	<input id="bouton-bottom" type="submit" value=" " />
</form>