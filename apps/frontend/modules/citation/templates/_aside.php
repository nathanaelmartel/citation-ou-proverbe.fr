
	<aside>	
	  <div class="author-card">
	    <h2 class="author-name">
	    	<a href="<?php echo url_for('@author?slug='.$citation->Author->slug) ?>">
	    		<?php echo $citation->Author->name ?>
	    	</a>
	    </h2>
	  </div>
	  <?php if (count($citation->Tags) > 0): ?>
		<div class="tags">
			<h2>D'autres citations ou proverbe sur les thèmes :</h2>
			<ul>
				<?php foreach ($citation->Tags as $tag):?>
					<li><a href="<?php echo url_for('@tag?slug='.$tag->slug) ?>"><?php echo $tag ?></a></li>
				<?php endforeach ?>
			</ul>
		</div>
		<?php endif ?>
		<div class="action">
		  <?php include_partial('citation/action', array(
  				'citation' => $citation, 
		  		'url' => url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)), 
		  		'image' => url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true))
		  )) ?>
		</div>
	</aside>