<?php

class actionTask extends sfBaseTask
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
    $this->name             = 'action';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [action|INFO] task does things.
Call it with:

  [php symfony action|INFO]
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
    ->from('Author l')
    ->offset(rand(0, 5))
    ->limit(1000)
    ->orderBy('action_at ASC');
     
    foreach ($q->execute() as $Author) {
      if (time() - $begin_time > $max_time) break;
    	
      if (count($Author->Citations) == 0) {
      	sfTask::log($Author->name);
      	$Author->is_active = false;
      }
      
      $Author->action_at = new Doctrine_Expression('NOW()');
	  	$Author->save();
    }
     
    
    sfTask::log('==== end on '.date('r').' ====');
  }
}
