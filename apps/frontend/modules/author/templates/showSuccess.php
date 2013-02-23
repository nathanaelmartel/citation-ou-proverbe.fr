

<div class="grid_14 prefix_1">
	<h1><?php echo $author->name ?></h1>
</div>

<div class="grid_8 prefix_1">

<?php foreach ($citations as $citation):?>

	<div class="citation">
		<div class="card-container">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		</div>
	</div>
	
<?php endforeach ?>
</div>

<div class="grid_6">
	<div class="author-card">
		<p><?php echo $author->getDescription() ?></p>
	</div>
	
	<?php if ($citations->haveToPaginate()): ?>
		<div class="action">
			<?php $links = $citations->getLinks(); ?>
			<?php foreach ($links as $page): ?>
				<?php if ($page == $citations->getPage()): ?>
					<span><?php echo $page?></span>
				<?php else: ?>
					<a href="<?php echo url_for( '@author_page?slug='.$author->slug.'&page='.$page) ?>" title="Citations ou Proverbes de : <?php echo $author->name?>"><?php echo $page?></a>
				<?php endif; ?>
			<?php endforeach ?>
		</div>    
	<?php endif ?>
	
</div>
