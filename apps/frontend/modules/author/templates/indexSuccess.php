

<div class="grid-100">
	<h1>Auteur de citations</h1>
</div>

<div class="top-authors top-authors-10">
	<?php foreach ($top_authors as $author):?>
	
	  <div class="author-card grid-20 mobile-grid-50">
	    <h2 class="author-name">
	    	<a href="<?php echo url_for('@author?slug='.$author['slug']) ?>">
	    		<?php echo $author['name'] ?> 
	    	</a>
	    </h2>
	    (<?php echo $author['nb'] ?> citations)
	    <img src="<?php echo url_for('@portrait_image?author='.$author['slug'].'&effect=contour&sf_format=jpg&authorb='.$author['slug']) ?>" alt="<?php echo $author['name'] ?> "  />
	  </div>
		
	<?php endforeach ?>
	<div class="clear"></div>
</div>

<div class="top-authors">
	<?php foreach ($authors as $author):?>
	
	  <div class="author-card grid-20 mobile-grid-50">
	    <h2 class="author-name">
	    	<a href="<?php echo url_for('@author?slug='.$author['slug']) ?>">
	    		<?php echo $author['name'] ?> 
	    	</a>
	    </h2>
	    (<?php echo $author['nb'] ?> citations)
	  </div>
		
	<?php endforeach ?>
</div>