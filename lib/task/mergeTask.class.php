<?php

class mergeTask extends sfBaseTask
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
    $this->name             = 'merge';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [merge|INFO] task does things.
Call it with:

  [php symfony merge|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    sfTask::log('==== begin on '.date('r').' ====');
    $do_modif = true;

    // SELECT * FROM `author_dbpedia` , `author_wikipedia` WHERE `author_dbpedia`.`wikipedia_url` = `author_wikipedia`.`wikipedia_url` AND `author_dbpedia`.`author_id` <> `author_wikipedia`.`author_id`
    $duplicate = array(
    		14026 => 2064,
    		1185 => 6703,
    		3432 => 12724,
    		10291 => 1492,
    		4085 => 14807,
    		410 => 8435,
    		14227 => 4346,
    		1165 => 5677,
    		236 => 13881,
    		//3249 => 10054, http://fr.wikipedia.org/wiki/Philippe_Aubert
    		5614 => 9292,
    		9350 => 4687,
    		354 => 13149,
    		1754 => 8284,
    		8070 => 6244,
    		356 => 13671,
    		8363 => 366,
    		551 => 13942,
    		948 => 10106,
    		9950 => 5510,
    		12616 => 4750,
    		1974 => 12942,
    		6324 => 8601,
    		234 => 11942,
    		4753 => 8629,
    		1405 => 1719,
    		1458 => 14025,
    		3348 => 14561,
    		592 => 5865,
    		402 => 12267,
    		1537 => 505,
    		873 => 7165,
    		13670 => 3773,
    		1002 => 11315
    );
    // add your code here
    
    foreach ($duplicate as $old => $new) {
    	$old_author = Doctrine_Core::getTable('Author')->findOneById($old);
    	$new_author = Doctrine_Core::getTable('Author')->findOneById($new);
    	if ($old_author) {
	    	sfTask::log($old.' => '.$new);
	    	
	    	// quel nom conserver ?
	    	$final_name = $old_author->name;
	    	foreach ($old_author->DBPedia as $dbpedia) {
	    		$final_name = $dbpedia->name;
	    	}
	    	foreach ($old_author->Wikipedia as $wikipedia) {
	    		$final_name = $wikipedia->name;
	    	}
	    	$final_name = $new_author->name;
	    	foreach ($new_author->DBPedia as $dbpedia) {
	    		$final_name = $dbpedia->name;
	    	}
	    	foreach ($new_author->Wikipedia as $wikipedia) {
	    		$final_name = $wikipedia->name;
	    	}
	    	sfTask::log($old_author->name.', '.$new_author->name.' => '.$final_name);
	    	
	    	
	    	// merge citations
	    	foreach ($old_author->Citations as $citation) {
		    	if ($do_modif) {
			    	$citation->author_id = $new_author->id;
			    	$citation->save();
		    	}
	    	}
	    	sfTask::log(count($old_author->Citations).' citations moved, total : '.count($new_author->Citations));
	    	
	    	// merge dbpedia
	    	foreach ($old_author->DBPedia as $dbpedia) {
		    	if ($do_modif) {
			    	$dbpedia->author_id = $new_author->id;
			    	$dbpedia->save();
		    	}
	    	}
	    	sfTask::log(count($old_author->DBPedia).' dbpedia moved, total : '.count($new_author->DBPedia));
	    	
	    	// merge wikipedia
	    	foreach ($old_author->Wikipedia as $wikipedia) {
		    	if ($do_modif) {
			    	$wikipedia->author_id = $new_author->id;
			    	$wikipedia->save();
		    	}
	    	}
	    	sfTask::log(count($old_author->Wikipedia).' wikipedia moved, total : '.count($new_author->Wikipedia));
	    	
	    	
	    	// remove old
	    	if ($do_modif) {
	    		$old_author->delete();
		    	$new_author->name = $final_name;
		    	$new_author->save();
	    	}
    	} else {
	    	sfTask::log($new_author->name);
	    	sfTask::log(count($new_author->Citations).' citations');
	    	sfTask::log(count($new_author->DBPedia).' dbpedia');
	    	sfTask::log(count($new_author->Wikipedia).' wikipedia');
    	}
    	
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }
}
