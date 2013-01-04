<?php

class wikipedia2Task extends sfBaseTask
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
    $this->name             = 'dbpedia';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [wikipedia2|INFO] task does things.
Call it with:

  [php symfony wikipedia2|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    sfTask::log('==== begin on '.date('r').' ====');
    
    $data = array(
    		'dbpedia_url' => 'dbpedia_url', 
    		'abstract' => 'abstract', 
    		'comment' => 'comment', 
    		'wikipedia_url' => 'wikipedia_url', 
    		'thumbnail' => 'thumbnail',
    		'birth_date' => 'dateDeNaissance',
    		'death_date' => 'dateDeDécès');
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Author l')
    ->where('dbpedia_url = ?', '')
    ->offset(rand(0, 5))
    ->limit(10)
    ->orderBy('dbpedia_at ASC');
     
    foreach ($q->execute() as $Author) {
    	sfTask::log('***** '.$Author->name);
	    
    	foreach ($data as $key => $key_fr) {
    		$response = json_decode($this->request($Author->name, $key_fr), true);
		    if (count($response['results']['bindings'])) {
		    	$value = $response['results']['bindings'][0][$key]['value'];
		    	//sfTask::log($key."\t".$value);
		    	$Author->$key = $value;
		    } else {
		    	$response = json_decode($this->request($Author->name, $key, 'dbpedia.org/sparql'), true);
		    	if (count($response['results']['bindings'])) {
			    	$value = $response['results']['bindings'][0][$key]['value'];
		    		//sfTask::log($key." (en) \t".$value);
		    		//sfTask::log($key);
		    		$Author->$key = $value;
		    	}
		    }
    	}
    	
    	$Author->dbpedia_at = new Doctrine_Expression('NOW()');
    	$Author->save();
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }

	function request($author_name, $query_type = 'dbpedia_url', $base = 'fr.dbpedia.org/sparql'){
		$prefix = "PREFIX dbp: <http://dbpedia.org/resource/>
		  PREFIX dbp2: <http://dbpedia.org/ontology/>
		  PREFIX foaf: <http://xmlns.com/foaf/0.1/>
		  PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>";
		$filter = '';
		
		if ($base == 'dbpedia.org/sparql') {
			$filter = "FILTER langMatches(lang(?".$query_type."), 'fr')";
		}
	  
		$query = array (
			'dbpedia_url' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr.}',
			'abstract' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url dbp2:abstract ?abstract. '.$filter.'}',
			'comment' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url rdfs:comment ?comment. '.$filter.'}',
			'wikipedia_url' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url foaf:isPrimaryTopicOf ?wikipedia_url. }',
			'thumbnail' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url dbp2:thumbnail ?thumbnail. }',
			'birth_date' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url dbp2:birthDate ?birth_date. }',
			'death_date' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url dbp2:deathDate ?death_date. }',
			'dateDeNaissance' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url prop-fr:dateDeNaissance ?birth_date. }',
			'dateDeDécès' => 'SELECT * WHERE { ?dbpedia_url rdf:type foaf:Person. ?dbpedia_url rdfs:label "'.$author_name.'"@fr. ?dbpedia_url prop-fr:dateDeDécès ?death_date. }',
		);
		
		//if ($query_type == 'dateDeNaissance') sfTask::log($query);
		
	  $url = 'http://'.$base.'?query='.urlencode($prefix.$query[$query_type]).'&format=json';
	   
	  $ch= curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 5); 
	  $response = curl_exec($ch);
	  curl_close($ch);
	 
	  return $response;
	}
	
	
}
