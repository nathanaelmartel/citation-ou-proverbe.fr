

	<div class="grid-100">
  	<h1>Fond d'écran personalisé</h1>
  	<p class="subheader">Citaton : «<?php echo $citation->quote ?>» <?php echo $citation->Author->name ?></p>
  </div>
	<div class="clear"></div>
	
	<div class="grid-50">
	
		<form action="" method="post" id="wallpaper-form">
		
		  <fieldset class="form" >
				
				<input type="hidden" id="author" value="<?php echo $citation->Author->slug ?>" />
				<input type="hidden" id="citation" value="<?php echo $citation->id ?>" />
		    <?php include_partial('media/input', array('name' => 'width', 'label' => 'Largeur', 'help' => '', 'value' => '1200', 'type' => 'text')); ?>
		    <?php include_partial('media/input', array('name' => 'height', 'label' => 'Hauteur', 'help' => '', 'value' => '768', 'type' => 'text')); ?>
		    <?php include_partial('media/input', array('name' => 'bgcolor', 'label' => 'Couleur de fond', 'help' => '', 'value' => $citation->getRGBColorHex(), 'type' => 'color' )); ?>
		    <?php include_partial('media/input', array('name' => 'textcolor', 'label' => 'Couleur du texte', 'help' => '', 'value' => $citation->getTextRGBColorHex(), 'type' => 'color' )); ?>
		    <?php include_partial('media/checkbox', array('name' => 'authorname', 'label' => 'Afficher le nom de l\'auteur', 'help' => '', 'value' => true )); ?>
		    <?php if ($citation->Author->has_thumbnail): ?>
		    	<?php include_partial('media/checkbox', array('name' => 'authoravatar', 'label' => 'Afficher la photo de l\'auteur', 'help' => '', 'value' => $citation->Author->has_thumbnail )); ?>
		    <?php endif ?>
		
		  </fieldset>
		
		</form>

	</div>
	
	<div class="grid-50">
		<?php $image = url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug);  ?>
	  <img src="<?php echo $image ?>" alt="«<?php echo $citation->quote ?>» <?php echo $citation->Author->name ?>" id="wallpaper" class="wallpaper" />
		<div class="action">
	  	<a href="<?php echo $image ?>" target="_blank" title="Télécharger la citation en fond d'écran" >Télécharger le fond d'écran</a>
			<!--  <a class="mail-share icon" href="<?php echo url_for('@send_mail?id='.$citation->id)?>" title="Envoyer la citation par mail">&nbsp;</a> -->
	  </div>
	</div>