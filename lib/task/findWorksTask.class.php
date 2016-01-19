<?php

class findWorksTask extends sfBaseTask
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
    $this->name             = 'findWorks';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [findWorks|INFO] task does things.
Call it with:

  [php symfony findWorks|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    
    require_once(dirname(__FILE__).'/../vendor/simplement/scraper.class.php');
    sfTask::log('==== begin on '.date('r').' ====');
    $begin_time = time();
    $max_time = 50;
    
    if (!file_exists('data/scraper_cache/wikipedia'))
    	mkdir('data/scraper_cache/wikipedia');
    
    $q = Doctrine_Query::create()
	    ->select('*')
	    ->from('AuthorDBPedia d')
	    ->where('dbpedia_url <> ?', '')
	    ->offset(rand(0, 5))
	    ->limit(10)
	    ->orderBy('updated_at DESC');
    
    $authors = $q->execute();
    
    foreach ($authors as $AuthorDBPedia) {
      if (time() - $begin_time > $max_time) break;
      
    	$log = $this->pass($AuthorDBPedia);
    	sfTask::log($AuthorDBPedia->Author->name.' '.$AuthorDBPedia->dbpedia_url.' '.$log);
      
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  function pass($AuthorDBPedia) {
  	
  	$id = str_replace('http://fr.dbpedia.org/resource/', '', $AuthorDBPedia->dbpedia_url);
  	
  	$this->request($id);
  	/*
  	 * on a la liste des œuvre, il faut pour chacune, récupérer le nom…
  	 */
  }

	function request($author, $query_type = 'works', $base = 'dbpedia.org/sparql'){
		/*
		 * 
http://lod.openlinksw.com/sparql
http://dbpedia.org/sparql

PREFIX dbp: <http://dbpedia.org/resource/>
PREFIX dbp2: <http://dbpedia.org/ontology/>

SELECT * WHERE   {
?s       dbp2:author    dbp:Victor_Hugo
}  order by ?s  



		 */
		
		
		
		$prefix = "PREFIX dbp: <http://dbpedia.org/resource/>
		  PREFIX dbp2: <http://dbpedia.org/ontology/>";
		$filter = '';
			  
		$query = array (
			'works' => 'SELECT * WHERE   {?s dbp2:author dbp:'.$author.' }  order by ?s  ',
		);
		
		//if ($query_type == 'label') sfTask::log($prefix.$query[$query_type]);
		
	  $url = 'http://'.$base.'?default-graph-uri=http://dbpedia.org&should-sponge=soft&query='.urlencode($prefix.$query[$query_type]).'&format=json&debug=on&timeout=';
	   
	  $ch= curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 5); 
	  $response = curl_exec($ch);
	  curl_close($ch);
	  
	  var_dump($response);
	  return json_decode($response, true);
	}
}
