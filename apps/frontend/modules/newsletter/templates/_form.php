<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('newsletter/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>


  <fieldset class="form">
    
    <?php if (!$form->getObject()->isNew()): ?>
		  <input type="hidden" name="sf_method" value="put" />
		<?php endif; ?>
    
    <?php echo $form->renderHiddenFields(false) ?>
    <?php echo $form->renderGlobalErrors() ?>
    
    <?php foreach($form as $name => $field): ?>
        <?php include_partial('newsletter/input', array('field' => $field, 'name' => $name)) ?>
    <?php endforeach; ?>
    <div class="row grid-20 prefix-20" >
      <input type="submit" value="Inscription" class="bouton" />
    </div>
  </fieldset>
</form>
