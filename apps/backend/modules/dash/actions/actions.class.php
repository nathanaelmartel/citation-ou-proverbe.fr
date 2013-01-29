<?php

/**
 * dash actions.
 *
 * @package    citations-vi
 * @subpackage dash
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dashActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executePage(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeCitation(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeAuthor(sfWebRequest $request)
  {
  	$this->q = Doctrine_Manager::getInstance()->getCurrentConnection();
  }
  
  public function executeCheckEncoding(sfWebRequest $request)
  {
    require_once(dirname(__FILE__).'/../../../../../lib/vendor/simplement/scraper.class.php');
    
  	$websites = array(
  	  		'citations',
  			  '1001-citations',
  	      'linternaute',
  	      'citation-et-proverbe',
  	      'les-citations',
  	      'evene',
  	  		'lexode'
  	);
  	
  	$selector = array(
  		'citations' => '#theme-en-avant .texte',
	  	'1001-citations' => '.title',
	  	'linternaute' => '.libelle_citation_jour',
	  	'citation-et-proverbe' => 'blockquote',
	  	'les-citations' => '.view-content h1',
	  	'evene' => '.block-themas-items .first .txt',
	  	'lexode' => '.citation'
  	);
  	$strings = array();
  	
  	
  	foreach ($websites as $website) {
  			 
  			$q = Doctrine_Query::create()
  			->select('*')
		  	->from('Page l')
		  	->andWhere('website = ?', $website)
	  		->limit(1)
	  		->orderBy('created_at ASC');
  		 
  		foreach ($q->execute() as $Page) {
	  		try {
		  		$Scraper = new scraper($Page->url, 0, false);
		  		 
		  		$string = $Scraper->queryPage($selector[$website], 'nodeValue');
		  		$strings[$website] = $string[0];
	  		 
	  		} catch (Exception $e) {
	  		}
  		}
  		 
    }
  	
  	
  	foreach ($websites as $website) {
  			 
  			$q = Doctrine_Query::create()
  			->select('*')
		  	->from('Page l')
		  	->andWhere('website = ?', $website)
	  		->limit(1)
	  		->orderBy('created_at ASC');
  		 
  		foreach ($q->execute() as $Page) {
	  		try {
		  		$Scraper = new scraper($Page->url, 0);
		  		 
		  		$string = $Scraper->queryPage($selector[$website], 'nodeValue');
		  		$strings[$website.'-2'] = $string[0];
	  		 
	  		} catch (Exception $e) {
	  		}
  		}
  		 
    }
  	
  	$this->strings = $strings;
  }
}
