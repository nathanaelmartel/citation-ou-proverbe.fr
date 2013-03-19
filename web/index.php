<?php

$application = 'frontend';
$env = 'prod';

$site_local = array(
		'citation-ou-proverbe.fr.dev', 
		'admin.citation-ou-proverbe.fr.dev'
);

$backend = array(
		'admin.citation-ou-proverbe.fr.dev',
		'admin.citation-ou-proverbe.fr'
);

if (in_array($_SERVER['HTTP_HOST'], $backend))
	$application = 'backend';

$api = array(
		'api.citation-ou-proverbe.fr.dev',
		'api.citation-ou-proverbe.fr'
);

if (in_array($_SERVER['HTTP_HOST'], $api))
	$application = 'api';

if (in_array($_SERVER['HTTP_HOST'], $site_local))
	$env = 'dev';

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration($application, $env, $env == 'dev');
sfContext::createInstance($configuration)->dispatch();



