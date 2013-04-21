<?php

/**
 * citation actions.
 *
 * @package    citations
 * @subpackage citation
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class citationActions extends sfActions
{
  
  public function executeShort(sfWebRequest $request)
  {
  	$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	if ($citation->slug == '')  {
  		$citation->generateSlug();
    	$citation->save();
  	}
  	
  	$this->redirect('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug, 301);
  }
  
  public function executeNote(sfWebRequest $request)
  {
  	$this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneById(array($request->getParameter('id'))), sprintf('Object citation does not exist (%s).', $request->getParameter('id')));
  	
  	if (!$this->getUser()->getAttribute('note_'.$citation->id)) {
	  	$citation->note = $citation->note + 1;
	  	$citation->save();
	  	$this->getUser()->setAttribute('note_'.$citation->id, true);
	  	
      if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
      	require_once sfConfig::get('sf_lib_dir').'/vendor/piwik/PiwikTracker.php';
        PiwikTracker::$URL = 'http://piwik.fam-martel.eu/';
            
        $piwikTracker = new PiwikTracker( $idSite = 17 );
        //$piwikTracker->setCustomVariable( 6, 'dernière citation notée', $citation->id, 'visit');
        $piwikTracker->doTrackPageView('Noter la citation');
        $piwikTracker->doTrackGoal($idGoal = 6, $revenue = 10000);
      }
  	}
  	
  	$this->setLayout(false);
  	return $this->renderText($citation->note);
  }
  
  public function executeRandom(sfWebRequest $request)
  {
    $citations = Doctrine::getTable('Citation')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit(50)
      ->offset(rand(0, 1000))
      ->orderBy('last_published_at desc')
      ->execute(); 
    
    $citation = $citations[rand(0, 49)];
    
    if($citation)
      $this->redirect('@citation?slug='.$citation->slug.'&author='.$citation->Author->slug);
    else 
      $this->redirect('@homepage');
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->forward404Unless($citation = Doctrine_Core::getTable('Citation')->findOneBySlug(array($slug)), sprintf('Object citation does not exist (%s).', $slug));
    $this->forward404Unless($citation->is_active);
    
    $response = $this->getResponse();
    $response->addMeta('description', $citation->getShortQuote().' - '.$citation->getAuthor().'. Retrouvez d\'autre citations et proverbe sur notre site.');
    $response->setTitle($citation->Author->name.' : '.$citation->quote );
    
    $citation->view = $citation->view + 1;
    $citation->save();
    
    $this->citation = $citation;
  }
  
  public function executeLast(sfWebRequest $request)
  {
  	$citations = Doctrine_Core::getTable('Citation')->retrieveLast();
  	
  	$this->citations = $citations;
  }
  
  public function executeFeed(sfWebRequest $request)
  {
  	$citations = Doctrine_Core::getTable('Citation')->retrieveLast();
  	
    $feed = new sfRss10Feed();

	  $feed->setTitle('Citation ou Proverbe');
	  $feed->setLink('http://www.citation-ou-proverbe.fr/');
	  $feed->setAuthorEmail('contact@citation-ou-proverbe.fr');
	  $feed->setAuthorName('Citation ou Proverbe');

		$feedImage = new sfFeedImage();
		$feedImage->setLink('http://www.citation-ou-proverbe.fr/images/logo.png');
		$feedImage->setTitle('Citation ou Proverbe');
		$feed->setImage($feedImage);
		
	  foreach ($citations as $citation) {
	    $item = new sfFeedItem();
      $item->setTitle($citation->quote);
	    $item->setLink('http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'/'.$citation->slug.'?pk_campaign=feed&pk_kwd=feed-fink');
	    $item->setAuthorName($citation->Author->name);
	    $item->setAuthorEmail('contact@citation-ou-proverbe.fr');
	    $item->setPubdate(strtotime($citation->getLastPublishedAt()));
	    $item->setUniqueId('http://www.citation-ou-proverbe.fr/'.$citation->slug.'/');
	    
	    $description = '<img src="http://www.citation-ou-proverbe.fr/medias/'.$citation->Author->slug.'/'.$citation->Author->slug.'.'.$citation->slug.'.png" alt="'.$citation->quote.'" />' ;
		  $description .= '<p>'.$citation->quote.' <a href="http://www.citation-ou-proverbe.fr/'.$citation->Author->slug.'?pk_campaign=feed&pk_kwd=feed-author" >'.$citation->Author->name.'</a></p>' ;
	    $description .= '<p>Retrouvez plus de citations sur <a href="http://www.citation-ou-proverbe.fr/?pk_campaign=feed&pk_kwd=feed-prefix">www.citation-ou-proverbe.fr</a></p>';

	    $item->setDescription($description);
	    $feed->addItem($item);
	  }
	
    $this->setLayout(false);
	  $this->feed = $feed;
  }
  
  public function executeSitemap(sfWebRequest $request)
  {
  	$page = $request->getParameter('page', 0);
  	$nb = 2500;
  	
    $this->authors = Doctrine::getTable('Author')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit($nb/2)
      ->offset($nb/2*$page)
      ->execute();
    $this->tags = Doctrine::getTable('Tag')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit($nb/2)
      ->offset($nb/2*$page)
      ->execute();
    $this->citations = Doctrine::getTable('Citation')
      ->createQuery('a')
      ->where('is_active = ?', 1)
      ->limit($nb)
      ->offset($nb*$page)
      ->orderBy('last_published_at desc')
      ->execute();
    
    $this->nextpage = $page+1;
    $this->setLayout(false);
    $this->getResponse()->addHttpMeta('content-type', 'text/xml');
  }
}
