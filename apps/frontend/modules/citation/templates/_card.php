
	<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>">
		<blockquote style="color: <?php echo $citation->getTextRGBColorHex() ?>;background-color: <?php echo $citation->getRGBColorHex() ?>;">
			<?php echo $citation->quote ?>
		</blockquote>
	</a>
	