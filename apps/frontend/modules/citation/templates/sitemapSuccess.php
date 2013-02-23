<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9             http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($authors as $author): ?>
<url>
  <loc>
  	<?php echo url_for('@author?slug='.$author->slug, array('absolute' => true)) ?>
  </loc>
</url>
<?php endforeach; ?>
<?php foreach ($tags as $tag): ?>
<url>
  <loc>
  	<?php echo url_for('@tag?slug='.$tag->slug, array('absolute' => true)) ?>
  </loc>
</url>
<?php endforeach; ?>
<?php foreach ($citations as $citation): ?>
<url>
  <loc>
  	<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>
  </loc>
</url>
<?php endforeach; ?>
</urlset>
