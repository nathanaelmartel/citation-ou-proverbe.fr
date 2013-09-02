

  <div id="author-<?php echo $author->id ?>" class="author-card" itemscope itemtype="http://schema.org/Person">
    <?php if ($author->getDates() != '' ): ?>
    	<span class="dates" itemtype="birthDate"><?php echo $author->getDates() ?></span>
		<?php endif; ?>
    <h2 class="author-name" itemtype="name">
    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
    		<?php echo $author->name ?>
    	</a>
    </h2>
    <?php if ($author->has_thumbnail): ?>
			<img src="<?php echo url_for('@portrait_image?author='.$author->slug.'&effect=contour&sf_format=jpg&authorb='.$author->slug) ?>" alt="" class="portrait" itemtype="image" />
		<?php endif; ?>
    <p itemtype="description"><?php echo $author->getShortDescription() ?></p>
    <p>
    <?php if ($author->getWikipediaUrl() != '' ): ?>
    	<a href="<?php echo $author->getWikipediaUrl() ?>" class="wikipedia" target="_blank" ><?php echo $author->name ?> sur wikipedia</a><br />
		<?php endif; ?>
	   	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>" class="author-link" itemtype="url">
	    	Toutes les citations de <?php echo $author->name ?>
	    </a>
    </p>
  </div>
  