

  <meta name="twitter:card" content="summary" />
  <meta name="twitter:url" content="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta name="twitter:title" content="«<?php echo $citation->quote; ?>»" />
  <meta name="twitter:description" content="«<?php echo $citation->quote; ?>» <?php echo $citation->Author->name; ?>" />
  <meta name="twitter:image" content="<?php echo url_for('@citation_twitter_image?sf_format=png&id='.$citation->id.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta name="twitter:site" content="@1citation" />
  