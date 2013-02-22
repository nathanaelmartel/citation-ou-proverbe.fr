
<h1><?php echo $author->name ?></h1>

<ul>
	<?php foreach ($author->Citations as $citation): ?>
		<li>
			«<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$author->slug)?>"><?php echo $citation->quote ?></a>»
		</li>
	<?php endforeach; ?>
</ul>