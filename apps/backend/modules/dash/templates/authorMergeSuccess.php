<div id="sf_admin_container">
	<h1>Author Merge</h1>
	
	<?php if ($sf_user->hasFlash('notice')): ?>
	  <div class="notice"><?php echo $sf_user->getFlash('notice') ?></div>
	<?php endif; ?>
	
	<?php if ($sf_user->hasFlash('error')): ?>
	  <div class="error"><?php echo $sf_user->getFlash('error') ?></div>
	<?php endif; ?>
	
	
	<div id="sf_admin_content">
		<form action="<?php echo url_for('@author_merge')?>">
			<fieldset id="sf_fieldset_none">
			
				<div class="sf_admin_form_row sf_admin_text">
					<div>
						<label for="old_author">Old author</label>
						<input type="text" id="old_author" name="old_author" />
						<span id="old_autocomplete"></span>
						<input type="number" id="old_author_id" name="old_author_id" max="90000" required="required" placeholder="id" />
					</div>
				</div>
				
				<div class="sf_admin_form_row sf_admin_text">
					<div>
						<label for="new_author">New author</label>
						<input type="text" id="new_author" name="new_author" />
						<span id="new_autocomplete"></span>
						<input type="number" id="new_author_id" name="new_author_id" max="90000" required="required" placeholder="id" />
					</div>
				</div>
			
			</fieldset>
			
			<ul class="sf_admin_actions">
				<li class="sf_admin_action_save">
					<input type="submit" value="Merge" />
				</li>
			</ul>
		</form>
		
	</div>
	<div class="clear"></div>
	
</div>

<script>


$(document).ready(function () {
	if ($("#old_author").length) {
		  $("#old_author").autocomplete({
			  appendTo: "#old_autocomplete",
			  source: "/auteurs/search",
			  minLength: 2,
			  select: function( event, ui ) {
				  if (ui.item)
					  $( "#old_author_id" ).attr('value',  ui.item.id);
			  }
		  });
	}
	if ($("#new_author").length) {
		  $("#new_author").autocomplete({
			  appendTo: "#new_autocomplete",
			  source: "/auteurs/search",
			  minLength: 2,
			  select: function( event, ui ) {
				  if (ui.item)
					  $( "#new_author_id" ).attr('value',  ui.item.id);
			  }
		  });
	}
});

</script>