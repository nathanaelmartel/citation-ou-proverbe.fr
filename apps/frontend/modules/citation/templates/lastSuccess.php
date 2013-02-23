

<?php foreach ($citations as $citation):?>

	<div class="citation">
		
		<div class="grid_8 prefix_1 card-container">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		</div>
		<div class="grid_6 aside">
			<?php include_partial('citation/aside', array('citation' => $citation))?>
		</div>
		
		<div class="clear"></div>
	</div>
	
<?php endforeach ?>



