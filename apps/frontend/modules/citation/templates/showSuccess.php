<?php slot('header') ?>
  <link rel="canonical" href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  
	<?php include_partial('citation/opengraph', array('citation' => $citation))?>
	<?php include_partial('citation/twittercard', array('citation' => $citation))?>
<?php end_slot() ?>


<div id="citation-<?php echo $citation->id ?>" class="citation">
	<div class="grid-60">
		<?php include_partial('citation/card', array('citation' => $citation))?>
	</div>
	
	
	<div class="grid-40">
	  <?php include_partial('author/card', array('author' => $citation->Author))?>
	</div>
	
	<div class="clear"></div>
</div>


<div class="grid-60 action">
  <?php include_partial('citation/action', array(
  		'citation' => $citation, 
  		'url' => url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)), 
  		'image' => url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true))
  )) ?>
</div>

<?php if (count($citation->Tags) > 0): ?>
<div class="grid-40 tags">
	<h2>D'autres citations ou proverbe sur les thèmes :</h2>
	<ul>
		<?php foreach ($citation->Tags as $tag):?>
			<li><a href="<?php echo url_for('@tag?slug='.$tag->slug) ?>"><?php echo $tag ?></a></li>
		<?php endforeach ?>
	</ul>
</div>
<?php endif ?>
