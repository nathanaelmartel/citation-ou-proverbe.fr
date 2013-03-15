<?php

class chekencodingTask extends sfBaseTask
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
    $this->name             = 'checkencoding';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [chekencoding|INFO] task does things.
Call it with:

  [php symfony chekencoding|INFO]
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
    		'linternaute',
    		'citation-et-proverbe',
    		'les-citations',
    		'evene',
    		'lexode'
    );
     
    $selector = array(
    		'citations' => '#theme-en-avant .texte',
    		'1001-citations' => '.title',
    		'linternaute' => '.libelle_citation_jour',
    		'citation-et-proverbe' => 'blockquote',
    		'les-citations' => '.view-content h1',
    		'evene' => '.block-themas-items .first .txt',
    		'lexode' => '.citation'
    );
    $strings = array();
     
     
    foreach ($websites as $website) {
   	  sfTask::log($website);
    
    	$q = Doctrine_Query::create()
    	->select('*')
    	->from('Page')
    	->where('website = ?', $website)
    	->limit(1)
    	->orderBy('created_at ASC');
    		
    	foreach ($q->execute() as $Page) {
    		
    		try {
    			$Scraper = new scraper($Page->url, $Page->id);
    		  
    			$string = $Scraper->queryPage($selector[$website], 'nodeValue');
    			$Option = Doctrine::getTable('Option')->findOneByOptionKey($website);
    			if ($Option)
    				$Option->delete();
    
    			$newOption = new Option;
    			$newOption->option_key = $website;
    			$newOption->option_value = $string[0];
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' alpha';
    			$newOption->option_value = scraper::encodingCorrection($string[0], 'alpha');
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' beta';
    			$newOption->option_value = scraper::encodingCorrection($string[0], 'beta');
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' gamma';
    			$newOption->option_value = scraper::encodingCorrection($string[0], 'gamma');
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' epsilon';
    			$newOption->option_value = scraper::encodingCorrection($string[0], 'epsilon');
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' cleanTag';
    			$newOption->option_value = scraper::cleanTag($string[0]);
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' cleanAuthor';
    			$newOption->option_value = scraper::cleanAuthor($string[0]);
    			$newOption->save();
    
    			$newOption = new Option;
    			$newOption->option_key = $website.' cleanString';
    			$newOption->option_value = scraper::cleanString($string[0]);
    			$newOption->save();
    
    			$strings[$website] = $string[0];
    
    		} catch (Exception $e) {
    			$strings[$website] = 'erreur';
    		}
    	}
    		
    }
    sfTask::log('==== end on '.date('r').' ====');
  }
}
