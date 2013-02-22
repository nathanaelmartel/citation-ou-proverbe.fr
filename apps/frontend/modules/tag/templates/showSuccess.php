
<h1><?php echo $tag->name ?></h1>
<ul>
	<?php foreach ($tag->Citations as $citation): ?>
		<li>
			«<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug)?>"><?php echo $citation->quote ?></a>»
		</li>
	<?php endforeach; ?>
</ul>