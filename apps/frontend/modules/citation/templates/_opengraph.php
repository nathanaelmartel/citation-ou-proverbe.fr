


  <meta property="og:title" content="«<?php echo $citation->quote; ?>»" />
  <meta property="og:description" content="«<?php echo $citation->quote; ?>» <?php echo $citation->Author->name; ?>" />
  <meta property="og:image" content="<?php echo url_for('@citation_image?sf_format=png&slug='.$citation->slug.'&author='.$citation->Author->slug.'&authorb='.$citation->Author->slug, array('absolute' => true)) ?>" />
  <meta property="og:url" content="<?php echo url_for('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, array('absolute' => true)) ?>" />
  