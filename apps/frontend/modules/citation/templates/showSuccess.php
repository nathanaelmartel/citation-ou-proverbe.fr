<?php slot('header') ?>
  <link rel="canonical" href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  
	<?php include_partial('citation/opengraph', array('citation' => $citation))?>
	<?php include_partial('citation/twittercard', array('citation' => $citation))?>
<?php end_slot() ?>


<div class="grid_8 prefix_1 card-container">
	<?php include_partial('citation/card', array('citation' => $citation))?>
</div>


<div class="grid_6">
  <?php include_partial('author/card', array('author' => $citation->Author))?>
</div>

<div class="clear"></div>

<div class="grid_8 prefix_1 action">
	<a href="<?php echo url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)) ?>">fond d'écran</a>
	<!-- <a href="">Personaliser le fond d'écran</a>
	<a href="">Envoyer la citation par mail</a>-->
</div>

<?php if (count($citation->Tags) > 0): ?>
<div class="grid_6 tags">
	<h2>D'autres citations ou proverbe sur les thèmes :</h2>
	<ul>
		<?php foreach ($citation->Tags as $tag):?>
			<li><a href="<?php echo url_for('@tag?slug='.$tag->slug) ?>"><?php echo $tag ?></a></li>
		<?php endforeach ?>
	</ul>
</div>
<?php endif ?>
