

<?php foreach ($citations as $citation):?>

	<div id="citation-<?php echo $citation->id ?>" class="citation">
		
		<div class="grid-60">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		</div>
		<div class="grid-40 aside">
			<?php include_partial('citation/aside', array('citation' => $citation))?>
		</div>
		
		<div class="clear"></div>
	</div>
	
<?php endforeach ?>



