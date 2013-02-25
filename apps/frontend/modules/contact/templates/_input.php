
<?php if(!$field->isHidden()):?>
<div class="row <?php echo $field->hasError()?'error_row':''; ?> <?php echo $field->getName() == 'comments'?'textarea':''; ?>">

  <div class="labels grid_2 prefix_2">
  <?php echo $field->renderLabel() ?>
    <em class="help"><?php echo $field->renderHelp() ?></em>
  </div>
  
  <div class="fields grid_4">
    <?php echo $field->render(array('class' => 'text')) ?>
  </div>
  
  <div class="clear"></div>

  <?php echo $field->renderError() ?>
  
  <div class="clear"></div>
    
</div>
<?php else:?>
  <?php echo $field->render() ?>
<?php endif;?> 
