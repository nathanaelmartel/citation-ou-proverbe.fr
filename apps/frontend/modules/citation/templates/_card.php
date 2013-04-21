
	<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" class="card-container" >
		<blockquote style="color: <?php echo $citation->getTextRGBColorHex() ?>;background-color: <?php echo $citation->getRGBColorHex() ?>;" data-info="<?php echo $citation->getInfo() ?>">
			<?php echo $citation->quote ?>
		</blockquote>
	</a>
	<p class="context">
		<a href="<?php echo url_for('@author?slug='.$citation->Author->slug) ?>" class="author"><?php echo $citation->Author->name ?></a>
		<?php if (substr($citation->source, 0, 4) == 'http' ): ?>
			<a href="<?php echo $citation->source ?>" target="_blank" >source</a>
		<?php else: ?>
			<span class="source"><?php echo $citation->source ?></span>
		<?php endif;?>
	</p>
	