
		<div id="site-name" >
			<a href="<?php echo url_for('@homepage')?>" title="Dernières Citations" >Citations</a>
		</div>

    <nav>
      <a href="<?php echo url_for('@homepage')?>">Dernières Citations</a>
      <a href="<?php echo url_for('@authors')?>">Auteurs</a>
      <a href="<?php echo url_for('@themes')?>">Thèmes</a>
      <a href="<?php echo url_for('@random')?>">Une Citation Aléatoire</a>
      <a href="https://twitter.com/citation_fr" target="_blank" >Suivez nous sur Twitter</a>
      <a href="<?php echo url_for('@newsletter')?>" style="color: #000000;" >Recevoir une citation quotidienne par mail</a>
    </nav>
    
  	<div class="search-form">
	    <form action="<?php echo url_for('@recherche')?>" id="cse-search-box">
			  <div>
			    <input type="hidden" name="cx" value="partner-pub-6736033252489950:4620028292" />
			    <input type="hidden" name="cof" value="FORID:10" />
			    <input type="hidden" name="ie" value="UTF-8" />
			    <input type="text" name="q" size="30" />
			    <input type="submit" name="sa" value="Rechercher" />
			  </div>
			</form>
			<script type="text/javascript" src="http://www.google.fr/coop/cse/brand?form=cse-search-box&amp;lang=fr"></script>
		</div>