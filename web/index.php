<?php

$application = 'frontend';
$env = 'prod';

$site_local = array(
		'v2.citation-et-proverbe.fr.dev', 
		'v2-admin.citation-et-proverbe.fr.dev'
);

$backend = array(
		'v2-admin.citation-et-proverbe.fr.dev'
		'v2-admin.citation-et-proverbe.fr'
);

if (in_array($_SERVER['HTTP_HOST'], $backend))
	$application = 'backend';

if (in_array($_SERVER['HTTP_HOST'], $site_local))
	$env = 'dev';

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration($application, $env, $env == 'dev');
sfContext::createInstance($configuration)->dispatch();
