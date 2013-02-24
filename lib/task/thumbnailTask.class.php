<?php

class thumbnailTask extends sfBaseTask
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
    $this->name             = 'thumbnail';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [thumbnail|INFO] task does things.
Call it with:

  [php symfony thumbnail|INFO]
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
    ->from('Author a')
    ->leftJoin('AuthorDBPedia d')
    ->Where('a.has_thumbnail = false')
    ->andWhere('d.thumbnail is not null')
    ->andWhere('d.author_id = a.id')
    ->offset(rand(0, 10))
    ->limit(100)
    ->orderBy('a.updated_at ASC');
    
    //echo $q->getSqlQuery();echo "\n";die;
    
    foreach ($q->execute() as $Author) {
    	if (time() - $begin_time > $max_time) break;
    	$log = '';
    	
    	foreach ($Author->DBPedia as $item) {
    
		    $filename = sfConfig::get('sf_web_dir').'/portrait/'.$Author->slug; 
		    if (!file_exists($filename))
		    	mkdir($filename);
		    
		    $pathinfo = pathinfo($item->thumbnail);
		    
    		if (copy($item->thumbnail, $filename.'/original.'.$pathinfo['extension'])) {
    			$Author->has_thumbnail = true;
    			$Author->save();
    			$log = ' thumbnail';
    			break;
    		}
    	}
    	 
    	sfTask::log($Author->id.': '.$Author->name.$log);
    }
    
    
    sfTask::log('==== end on '.date('r').' ====');
  }
}
