

<div class="grid-100">
	<h1>Thèmes de citations</h1>
</div>

<div class="tags grid-100">
	<ul>
		<?php foreach ($tags as $tag):?>
			<li>
		    	<a href="<?php echo url_for('@tag?slug='.$tag['slug']) ?>" style="font-size:<?php echo max(100, $tag['nb']/12) ?>%;" title="<?php echo $tag['nb'] ?> citations ou proverbes sur le thème <?php echo $tag['name'] ?>">
		    		<?php echo $tag['name'] ?>
		    	</a>
			</li>
		<?php endforeach ?>
	</ul>
</div>
