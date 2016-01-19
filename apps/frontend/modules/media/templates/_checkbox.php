

<div class="row">

  <div class="labels push-10 grid-90 mobile-push-20 mobile-grid-80">
  	<label for="<?php echo $name ?>"><?php echo $label ?></label>
    <em class="help"><?php echo $help ?></em>
  </div>
  
  <div class="fields pull-90 grid-10 mobile-pull-80 mobile-grid-20">
    <input type="checkbox" class="checkbox" id="<?php echo $name ?>" name="<?php echo $name ?>" <?php echo ($value)?'checked':''; ?> />
  </div>
  
  <div class="clear"></div>
    
</div>
