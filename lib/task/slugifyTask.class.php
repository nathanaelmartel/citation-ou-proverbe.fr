<?php

class slugifyTask extends sfBaseTask
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
    $this->name             = 'slugify';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [slugify|INFO] task does things.
Call it with:

  [php symfony slugify|INFO]
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
    	//->where('slug = ""')
    	->Where('slug is null')
    	->offset(rand(0, 10))
    	->limit(100)
    	->orderBy('updated_at ASC');
    	 
    	//echo $q->getSqlQuery();echo "\n";die;
    	 
    	foreach ($q->execute() as $Citation) {
    		if (time() - $begin_time > $max_time) break;
    		
    		$Citation->generateSlug();
    		$Citation->save();
    		 
    		sfTask::log($Citation->id.': '.$Citation->slug);
    	}
    	 
    
    sfTask::log('==== end on '.date('r').' ====');
  }
}
