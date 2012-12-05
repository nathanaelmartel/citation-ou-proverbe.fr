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
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Page l')
    //->where('http_code = ?', '200')
    ->andWhere('website = ?', '1001-citations')
    //->andWhere('nb_citations = ?', 0)
    ->limit(10)
    ->orderBy('parsed_date DESC');
    
    foreach ($q->execute() as $Page) {
    	$total_quote = 0;
    	$new_quote = 0;
    	
    	switch ($Page->website) {
    		case '1001-citations':
    			$quotes = $this->parse_1001citations($Page->url);
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
    	
    	sfTask::log($Page->url.' ++ '.$new_quote.' ['.$total_quote.']');
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
    	  $quote = $result->nodeValue;
    	}
    	
	    $query_results = $dom2->query('.author a');
	    $author = '';
    	foreach($query_results as $result) {
    	  $author = $result->nodeValue;
    	}
    	
	    $query_results = $dom2->query('.tags a');
	    $tags = array();
    	foreach($query_results as $result) {
    	  $tags[] = $result->nodeValue;
    	}
    	
    	//sfTask::log($quote.' - '.$author.' - '.json_encode($tags));
    	$quotes[] = array('quote' => $quote, 'author' => $author, 'tags' => $tags);
    }
    
    return $quotes;
  }
}
