

<?php foreach ($citations as $key => $citation):?>

	<div class="citation">
	
		<?php if ($key%2 == 0): ?>
		
			<div class="grid_6 prefix_1 aside">
				<?php include_partial('citation/aside', array('citation' => $citation))?>
			</div>
			<div class="grid_8 card-container">
				<?php include_partial('citation/card', array('citation' => $citation))?>
			</div>
			
		<?php else: ?>
		
			<div class="grid_8 prefix_1 card-container">
				<?php include_partial('citation/card', array('citation' => $citation))?>
			</div>
			<div class="grid_6 aside">
				<?php include_partial('citation/aside', array('citation' => $citation))?>
			</div>
					
		<?php endif ?>
		
		<div class="clear"></div>
	</div>
	
<?php endforeach ?>



