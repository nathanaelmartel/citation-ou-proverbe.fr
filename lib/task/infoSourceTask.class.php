<?php

class infoSourceTask extends sfBaseTask
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
    $this->name             = 'infoSource';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [infoSource|INFO] task does things.
Call it with:

  [php symfony infoSource|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    require_once(dirname(__FILE__).'/../vendor/simplement/scraper.class.php');
    if (!file_exists('data/scraper_cache/wikipedia'))
    	mkdir('data/scraper_cache/wikipedia');
    sfTask::log('==== begin on '.date('r').' ====');
    $begin_time = time();
    $max_time = 50;
    
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Source c')
    ->where('url <> ""')
    ->offset(rand(0, 10))
    ->limit(500)
    ->orderBy('updated_at ASC');
    
    //echo $q->getSqlQuery();echo "\n";die;
    
    foreach ($q->execute() as $Source) {
    	if (time() - $begin_time > $max_time) break;
    	 
    	if (substr_count($Source->title, 'http') > 0) {
    		$Source->is_link = true;
    		$Source->url = $Source->title;
    		$Source->title = '';
    	}
    	$Source->save();
    	
    	if ($Source->is_link) {
    		$log = $this->infoLink($Source);
    	} else {
    		$log = $this->infoLivre($Source);
    	}
    	 
    
    	sfTask::log($Source->id.' - '.$Source->title.' '.$log);
    }
    
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  protected function infoLink($Source) {
    
    $Scraper = new scraper($Source->url, $Source->id);
    $title = $Scraper->queryPage('title', 'nodeValue');
    if (count($title) > 0) {
    	$Source->title = $title[0];
    }
    
    $url_info = parse_url($Source->url);
    $Source->editor = $url_info['host'];
    
    $date = '';
    
    // in url (wordpress)
    $pattern = '^(((19|20)[0-9][0-9])[-/.](0[1-9]|1[012])[-/.](0[1-9]|[12][0-9]|3[01]))^';
    if (preg_match($pattern, $Source->url, $matches)) {
    	$date = $matches[0];
    }
    if ($date == '') {
	    $rs = $Scraper->queryPage('meta[name="date"]', 'content');
	    if (count($rs) > 0) 
	    	$date = scraper::cleanString($rs[0]);
    }
    if ($date == '') {
    	$rs = $Scraper->queryPage('meta[name*="date"]', 'content');
	    if (count($rs) > 0) 
	    	$date = scraper::cleanString($rs[0]);
    }
    if ($date == '') {
    	$rs = $Scraper->queryPage('.metadata span', 'title'); // twitter
	    if (count($rs) > 0) 
	    	$date = scraper::cleanString($rs[0]);
    }
    if ($date == '') {
    	$rs = $Scraper->queryPage('#eow-date', 'nodeValue'); // youtube
	    if (count($rs) > 0) 
	    	$date = scraper::cleanString($rs[0]);
    }
    if ($date == '') {
    	$rs = $Scraper->queryPage('.date', 'nodeValue'); // la croix
	    if (count($rs) > 0) 
	    	$date = scraper::cleanString($rs[0]);
    }
    
    if ($date != '') {
    	$Source->date = $date;
    }
    
    $description = '';
    $rs = $Scraper->queryPage('meta[name="description"]', 'content');
    if (count($rs) > 0) 
    	$description = scraper::cleanString($rs[0]);
    if ($description == '') {
    	$rs = $Scraper->queryPage('meta[name*="description"]', 'content');
	    if (count($rs) > 0) 
	    	$description = scraper::cleanString($rs[0]);
    }
    
    if ($description != '') {
    	$Source->description = $description;
    }
    
    $Source->save();
  	return '';
  }
  
  protected function infoLivre($Source) {
  	
  	$this->infoLivreDbPedia($Source);
  	
  	return '';
  }
  
  protected function infoLivreDbPedia($Source) {
  	$response = $this->request($Author->name, $key_fr);
  	
  	return '';
  }

	function request($work_name, $query_type = 'dbpedia_url', $base = 'fr.dbpedia.org/sparql'){
		$prefix = "PREFIX dbp: <http://dbpedia.org/resource/>
		  PREFIX dbp2: <http://dbpedia.org/ontology/>
		  PREFIX foaf: <http://xmlns.com/foaf/0.1/>
		  PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>  ";
		$filter = '';
		
		if ($base == 'dbpedia.org/sparql') {
			$filter = "FILTER langMatches(lang(?".$query_type."), 'fr')";
		}
	  
		$query = array (
			'dbpedia_url' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr.}',
			'name' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url foaf:name ?name. '.$filter.'}',
			'abstract' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:abstract ?abstract. '.$filter.'}',
			'comment' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url rdfs:comment ?comment. '.$filter.'}',
			'wikipedia_url' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url foaf:isPrimaryTopicOf ?wikipedia_url. }',
			'thumbnail' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:thumbnail ?thumbnail. }',
			'birth_date' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:birthDate ?birth_date. }',
			'death_date' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:deathDate ?death_date. }',
			'dateDeNaissance' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:dateDeNaissance ?birth_date. }',
			'dateDeDécès' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:dateDeDécès ?death_date. }',
			'birth_place' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:birthPlace ?birth_place. }',
			'death_place' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:deathPlace ?death_place. }',
			'lieuDeNaissance' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:lieuDeNaissance ?birth_place. }',
			'lieuDeDécès' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:lieuDeDécès ?death_place. }',
			'occupation' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:occupation ?occupation. }',
			'activité' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:activité ?occupation. }',
			'notableworks' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url dbp2:notableworks ?notableworks. }',
			'œuvre' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:name "'.$author_name.'"@fr. ?dbpedia_url prop-fr:œuvre ?notableworks. }',
				
			'label' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url foaf:isPrimaryTopicOf "'.$author_name.'"@fr. }',
		);
		
		// http://fr.dbpedia.org/sparql
		// SELECT * WHERE { ?dbpedia_url rdf:type dbpedia-owl:Work. ?dbpedia_url foaf:name "Les Misérables"@fr. ?dbpedia_url dbpedia-owl:author ?author. }
		
		//if ($query_type == 'label') sfTask::log($prefix.$query[$query_type]);
		
	  $url = 'http://'.$base.'?query='.urlencode($prefix.$query[$query_type]).'&format=json';
	   
	  $ch= curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 5); 
	  $response = curl_exec($ch);
	  curl_close($ch);
	 
	  return json_decode($response, true);
	}
}
