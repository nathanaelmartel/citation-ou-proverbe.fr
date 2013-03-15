<?php

class wikipediaTask extends sfBaseTask
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
    $this->name             = 'wikipedia';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [wikipedia|INFO] task does things.
Call it with:

  [php symfony wikipedia|INFO]
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
    ->from('Author a')
    ->offset(rand(0, 50))
    ->limit(100)
   	->orderBy('wikipedia_at ASC');;
    
    $authors = $q->execute();
    
    foreach ($authors as $Author) {
      if (time() - $begin_time > $max_time) break;
    	if (!$Author->hasWikipedia()) {
    		$log = $this->pass($Author);
    		sfTask::log($Author->name.$log);
    	}
      
	  	$Author->wikipedia_at = new Doctrine_Expression('NOW()');
	  	$Author->save();
    }
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Author a')
    ->offset(rand(0, 50))
    ->limit(100)
   	->orderBy('wikipedia_at ASC');;
    
    $authors = $q->execute();
    
    foreach ($authors as $Author) {
      if (time() - $begin_time > $max_time) break;
    	if (!$Author->hasWikipedia()) {
    		$log = $this->pass($Author);
    		sfTask::log($Author->name.$log);
    	}
      
	  	$Author->wikipedia_at = new Doctrine_Expression('NOW()');
	  	$Author->save();
    }
    
    sfTask::log('==== end on '.date('r').' ====');
  }
  
  public function pass($Author)
  {
      $file = 'data/scraper_cache/wikipedia/'.$Author->id.'.html';
      $file_thumb = 'data/scraper_cache/wikipedia/'.$Author->id.'-thumb.html';
      $log = '';
      $is_merged = false;
      
      if ($url = $this->searchWikipedia($Author->name)) {
		  	$AuthorWikipedia = new AuthorWikipedia;
		  	$AuthorWikipedia->author_id = $Author->id;
		  	$AuthorWikipedia->wikipedia_url = $url;
        $log = ' url, ';
        
        $this->retrievePage($url, $file);
        if (file_exists($file)) {
	        $fp = fopen($file, "r");
	        $html = fread($fp, filesize($file));
	        fclose($fp);
	          
	        $html = str_replace(
	           array('&#13;', '&#amp;', '&#039;', '’', '&#160;'),
	           array(" ", '&', "'", "'", ' '),
	           $html);
	          	
	        $AuthorWikipedia->name = $this->retrieveName($html);
	        $AuthorWikipedia->abstract = $this->retrieveBio($html);
	        $AuthorWikipedia->thumbnail = $this->retrievePhoto($html, $url, $file_thumb);
      	}
		  	$AuthorWikipedia->save();
      }
      
      return $log;
  }
  
  public function searchWikipedia($name)
  {
    
    // http://fr.wikipedia.org/w/api.php?action=opensearch&search=Aaron+Eckhart&format=json
    $url = 'http://fr.wikipedia.org/w/api.php?action=opensearch&search='.str_replace(' ', '+', $name).'&format=json';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'citation-et-proverbe.fr , nathanael@fam-martel.eu'); 
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);

    //echo $url."\n".$output."\n";
    $response = json_decode($output);
    if (count($response[1]) == 1)
      return 'http://fr.wikipedia.org/wiki/'.str_replace(' ', '_', $response[1][0]);
    else {
    	if (count($response[1]) > 1) {
    		$names = '';
    		foreach ($response[1] as $item) {
    			$names .= $item.', ';
    		}
    		sfTask::log('homonymes in wikipedia ('.$name.'):'.$names);
    	}
      return false;
    }
  }
  
  public function retrievePage($url, $file) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'citation-et-proverbe.fr , nathanael@fam-martel.eu'); 
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5); 
    $output = curl_exec($ch);
    curl_close($ch);

    $fp = fopen($file, "w");
    fwrite($fp, $output);
    fclose($fp);
    
    return $output;
  }
  
  public function retrieveBio($html) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('#mw-content-text>p');
    
    foreach ($results as $result) {
        return utf8_decode(strip_tags($result->nodeValue));
    }
  }
  
  public function retrieveName($html) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('.firstHeading');
    
    foreach ($results as $result) {
        return utf8_decode(strip_tags($result->nodeValue));
    }
  }
  
  public function retrievePhoto($html, $base_url, $file_thumb) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('.infobox_v3 .thumbinner a.image');
    $has_photo = false;
    
    foreach ($results as $result) {
      $image_page = $result->getAttribute('href');
    	$has_photo = true;
    }
    
    if (!$has_photo)
    	return null;
    
    $image_url = scraper::absoluteUrl($image_page, $base_url);
    
    $this->retrievePage($image_url, $file_thumb);
    if (file_exists($file_thumb)) {
	    $fp = fopen($file_thumb, "r");
	    $html = fread($fp, filesize($file_thumb));
	    fclose($fp);
	          
	    $html = str_replace(
	      array('&#13;', '&#amp;', '&#039;', '’', '&#160;'),
	      array(" ", '&', "'", "'", ' '),
	      $html);
    	
	    $dom = new Zend_Dom_Query($html);
    	$results = $dom->query('.fullMedia a');
	    foreach ($results as $result) {
	      return scraper::absoluteUrl($result->getAttribute('href'), $image_url);
	    }
    }

    return '';
  }
  
  public function mergeAuthor($newAuthor, $oldAuthor) {
  	return true;

  	if ($newAuthor->id == $oldAuthor->id) {
  		return false;
  	}
  	
  	echo "\n";
    foreach ($oldAuthor->Citations as $Citation) {
    	$Citation->Author = $newAuthor;
    	$Citation->save();
    	echo '.';
    }
  	echo "\n";
		sfTask::log('merge author: '.$oldAuthor->id.' -> '.$newAuthor->id.' ['.$oldAuthor->name.' -> '.$newAuthor->name.']');
		$oldAuthor->delete();
		$oldAuthor->save();
    
  	return true;
  }
}
