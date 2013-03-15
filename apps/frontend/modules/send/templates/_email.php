
<p>Vous avez reçu une citation de <?php echo $send->email; ?></p>
<div><?php echo nl2br($send->comments) . "\n"; ?></div>

	<a href="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>?pk_campaign=email&pk_kwd=email-citation" class="card-container" style="width: 460px;display: block;text-decoration: none;">
		<blockquote style="color: <?php echo $citation->getTextRGBColorHex() ?>;background-color: <?php echo $citation->getRGBColorHex() ?>;width: 460px;display: table-cell;font-size: 1.8em;height: 8em;line-height: 1.2em;padding: 1em;vertical-align: middle;">
			<?php echo $citation->quote ?>
		</blockquote>
	</a>
	
 
<p>Retrouver d'autres citations de <a style="color:#000;" href="<?php echo url_for('@author?slug='.$citation->Author->slug, array('absolute' => true)) ?>?pk_campaign=email&pk_kwd=email-auteur"><?php echo $citation->Author->name; ?></a></p> 

<p>
-- <br />
L'équipe de <a href="http://www.citation-ou-proverbe.fr?pk_campaign=email&pk_kwd=email-footer" style="color:#000;">Citation ou Proverbe</a>
</p>