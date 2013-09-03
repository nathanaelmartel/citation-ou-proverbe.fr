<?php

class findSourcesTask extends sfBaseTask
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
    $this->name             = 'findSources';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [findSources|INFO] task does things.
Call it with:

  [php symfony findSources|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    sfTask::log('==== begin on '.date('r').' ====');
    $begin_time = time();
    $max_time = 50;
    
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Citation c')
    ->Where('source <> ""')
    ->andWhere('source_id is null')
    //->offset(rand(0, 10))
    ->limit(500)
    ->orderBy('updated_at ASC');
    
    //echo $q->getSqlQuery();echo "\n";die;
    
    foreach ($q->execute() as $Citation) {
    	if (time() - $begin_time > $max_time) break;
    	
    	$rs = Doctrine_Query::create()
	    	->select('*')
	    	->from('Source s')
	    	->Where('title = ?', $Citation->source)
	    	->andWhere('author_id = ?', $Citation->author_id)
	    	->execute();
    	
    	if (count($rs) == 0) {
    		
    		$Source = new Source;
    		$Source->title = $Citation->source;
    		$Source->author_id = $Citation->author_id;
    		
    		sfTask::log('++ new source: '.$Source->title);
    		
    	} else {
    		
    		$Source = $rs[0];
    		
    	}
    	
    	$Citation->source_id =  $Source->id;
    	$Citation->save();
    	
    	$Source->save();
    	 
    	sfTask::log($Citation->id.' source : '.$Source->title);
    }
    
    
    sfTask::log('==== end on '.date('r').' ====');
  }
}
