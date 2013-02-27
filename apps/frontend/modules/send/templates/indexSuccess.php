

	<div class="grid-100">
  	<h1>Envoyer la citation par mail</h1>
  </div>
	<div class="clear"></div>
	
	<div class="grid-50">
	
		<form action="<?php echo url_for('@send_mail_validation') ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
		
		  <fieldset class="form" >
		
		    <?php if (!$form->getObject()->isNew()): ?>
		      <input type="hidden" name="sf_method" value="put"/>
		    <?php endif; ?>
		
		    <?php foreach ($form as $fieldName => $field): ?>
		      <?php include_partial('send/input', array('field' => $field)); ?>
		    <?php endforeach; ?>
		
		    <div class="grid-40 prefix-30 mobile-grid-50 mobile-prefix-50 row-control">
		      <input type="submit" value="Envoyer" class="buton"/>
		    </div>
		
		  </fieldset>
		
		</form>

	</div>
	
	<div class="grid-50">
		<?php include_partial('citation/card', array('citation' => $citation))?>
	  <div class="author-card">
	    <h2 class="author-name">
	    	<a href="<?php echo url_for('@author?slug='.$citation->Author->slug) ?>">
	    		<?php echo $citation->Author->name ?>
	    	</a>
	    </h2>
	  </div>
	</div>