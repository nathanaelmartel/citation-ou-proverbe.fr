
  <a href="<?php echo url_for('@citation_wallpaper?author='.$citation->Author->slug.'&slug='.$citation->slug)?>" title="Citation en fond d'écran" >fond d'écran</a> 
	<a class="mail-share icon" href="<?php echo url_for('@send_mail?id='.$citation->id)?>" title="Envoyer la citation par mail">&nbsp;</a>
  <a class="facebook-share icon" title="partager sur facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo $url.'?pk_campaign=share&pk_kwd=share-facebook' ?>&amp;t=<?php echo $citation->quote ?>">&nbsp;</a> 
  <a class="twitter-share icon" title="partager sur twitter" target="_blank" href="http://twitter.com/share?text=<?php echo $citation->quote ?>&amp;url=<?php echo $url.'?pk_campaign=share&pk_kwd=share-twitter' ?>">&nbsp;</a> 
  <a class="google-share icon" title="partager sur google+" target="_blank" href="https://plus.google.com/share?url=<?php echo $url.'?pk_campaign=share&pk_kwd=share-googleplus' ?>&amp;hl=fr">&nbsp;</a> 
  <a class="pinterest-share icon" title="partager sur pinterest" target="_blank" href="http://pinterest.com/pin/create/button/?url=<?php echo $url.'?pk_campaign=share&pk_kwd=share-pinterest' ?>&amp;media=<?php echo $image ?>&amp;description=<?php echo $citation->quote ?>">&nbsp;</a> 
	<!--  <a class="note" title="notez la citation"><span>&nbsp;<span>&nbsp;<span>&nbsp;<span>&nbsp;<span>&nbsp;</span></span></span></span></span> 4,5</a> --> 
	
	