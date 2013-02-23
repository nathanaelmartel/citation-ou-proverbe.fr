


<div class="grid_14 prefix_1">
	<h1><?php echo $tag->name ?></h1>
</div>


<?php foreach ($citations as $citation):?>

	<div class="citation">
		<div class="grid_8 prefix_1 card-container">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		</div>
		<div class="grid_6 aside">
			<?php include_partial('author/card', array('author' => $citation->Author))?>
		</div>
		<div class="clear"></div>
	</div>
	
<?php endforeach ?>



<?php if ($citations->haveToPaginate()): ?>
<div class="grid_14 prefix_1">
	<div class="action">
		<?php $links = $citations->getLinks(); ?>
		<?php foreach ($links as $page): ?>
			<?php if ($page == $citations->getPage()): ?>
				<span><?php echo $page?></span>
			<?php else: ?>
				<a href="<?php echo url_for( '@tag_page?slug='.$tag->slug.'&page='.$page) ?>" title="Citations ou Proverbes sur le thème : <?php echo $tag->name?>"><?php echo $page?></a>
			<?php endif; ?>
		<?php endforeach ?>
	</div>    
</div>
<?php endif ?>

<div class="clear"></div>