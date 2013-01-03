<?php

class parseTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'parse';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [parse|INFO] task does things.
Call it with:

  [php symfony parse|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    
    require_once(dirname(__FILE__).'/../vendor/simplement/scraper.class.php');
    sfTask::log('==== begin on '.date('r').' ====');
    
    $websites = array(
    		'citations', 
    		'1001-citations', 
    		'linternaute'
    );
    
    foreach ($websites as $website) {
    	sfTask::log('**** '.$website.' '.date('r').' ****');    	
    	
    	$q = Doctrine_Query::create()
    	->select('*')
    	->from('Page l')
    	->where('http_code = ?', '200')
    	->andWhere('website = ?', $website)
    	->andWhere('parsed_date is ?', null)
    	->offset(rand(0, 5))
    	->limit(ceil(40/count($websites)))
    	->orderBy('downloaded_date ASC');
    	
    	foreach ($q->execute() as $Page) {
    		$total_quote = 0;
    		$new_quote = 0;
    		 
    		//sfTask::log($Page->id.' ('.$Page->website.')');
    		 
    		switch ($Page->website) {
    			case '1001-citations':
    				$quotes = $this->parse_1001citations($Page->url);
    				break;
    			case 'citations':
    				$quotes = $this->parse_citations($Page->url);
    				break;
    			case 'linternaute':
    				$quotes = $this->parse_linternaute($Page->url);
    				break;
    		}
    		 
    		$Page->nb_citations = count($quotes);
    		$Page->parsed_date = new Doctrine_Expression('NOW()');
    		$Page->save();
    	
    		foreach ($quotes as $quote) {
    			foreach ($quote['tags'] as $tag) {
    				if (TagTable::addTag($tag))
    					sfTask::log('++ tag : '.$tag);
    			}
    			if (AuthorTable::addAuthor($quote['author']))
    				sfTask::log('++ author : '.$quote['author']);
    			if (CitationTable::addCitation($quote)) {
    				sfTask::log('++ citation : '.$quote['quote']);
    				$new_quote++;
    			}
    			$total_quote++;
    		}
    		 
    		sfTask::log($Page->id.' ('.$Page->website.') ++ '.$new_quote.' ['.$total_quote.']');
    	}
    	
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  function parse_1001citations($url) {
  	$quotes = array();
    $Scraper = new scraper;
    $html = $Scraper->getPage($url);
    $dom = new Zend_Dom_Query($html);
    $values = $dom->query('.entry');
    
    foreach($values as $value) {
      $item = simplexml_import_dom($value)->asXML();
	    $dom2 = new Zend_Dom_Query($item);
	    
	    $query_results = $dom2->query('h2');
	    $quote = '';
    	foreach($query_results as $result) {
    	  $quote = trim(scraper::encodingCorrection($result->nodeValue, 'alpha'), '"');
    	}
    	
	    $query_results = $dom2->query('.author');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
	    $query_results = $dom2->query('.tags a');
	    $tags = array();
    	foreach($query_results as $result) {
    	  $tags[] = scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
    	//sfTask::log($quote.' - '.$author.' - '.json_encode($tags));
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => $tags);
    }
    
    return $quotes;
  }
  
  function parse_citations($url) {
  	$quotes = array();
    $Scraper = new scraper;
    $html = $Scraper->getPage($url);
    $dom = new Zend_Dom_Query($html);
    $values = $dom->query('#contenu-zoom-citation');
    
    foreach($values as $value) {
      $item = simplexml_import_dom($value)->asXML();
	    $dom2 = new Zend_Dom_Query($item);
	    
	    $query_results = $dom2->query('h2');
	    $quote = '';
    	foreach($query_results as $result) {
    	  $quote = trim(scraper::encodingCorrection($result->nodeValue, 'alpha'), '"');
    	}
    	
	    $query_results = $dom2->query('h1');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
	    $query_results = $dom2->query('h2 a');
	    $tags = array();
    	foreach($query_results as $result) {
    	  $tags[] = scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
    	//sfTask::log('==== '.$quote.' - '.$author.' - '.json_encode($tags));
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => $tags);
    }
    
    return $quotes;
  }
  
  function parse_linternaute($url) {
  	$allowed_hosts = array(
  			'http://www.linternaute.com/citation/auteur/',
  			'http://www.linternaute.com/citation/theme/',
  			'http://www.linternaute.com/citation/avis/',
  			'http://www.linternaute.com/citation/meilleures_citations/',
  			'http://www.linternaute.com/citation/plus_commentees/',
  			'http://www.linternaute.com/citation/contenu/',
  			'http://www.linternaute.com/citation/recherche_top/'
  	);
  	foreach ($allowed_hosts as $allowed_host) {
  		if (substr($url, 0, strlen($allowed_host)) == $allowed_host){
	  		return array();
  		}
  	}
  	
  	$quotes = array();
    $Scraper = new scraper;
    $html = $Scraper->getPage($url);
    $dom = new Zend_Dom_Query($html);
    $values = $dom->query('.col_milieu');
    
    foreach($values as $value) {
	    $quote = '';
	    $author = '';
	    $tags = array();
	    
      $item = simplexml_import_dom($value)->asXML();
	    $dom2 = new Zend_Dom_Query($item);
	    
	    $query_results = $dom2->query('h1');
    	foreach($query_results as $result) {
    	  $quote = trim(scraper::encodingCorrection($result->nodeValue, 'alpha'), '"');
    	}
    	
	    $query_results = $dom2->query('.petit_texte a.nom_personnage');
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
	    $query_results = $dom2->query('.petit_texte a');
    	foreach($query_results as $key => $result) {
    		if ($key == 2) {
    	  	$tags[] = scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    		}
    	}
    	
    	//sfTask::log('==== '.$quote.' - '.$author.' - '.json_encode($tags));
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => $tags);
    }
    
    return $quotes;
  }
}
