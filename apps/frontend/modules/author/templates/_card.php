

  <div class="author-card">
    <h2 class="author-name">
    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
    		<?php echo $author->name ?>
    	</a>
    </h2>
    <p><?php echo $author->getShortDescription() ?></p>
  </div>
  