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
    
    $q = Doctrine_Manager::getInstance()->getCurrentConnection();
    $q->execute('SHOW COLLATION LIKE "utf8%" ;');
    
    require_once(dirname(__FILE__).'/../vendor/simplement/scraper.class.php');
    sfTask::log('==== begin on '.date('r').' ====');
    $begin_time = time();
    $max_time = 50;
    
    $websites = array(
    		'citations', 
    //		'1001-citations',
    //		'citation-et-proverbe',
    //		'evene',
    //		'lexode'
    );
    
    shuffle($websites);
    $limit = ceil(50/count($websites));
    //$limit = 50;
    
    foreach ($websites as $website) {
      if (time() - $begin_time > $max_time) break;
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
      	if (time() - $begin_time > $max_time) break;
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
    			//var_dump($quote);
    			foreach ($quote['tags'] as $tag) {
    				if (TagTable::addTag($tag))
    					sfTask::log('++ tag : '.$tag);
    			}
          if ((array_key_exists('author', $quote)) && (strlen($quote['author']) > 0)) {
        		$quote['author'] = $quote['author'];
          } else {
        		$quote['author'] = 'Anonyme';
          }
          if (in_array($quote['author'], array('Auteur Inconnu', 'Inconnu', 'Artiste Inconnue')))
        		$quote['author'] = 'Anonyme';
          	
    			if (AuthorTable::addAuthor($quote['author']))
    				sfTask::log('++ author : '.$quote['author']);
    			if (CitationTable::addCitation($quote)) {
    				sfTask::log('++ citation : '.$quote['quote']);
    				$new_quote++;
    			}
    			$total_quote++;
    		}
    		 
    		sfTask::log($Page->id.' ('.$Page->website.') ++ '.$new_quote.' ['.$total_quote.']');
    		if ($total_quote == 0)
    			sfTask::log($Page->url);
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
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'source' => $source, 'tags' => array_unique($tags, SORT_LOCALE_STRING));
    }
    
    return $quotes;
  }
  
  function parse_evene($Page) {
  	 
  	$quotes = array();
  	$Scraper = new scraper($Page->url, $Page->id);
  	$html = $Scraper->getPage();
  	$dom = new Zend_Dom_Query($html);
  	
  	
  	$values = $dom->query('.evene-content .block-cdc-citations .txt');
  	 
  	foreach($values as $value) {
  		$item = simplexml_import_dom($value)->asXML();
  		$dom2 = new Zend_Dom_Query($item);
  	  
  		$query_results = $dom2->query('h3');
  		$quote = '';
  		foreach($query_results as $result) {
  			$quote = trim(scraper::utf8($result->nodeValue));
  			$quote = trim(substr($quote, 4, -4));
  		}
  		 
  		$query_results = $dom2->query('h4 span');
  		$author = '';
  		foreach($query_results as $result) {
  			$author = scraper::cleanAuthor(scraper::utf8($result->nodeValue));
  		} 
  		if ($author == '')
  		$query_results = $dom2->query('h4');
  		foreach($query_results as $result) {
  			$author = scraper::cleanAuthor(scraper::utf8($result->nodeValue));
  			$author = str_replace('De ', '', $author);
  			$author = str_replace('[+]', '', $author);
  			$author = trim($author);
  		}
  		if (strlen($author) > 50){
  			sfTask::log('author look too long: '.$author);
  			continue;
  		}
  		 
  		$query_results = $dom2->query('.author a');
  		$source = '';
  		foreach($query_results as $result) {
  			$source_temp = scraper::cleanAuthor(scraper::utf8($result->nodeValue));
  			if ($source_temp != '[+]')
  				$source = $source_temp;
  		}
  		 
  		$query_results = $dom2->query('h3 a');
  		$tags = array();
  		foreach($query_results as $result) {
  			$tag =  scraper::cleanTag(scraper::utf8($result->nodeValue));
  			if ($tag != '#')
  				$tags[] = $tag;
  		}
  		 
  		//sfTask::log('==== '.$quote.' - '.$author.' - '.$source.' - '.json_encode($tags));
  		$quotes[] = array('quote' => $quote, 'author' => $author, 'source' => $source, 'tags' => array_unique($tags, SORT_LOCALE_STRING));
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
    	  $quote = trim(scraper::utf8($result->nodeValue));
    	}
    	
	    $query_results = $dom2->query('legend');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = scraper::cleanAuthor(scraper::utf8($result->nodeValue));
  			$author = trim(str_replace('Auteur inconnu', 'Anonyme', $author));
    	}
    	
	    $query_results = $dom2->query('.legende a');
	    $tags = array();
    	foreach($query_results as $result) {
    		$tag =  scraper::cleanTag(scraper::utf8($result->nodeValue));
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
