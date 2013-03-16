
<?php if(!$field->isHidden()):?>
<div class="row <?php echo $field->hasError()?'error_row':''; ?> <?php echo $field->getName() == 'comments'?'textarea':''; ?>">

  <div class="labels grid-10 prefix-10 mobile-grid-40">
  <?php echo $field->renderLabel() ?>
    <em class="help"><?php echo $field->renderHelp() ?></em>
  </div>
  
  <div class="fields grid-40 mobile-grid-60">
    <?php echo $field->render(array('class' => 'text')) ?>
  	<?php echo $field->renderError() ?>
  </div>
  
  <div class="clear"></div>
    
</div>
<?php else:?>
  <?php echo $field->render() ?>
<?php endif;?> 
