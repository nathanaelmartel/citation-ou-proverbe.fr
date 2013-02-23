<?php slot('header') ?>
  <link rel="canonical" href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  
  <meta property="og:title" content="«<?php echo $citation->quote; ?>»" />
  <meta property="og:description" content="«<?php echo $citation->quote; ?>» <?php echo $citation->Author->name; ?>" />
  <meta property="og:image" content="<?php echo url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta property="og:url" content="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  
  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta name="twitter:title" content="«<?php echo $citation->quote; ?>»" />
  <meta name="twitter:description" content="«<?php echo $citation->quote; ?>» <?php echo $citation->Author->name; ?>" />
  <meta name="twitter:image" content="<?php echo url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta name="twitter:site" content="@1citation" />
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
