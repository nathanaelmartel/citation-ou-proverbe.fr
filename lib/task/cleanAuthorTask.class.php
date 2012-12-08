<?php

class cleanAuthorTask extends sfBaseTask
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
    $this->name             = 'cleanAuthor';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [cleanAuthor|INFO] task does things.
Call it with:

  [php symfony cleanAuthor|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    require_once(dirname(__FILE__).'/../vendor/simplement/scraper.class.php');
    sfTask::log('==== begin on '.date('r').' ====');
    
    for ($i=1; $i<6; $i++) {
	    $q = Doctrine_Query::create()
	    ->select('*')
	    ->from('Author')
	    ->where('slug LIKE ?', '%'.$i)
	    //->limit(100)
	    ->orderBy('name');
	    
	    foreach ($q->execute() as $Author) {
	    	
	    	if (count($Author->Citations) == 0) {
	    		$Author->delete();
	    	} else {
		    	$clean_name = scraper::cleanAuthor($Author->name);
		    	sfTask::log($clean_name.' ('.$Author->id.') ['.count($Author->Citations).']');
		    	
		    	$q = Doctrine_Query::create()
		    	->select('*')
		    	->from('Author')
		    	->where('slug = ?', str_replace('-'.$i, '', $Author->slug))
		    	->orderBy('name');
		    	
		    	foreach ($q->execute() as $other_author) {
		    		if ($other_author->id != $Author->id) {
		    			$this->changeCitationsAuthor($Author, $other_author);
		    		}
		    	}
		    	
			    $Author->name = $clean_name;
			    $Author->save();
	    	}
	    }
    }
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Author')
    ->orderBy('name');
    
    foreach ($q->execute() as $Author) {
	    $clean_name = scraper::cleanAuthor($Author->name);
	    if ($clean_name != $Author->name) {
		    $Author->name = $clean_name;
		    $Author->save();
	    }
	    if (count($Author->Citations) == 0){
    		$Author->delete();
    	}
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  function changeCitationsAuthor($OldAuthor, $NewAuthor) {
  	
    foreach($OldAuthor->Citations as $Citation) {
    	$Citation->author_id = $NewAuthor->id;
    	$Citation->save();
    }
    
    //sfTask::log($OldAuthor->name.' ('.$OldAuthor->id.') -> '.$NewAuthor->name.' ('.$NewAuthor->id.') ('.count($OldAuthor->Citations).')');
  	$OldAuthor->delete();
  	
  }
}
