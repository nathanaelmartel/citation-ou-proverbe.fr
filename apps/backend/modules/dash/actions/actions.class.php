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
		  	->from('Page')
		  	->where('website = ?', $website)
	  		->limit(1)
	  		->orderBy('created_at ASC');
  		 
  		foreach ($q->execute() as $Page) {
  			echo $Page;
	  		try {
		  		$Scraper = new scraper($Page->url, $Page->id);
		  		 
		  		$string = $Scraper->queryPage($selector[$website], 'nodeValue');
		  		$Option = Doctrine::getTable('Option')->findOneByOptionKey($website);
		  		if ($Option)
		  			$Option->delete();
		  		
		  		$newOption = new Option;
		  		$newOption->option_key = $website;
		  		$newOption->option_value = $string[0];
		  		$newOption->save();
		  		
		  		$strings[$website] = $string[0];
	  		 
	  		} catch (Exception $e) {
		  		$strings[$website] = 'erreur';
	  		}
  		}
  		 
    }
  	
  	$this->strings = $strings;
  }
  
  public function executeOption(sfWebRequest $request) {
    require_once(dirname(__FILE__).'/../../../../../lib/vendor/simplement/scraper.class.php');
  	$this->options = Doctrine::getTable('Option')->findAll();
  }
}
