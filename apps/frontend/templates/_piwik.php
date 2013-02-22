<?php 

if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')))
{
	require_once sfConfig::get('sf_lib_dir').'/vendor/piwik/PiwikTracker.php';
	PiwikTracker::$URL = 'http://piwik.fam-martel.eu/';
	
	$piwikTracker = new PiwikTracker( $idSite = 17 );
	$piwikTracker->doTrackPageView($sf_context->getResponse()->getTitle());
}