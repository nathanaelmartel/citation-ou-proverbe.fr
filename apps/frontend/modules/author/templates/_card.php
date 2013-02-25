

  <div class="author-card">
    <h2 class="author-name">
    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
    		<?php echo $author->name ?>
    	</a>
    </h2>
    <?php if ($author->has_thumbnail): ?>
			<img src="<?php echo url_for('@portrait_image?author='.$author->slug.'&effect=contour&sf_format=jpg&authorb='.$author->slug) ?>" alt="" class="portrait" />
		<?php endif; ?>
    <p><?php echo $author->getShortDescription() ?></p>
  </div>
  