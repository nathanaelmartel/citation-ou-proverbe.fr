
<article>


<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "NewsArticle",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://google.com/article"
  },
  "headline": "Citation de <?php echo $citation->Author->name ?>",
  "image": [
    "<?php echo url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)) ?>"
   ],
  "datePublished": "2015-02-05T08:00:00+08:00",
  "dateModified": "2015-02-05T09:20:00+08:00",
  "author": {
    "@type": "Person",
    "name": "<?php echo $citation->Author->name ?>"
  },
   "publisher": {
    "@type": "Organization",
    "name": "Citation ou Proverbe",
    "logo": {
      "@type": "ImageObject",
      "url": "https://www.citation-ou-proverbe.fr/apple-touch-icon.png"
    }
  },
  "description": "<?php echo $citation->quote ?>"
}
</script>

	<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" class="card-container" >
		<blockquote style="color: <?php echo $citation->getTextRGBColorHex() ?>;background-color: <?php echo $citation->getRGBColorHex() ?>;" data-info="<?php echo $citation->getInfo() ?>">
			<?php echo $citation->quote ?>
		</blockquote>
	</a>
	<p class="context">
		<a href="<?php echo url_for('@author?slug='.$citation->Author->slug) ?>" class="author"><?php echo $citation->Author->name ?></a>
		<?php if (($citation->source_id != '' ) && ($citation->Source->is_active)): ?>
			<a href="<?php echo url_for('@source?slug='.$citation->Source->slug.'&author='.$citation->Author->slug ) ?>" target="_blank" ><?php echo $citation->getSource() ?></a>
		<?php else: ?>
			<span class="source"><?php echo $citation->getSource() ?></span>
		<?php endif;?>
	</p>
</article>
	
