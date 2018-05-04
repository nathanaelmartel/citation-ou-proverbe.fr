<?php

	$baseline = array(
		'«&nbsp;L’education est l’arme la plus puissante pour changer le monde&nbsp;» — Nelson Mandela',
		'L’éducation de nos enfants est notre priorité : nous nous sommes donnés comme objectif de construire une école',
		'«&nbsp;Si tu veux aller vite, marche seul ; mais si tu veux aller loin, marchons ensemble&nbsp;» — proverbe africain',
		'«&nbsp;Un Enfant sans éducation est comme un oiseau sans ailes&nbsp;» — proverbe Tibétain',
		'Vous pouvez lire et écrire ceci… Pas les enfants de Niellé.',
	);

	$color = array('#ffe114', '#feea6d', '#f18111', '#fe778a', '#049fd7', '#e9ecd7');

?>


<div class="clear"></div>

<a class="badessatellites" href="http://www.badessatellites.com?utm_medium=website&utm_campaign=citation-ou-proverbe.fr" target="_blank" style="background-color:<?php echo $color[rand(0, count($color)-1)]?>">
	<span><?php echo $baseline[rand(0, count($baseline)-1)]?></span>
</a>

<div class="clear"></div>
