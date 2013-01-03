<?php

class downloadPagesTask extends sfBaseTask
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
    $this->name             = 'downloadPages';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [downloadPages|INFO] task does things.
Call it with:

  [php symfony downloadPages|INFO]
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
    	->where('downloaded_date is NULL')
    	->andWhere('website = ?', $website)
    	->offset(rand(0, 50))
    	->limit(ceil(50/count($websites)))
    	->orderBy('created_at ASC');
    	
    	//echo $q->getSqlQuery();echo "\n";die;
    	
    	foreach ($q->execute() as $Page) {
    		$Scraper = new scraper;
    	
    		$header = $Scraper->getPageHeader($Page->url);
    		if (is_array($header)) {
    			$Page->http_code = $header['http_code'];
    			$Page->loading_time = $header['total_time'];
    			$Page->save();
    		}
    		$title = $Scraper->queryPage($Page->url, 'title', 'nodeValue');
    		$Page->downloaded_date = new Doctrine_Expression('NOW()');
    		$Page->title = $title[0];
    		$Page->save();
    		 
    		$urls = $Scraper->queryPage($Page->url, 'a', 'href-absolute');
    		$new_urls = 0;
    		foreach ($urls as $url) {
    			if ($this->checkUrl($url)) {
    				$NewPage = new Page;
    				$NewPage->url = $url;
    				$NewPage->website = $Page->website;
    				$NewPage->nb_citations = 0;
    				$NewPage->save();
    	
    				$new_urls++;
    			}
    		}
    	
    		sfTask::log($Page->url.' ['.$Page->http_code.' - '.$Page->loading_time.']  ++ '.$new_urls);
    	}
    }
    
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  protected function checkUrl($url) {
  	$is_ok = false;
  	$allowed_hosts = array(
  			'http://www.evene.fr/citations',
  			'http://www.linternaute.com/citation/',
  			'http://www.1001-citations.com/',
  			'http://www.citations.com/',
  			'http://www.dicocitations.com/',
  			'http://www.lexode.com/citations/',
  			'http://www.les-citations.com/',
  			'http://www.kaakook.fr',
  			'http://www.leproverbe.fr/'
  	);
  	
  	foreach ($allowed_hosts as $allowed_host) {
  		if (substr($url, 0, strlen($allowed_host)) == $allowed_host){
  			$is_ok = true;
  		}
  	}
  	if (!$is_ok)
  		return false;
  	
  	$rs = Doctrine_Query::create()
  	->select('*')
  	->from('Page l')
    ->where('url = ?', $url)
  	->limit(2)
  	->execute();
  	
  	if (count($rs)>0)
  		$is_ok = false;
  		
  	
  	return $is_ok;
  }
}
