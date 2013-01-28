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
    /*		'citations', 
    		'1001-citations', 
    		'linternaute', 
    		'citation-et-proverbe',
    		'les-citations',*/
    		'evene',
    //		'lexode'
    );
    
    shuffle($websites);
    $limit = ceil(50/count($websites));
    //$limit = 50;
    
    foreach ($websites as $website) {
    	sfTask::log('**** '.$website.' '.date('r').' ****');    	
    	
    	$q = Doctrine_Query::create()
    	->select('*')
    	->from('Page l')
    	->where('http_code = 200')
    	->andWhere('website = ?', $website)
    	->andWhere('parsed_date is ?', null)
    	//->offset(rand(0, 50))
    	->limit($limit)
    	->orderBy('downloaded_date ASC');
    	
    	//echo $q->getSqlQuery();echo "\n";die;
    	
    	foreach ($q->execute() as $Page) {
    		$total_quote = 0;
    		$new_quote = 0;
    		 
    		//sfTask::log($Page->id.' ('.$Page->website.')');
    		 
    		switch ($Page->website) {
    			case '1001-citations':
    				$quotes = $this->parse_1001citations($Page);
    				break;
    			case 'citations':
    				$quotes = $this->parse_citations($Page);
    				break;
    			case 'linternaute':
    				$quotes = $this->parse_linternaute($Page);
    				break;
    			case 'citation-et-proverbe':
    				$quotes = $this->parse_citation_et_proverbe($Page);
    				break;
    			case 'les-citations':
    				$quotes = $this->parse_les_citations($Page);
    				break;
    			case 'evene':
    				$quotes = $this->parse_evene($Page);
    				break;
    			case 'lexode':
    				$quotes = $this->parse_lexode($Page);
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
  
  function parse_1001citations($Page) {
  	$quotes = array();
    $Scraper = new scraper($Page->url, $Page->id);
    $html = $Scraper->getPage();
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
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => array_unique($tags));
    }
    
    return $quotes;
  }
  
  function parse_citations($Page) {
  	$quotes = array();
    $Scraper = new scraper($Page->url, $Page->id);
    $html = $Scraper->getPage();
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
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => array_unique($tags));
    }
    
    return $quotes;
  }
  
  function parse_les_citations($Page) {
  	$quotes = array();
  	$Scraper = new scraper($Page->url, $Page->id);
  	$html = $Scraper->getPage();
  	$dom = new Zend_Dom_Query($html);
  	$values = $dom->query('.node-type-citation');
  
  	foreach($values as $value) {
  		$item = simplexml_import_dom($value)->asXML();
  		$dom2 = new Zend_Dom_Query($item);
  	  
  		$query_results = $dom2->query('.field-title h1');
  		$quote = '';
  		foreach($query_results as $result) {
  			$quote = trim(scraper::encodingCorrection($result->nodeValue, 'alpha'), '"');
  		}
  		 
  		$query_results = $dom2->query('.auteur-name h1');
  		$author = '';
  		foreach($query_results as $result) {
  			$author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
  		}
  		 
  		$query_results = $dom2->query('.field-item');
  		$source = '';
  		foreach($query_results as $result) {
  			$source = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
  		}
  		 
  		$query_results = $dom2->query('.field-terms a');
  		$tags = array();
  		foreach($query_results as $result) {
  			$tags[] = scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'alpha'));
  		}
  		 
  		//sfTask::log('==== '.$quote.' - '.$author.' - '.json_encode($tags));
  		$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => array_unique($tags));
  	}
  
  	return $quotes;
  }
  
  function parse_citation_et_proverbe($Page) {
  	$quotes = array();
    $Scraper = new scraper($Page->url, $Page->id);
    $html = $Scraper->getPage();
    $dom = new Zend_Dom_Query($html);
    $values = $dom->query('article');
    
    foreach($values as $value) {
      $item = simplexml_import_dom($value)->asXML();
	    $dom2 = new Zend_Dom_Query($item);
	    
	    $query_results = $dom2->query('blockquote');
	    $quote = '';
    	foreach($query_results as $result) {
    	  $quote = trim(str_replace('#', '', scraper::encodingCorrection($result->nodeValue, 'alpha')));
    	}
    	
	    $query_results = $dom2->query('.author');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
	    $query_results = $dom2->query('.source');
	    $source = '';
    	foreach($query_results as $result) {
    	  $source = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    	}
    	
	    $query_results = $dom2->query('blockquote a');
	    $tags = array();
    	foreach($query_results as $result) {
    		$tag =  scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'alpha'));
    		if ($tag != '#')
    	  	$tags[] = $tag;
    	}
    	
    	//sfTask::log('==== '.$quote.' - '.$author.' - '.json_encode($tags));
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'source' => $source, 'tags' => array_unique($tags));
    }
    
    return $quotes;
  }
  
  function parse_evene($Page) {
  	/* UPDATE `page` SET `downloaded_date`=NOW() WHERE `url` LIKE 'http://www.evene.fr/citations/mot.php?mot=%';
  	   UPDATE `page` SET `downloaded_date`=NOW() WHERE `url` LIKE 'http://www.evene.fr/citations/theme/%';
  	   UPDATE `page` SET `downloaded_date`=NOW() WHERE `url` LIKE 'http://www.evene.fr/citations/%?page=%';
  	 */
  	/*$allowed_hosts = array(
  			'http://www.evene.fr/citations/mot.php?mot=',
  			'http://www.evene.fr/citations/theme/'
  	);
  	foreach ($allowed_hosts as $allowed_host) {
  		if (substr($Page->url, 0, strlen($allowed_host)) == $allowed_host){
  			return array();
  		}
  	}*/
  	 
  	$quotes = array();
  	$Scraper = new scraper($Page->url, $Page->id);
  	$html = $Scraper->getPage();
  	$dom = new Zend_Dom_Query($html);
  	/*$values = $dom->query('.evene-content .block-citations-main');
  	 
  	foreach($values as $value) {
  		$item = simplexml_import_dom($value)->asXML();
  		$dom2 = new Zend_Dom_Query($item);
  	  
  		$query_results = $dom2->query('h1');
  		$quote = '';
  		foreach($query_results as $result) {
  			$quote = trim(scraper::encodingCorrection($result->nodeValue, 'gamma'));
  			$quote = htmlentities($quote);
  			$quote = str_replace('&nbsp;', '', $quote);
  			$quote = str_replace('&laquo;', '', $quote);
  			$quote = str_replace('&raquo;', '', $quote);
  			$quote = html_entity_decode($quote);
  		}
  		 
  		$query_results = $dom2->query('h2 span');
  		$author = '';
  		foreach($query_results as $result) {
  			$author = scraper::cleanAuthor(get_contents_utf8($result->nodeValue));
  		}
  		 
  		$query_results = $dom2->query('.author a');
  		$source = '';
  		foreach($query_results as $result) {
  			$source = scraper::cleanAuthor(get_contents_utf8($result->nodeValue));
  		}
  		 
  		$query_results = $dom2->query('h1 a');
  		$tags = array();
  		foreach($query_results as $result) {
  			$tag =  scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'gamma'));
  			if ($tag != '#')
  				$tags[] = $tag;
  		}
  		 
  		//sfTask::log('==== '.$quote.' - '.$author.' - '.$source.' - '.json_encode($tags));
  		$quotes[] = array('quote' => $quote, 'author' => $author, 'source' => $source, 'tags' => array_unique($tags));
  	}*/
  	
  	
  	$values = $dom->query('.evene-content .block-cdc-citations');
  	 
  	foreach($values as $value) {
  		$item = simplexml_import_dom($value)->asXML();
  		$dom2 = new Zend_Dom_Query($item);
  	  
  		$query_results = $dom2->query('h3');
  		$quote = '';
  		foreach($query_results as $result) {
  			$quote = trim(get_contents_utf8($result->nodeValue));
  			$quote = htmlentities($quote);
  			$quote = str_replace('&nbsp;', '', $quote);
  			$quote = str_replace('&laquo;', '', $quote);
  			$quote = str_replace('&raquo;', '', $quote);
  			$quote = html_entity_decode($quote);
  		}
  		 
  		$query_results = $dom2->query('h4');
  		$author = '';
  		foreach($query_results as $result) {
  			$author = scraper::cleanAuthor(get_contents_utf8($result->nodeValue));
  			$author = str_replace('De ', '', $author);
  			$author = str_replace('[+]', '', $author);
  			$author = trim($author);
  		}
  		 
  		$query_results = $dom2->query('.author a');
  		$source = '';
  		foreach($query_results as $result) {
  			$source_temp = scraper::cleanAuthor(get_contents_utf8($result->nodeValue));
  			if ($source_temp != '[+]')
  				$source = $source_temp;
  		}
  		 
  		$query_results = $dom2->query('h3 a');
  		$tags = array();
  		foreach($query_results as $result) {
  			$tag =  scraper::cleanTag(get_contents_utf8($result->nodeValue));
  			if ($tag != '#')
  				$tags[] = $tag;
  		}
  		 
  		//sfTask::log('==== '.$quote.' - '.$author.' - '.$source.' - '.json_encode($tags));
  		$quotes[] = array('quote' => $quote, 'author' => $author, 'source' => $source, 'tags' => array_unique($tags));
  	}
  
  	return $quotes;
  }
  
  function parse_lexode($Page) {
  	$quotes = array();
    $Scraper = new scraper($Page->url, $Page->id);
    $html = $Scraper->getPage();
    $dom = new Zend_Dom_Query($html);
    $values = $dom->query('fieldset.citation');
  	
    foreach($values as $value) {
      $item = simplexml_import_dom($value)->asXML();
	    $dom2 = new Zend_Dom_Query($item);
	    
	    $query_results = $dom2->query('.nolink');
	    $quote = '';
    	foreach($query_results as $result) {
    	  $quote = trim(scraper::encodingCorrection($result->nodeValue, 'gamma'));
    	}
    	
	    $query_results = $dom2->query('legend');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::encodingCorrection($result->nodeValue, 'gamma'));
  			$author = trim(str_replace('Auteur inconnu', 'Anonyme', $author));
    	}
    	
	    $query_results = $dom2->query('.legende a');
	    $tags = array();
    	foreach($query_results as $result) {
    		$tag =  scraper::cleanTag(scraper::encodingCorrection($result->nodeValue, 'gamma'));
    		if ($tag != '#')
    	  	$tags[] = $tag;
    	}
    	
    	//sfTask::log('==== '.$quote.' - '.$author.' - '.json_encode($tags));
  		$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => array_unique($tags));
    }
    
    return $quotes;
  }
  
  function parse_linternaute($Page) {
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
  		if (substr($Page->url, 0, strlen($allowed_host)) == $allowed_host){
	  		return array();
  		}
  	}
  	
  	$quotes = array();
    $Scraper = new scraper($Page->url, $Page->id);
    $html = $Scraper->getPage();
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
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => array_unique($tags));
    }
    
    return $quotes;
  }
}
