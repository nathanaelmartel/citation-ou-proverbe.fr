


<div class="grid-100">
	<h1><?php echo $tag->name ?></h1>
</div>


<?php $i = 0;foreach ($citations as $citation):?>

	<div class="citation">
		<div class="grid-60">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		</div>
		<div class="grid-40 aside">
			<?php include_partial('author/card', array('author' => $citation->Author))?>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php if ($i++%5 == 0): ?>
		<?php include_partial('ads/rdb') ?>
	<?php endif ?>
	
<?php endforeach ?>



<?php if ($citations->haveToPaginate()): ?>
<div class="grid-100">
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
