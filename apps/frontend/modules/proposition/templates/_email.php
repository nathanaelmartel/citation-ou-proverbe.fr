
<p>
Auteur : <?php echo $citation->Author->name . "\n" ?> (<a href="https://admin.citation-ou-proverbe.fr/index.php/author/<?php echo $citation->author_id ?>/edit">BO</a>) <br />
Citation : <?php echo $citation->quote . "\n"; ?> (<a href="https://admin.citation-ou-proverbe.fr/index.php/citation/<?php echo $citation->id ?>/edit">BO</a>) <br />
Source : <?php echo $citation->source ?> <br />
</p>
<p><a href="https://www.citation-ou-proverbe.fr/proposer-citation/validation/approbation/<?php echo $citation->id ?>">Approuver</a></p>

<p>
-- <br />
L'équipe de <a href="https://www.citation-ou-proverbe.fr?pk_campaign=email&pk_kwd=email-footer" style="color:#000;">Citation ou Proverbe</a>
</p>
