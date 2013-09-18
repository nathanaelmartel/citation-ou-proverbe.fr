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
    //preg_match(' 	^(0?[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$', $Source->url);
    var_dump(preg_match('^((19|20)\d\d[- /.](0[1-9]|1[012])[- /.]0?[1-9]|[12][0-9]|3[01])$', $Source->url));
    
    $rs = $Scraper->queryPage('meta[name="date"]', 'content');
    if (count($rs) > 0) 
    	$date = scraper::cleanString($rs[0]);
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
    	$Source->save();
    }
    
  	return $date;
  }
  
  protected function infoLivre($Source) {
  	return '';
  }
}
