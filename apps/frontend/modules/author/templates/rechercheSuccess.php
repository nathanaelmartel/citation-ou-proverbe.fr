

<div class="grid-100">
	<h1>Recherche par Auteur de citations</h1>
</div>

<div class="author-search">
	
	<form action="<?php echo url_for('@authors_search_page') ?>" method="post">
	  <fieldset class="form" >
	  
		  <div class="labels grid-20 prefix-10 mobile-grid-40">
		  	<label for="author_search">Rechercher un auteur :</label>
		  </div>
		  
		  <div id="autocomplete" class="fields grid-40 mobile-grid-60">
		  	<input type="text" value="<?php echo $term ?>" id="author_search" name="author_search" class="text" />
		  </div>
	  
		  <div class="grid-20 mobile-prefix-40 mobile-grid-60">
		  	<input type="submit" value="Chercher" class="buton"/>
		  </div>
		  
		  <div class="clear"></div>
	
	  </fieldset>
	
	</form>
</div>

<div class="grid-100">
	<p>Résultat de la recherche pour «<?php echo $term ?>»</p>
</div>

<div class="top-authors">
	<?php foreach ($authors as $author):?>
	
	  <div class="author-card grid-20 mobile-grid-50">
	    <h2 class="author-name">
	    	<a href="<?php echo url_for('@author?slug='.$author->slug) ?>">
	    		<?php echo $author->name ?> 
	    	</a>
	    </h2>
	    (<?php echo $author->getNbCitations() ?> citations)
	  </div>
		
	<?php endforeach ?>
</div>