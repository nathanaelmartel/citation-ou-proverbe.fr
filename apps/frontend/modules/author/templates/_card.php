

  <div id="author-<?php echo $author->id ?>" class="author-card">
    <?php if ($author->getDates() != '' ): ?>
    	<span class="dates"><?php echo $author->getDates() ?></span>
		<?php endif; ?>
    <h2 class="author-name">
    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
    		<?php echo $author->name ?>
    	</a>
    </h2>
    <?php if ($author->has_thumbnail): ?>
			<img src="<?php echo url_for('@portrait_image?author='.$author->slug.'&effect=contour&sf_format=jpg&authorb='.$author->slug) ?>" alt="" class="portrait" />
		<?php endif; ?>
    <p><?php echo $author->getShortDescription() ?></p>
    <p>
    <?php if ($author->getWikipediaUrl() != '' ): ?>
    	<a href="<?php echo $author->getWikipediaUrl() ?>" class="wikipedia" target="_blank" ><?php echo $author->name ?> sur wikipedia</a><br />
		<?php endif; ?>
	   	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>" class="author-link">
	    	Toutes les citations de <?php echo $author->name ?>
	    </a>
    </p>
  </div>
  