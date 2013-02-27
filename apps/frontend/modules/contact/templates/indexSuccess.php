


	<div class="grid-100">
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

    <div class="grid-20 prefix-20 mobile-grid-50 mobile-prefix-50 row-control">
      <input type="submit" value="Envoyer" class="buton"/>
    </div>

  </fieldset>

</form>