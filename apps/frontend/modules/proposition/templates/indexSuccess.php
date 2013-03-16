


	<div class="grid-100">
  	<h1>Proposer une citation</h1>
  </div>
	<div class="clear"></div>
	
<form action="<?php echo url_for('@new_citation_validation') ?>" method="post" <?php //$form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>

  <fieldset class="form" >
  
  <div class="row">

	  <div class="labels grid-10 prefix-10 mobile-grid-40">
	  	<label for="author_name">Auteur</label>
	  </div>
	  
	  <div id="autocomplete" class="fields grid-40 mobile-grid-60">
	  	<input type="text" value="" id="author_name" name="author_name" class="text" />
	  </div>
	  
	  <div class="clear"></div>
	    
	</div>

  	

    <?php if (!$form->getObject()->isNew()): ?>
      <input type="hidden" name="sf_method" value="put"/>
    <?php endif; ?>

    <?php foreach ($form as $fieldName => $field): ?>
      <?php include_partial('proposition/input', array('field' => $field)); ?>
    <?php endforeach; ?>

    <div class="grid-20 prefix-20 mobile-grid-30 mobile-prefix-40 row-control">
      <input type="submit" value="Proposer" class="buton"/>
    </div>

  </fieldset>

</form>