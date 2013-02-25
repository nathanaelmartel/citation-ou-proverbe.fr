
<?php if ($sf_user->hasFlash('confirmation')): ?>
	<div class="grid_14 prefix_1">
    <div id="confirmation">
        <?php echo$sf_user->getFlash('confirmation') ?>
    </div>
  </div>
    
<?php else: ?>

	<div class="grid_14 prefix_1">
  	<h1>Formulaire de Contact</h1>
  </div>
	<div class="clear"></div>
	
<form action="<?php echo url_for('@contact_validation') ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

  <fieldset class="form" >

    <?php if (!$form->getObject()->isNew()): ?>
      <input type="hidden" name="sf_method" value="put"/>
    <?php endif; ?>

    <?php foreach ($form as $fieldName => $field): ?>
      <?php include_partial('contact/input', array('field' => $field)); ?>
    <?php endforeach; ?>

    <div class="grid_6 prefix_4 row-control">
      <input type="submit" value="Envoyer" class="buton"/>
    </div>

  </fieldset>

</form>
<?php endif; ?>