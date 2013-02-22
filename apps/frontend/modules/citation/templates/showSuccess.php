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



<h1><?php echo $citation->quote ?></h1>
<a href="<?php echo url_for('@author?slug='.$citation->Author->slug) ?>" class="author"><?php echo $citation->Author->name ?></a>
<ul>
	<?php foreach ($citation->Tags as $tag):?>
		<li><a href="<?php echo url_for('@tag?slug='.$tag->slug) ?>"><?php echo $tag ?></a></li>
	<?php endforeach ?>
</ul>