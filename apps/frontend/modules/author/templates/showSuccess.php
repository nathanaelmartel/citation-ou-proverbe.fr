

<div class="grid-100">
	<h1><?php echo $author->name ?></h1>
</div>

<div class="grid-60 author-citations">

<?php foreach ($citations as $citation):?>

	<div id="citation-<?php echo $citation->id ?>" class="citation">
			<?php include_partial('citation/card', array('citation' => $citation))?>
		<div class="action">
		  <?php include_partial('citation/action', array(
  				'citation' => $citation, 
		  		'url' => url_for('@citation?slug='.$citation->slug.'&author='.$author->slug, array('absolute' => true)), 
		  		'image' => url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$author->slug.'&authorb='.$author->slug, array('absolute' => true))
		  )) ?>
		</div>
	</div>
	
<?php endforeach ?>
</div>

<div id="author-<?php echo $author->id ?>" class="grid-40">
	<div class="author-card">
    <?php if ($author->getDates() != '' ): ?>
    	<span class="dates"><?php echo $author->getDates() ?></span>
		<?php endif; ?>
    <h2 class="author-name">
    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
    		<?php echo $author->name ?>
    	</a>
    </h2>
    <?php if ($author->has_thumbnail): ?>
			<img src="<?php echo url_for('@portrait_image?author='.$author->slug.'&effect=contour&sf_format=jpg&authorb='.$author->slug) ?>" alt="" class="portrait" />
		<?php endif; ?>
		<p><?php echo $author->getDescription() ?></p>
    <p>
    <?php if ($author->getWikipediaUrl() != '' ): ?>
    	<a href="<?php echo $author->getWikipediaUrl() ?>" class="wikipedia" target="_blank" ><?php echo $author->name ?> sur wikipedia</a><br />
		<?php endif; ?>
    </p>
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
