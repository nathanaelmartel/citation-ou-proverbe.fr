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
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Author a')
    ->where('wikipedia_url IS NULL')
    ->offset(rand(0, 5))
    ->limit(50);
    
    $authors = $q->execute();
    
    foreach ($authors as $author) {
      $file = "data/scraper_cache/wikipedia/$author->slug.html";
      $log = '';
      $is_merged = false;
      
      if ($author->wikipedia_url == null) {
        if ($url = $this->searchWikipedia($author->name)) {
          
          $author->wikipedia_url = $url;
          $author->save();
          $log = 'url, ';
          
          
          // l'url est déja renseigné pour un autre auteur -> merge
          $authors = Doctrine::getTable('Author')->findByWikipediaUrl($url);
          if (count($authors) == 1)
          {
          	$is_merged = $this->mergeAuthor($authors[0], $author);
          } else if (count($authors) > 1) {
    				sfTask::log('author: duplicate url '.$author->name.' '.count($authors));
          }
          
          // l'url n'est pas trouvé dans la base, on chercre le nom (de wikipedia), si on le trouve -> merge
      		if (!$is_merged) {
	          $html = $this->retrievePage($url, $file);
	          if (file_exists($file)) {
	          	$fp = fopen($file, "r");
	          	$html = fread($fp, filesize($file));
	          	fclose($fp);
	          
	          	$html = str_replace(
	          			array('&#13;', '&#amp;', '&#039;', '’', '&#160;'),
	          			array(" ", '&', "'", "'", ' '),
	          			$html);
	          
	          	$name = $this->retrieveName($html);
		      		if ($name != '') {
		      			if ($author->name != $name) {
				          $log .= ' name updated ('.$author->name.' -> '.$name.') ';
				          $author->name = $name;
				          $author->save();
		      			}
		      		}
	          	
		          $authors = Doctrine::getTable('Author')->findByName($name);
		          if (count($authors) == 1)
		          {
		          	$is_merged = $this->mergeAuthor($authors[0], $author);
		          	$authors[0]->name = $name;
		          	$authors[0]->save();
		          } else if (count($authors) > 1) {
		    				sfTask::log('author: duplicate name '.$author->name.' '.count($authors));
		          }
	          }
      		}
      		
      		// ni l'url ni le nom ne sont trouvé -> on met à jour le nom
      		if ((!$is_merged) && ($name != '')) {
      			if ($author->name != $name) {
		          $log .= ' name updated ('.$author->name.' -> '.$name.') ';
		          $author->name = $name;
		          $author->save();
      			}
      		}
          
          
        } else {
          $author->wikipedia_url = '';
          $author->save();
        }
      }
      
    	sfTask::log($author->name.' '.$log);
    }
    
    sfTask::log('==== end on '.date('r').' ====');
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

    //echo $output;
    $response = json_decode($output);
    if (count($response[1]) > 0)
      return 'http://fr.wikipedia.org/wiki/'.str_replace(' ', '_', $response[1][0]);
    else {
      return false;
    }
  }
  
  public function retrievePage($url, $file) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'citation-et-proverbe.fr , nathanael@fam-martel.eu'); 
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
        return strip_tags($result->nodeValue);
    }
  }
  
  public function retrieveName($html) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('.firstHeading');
    
    foreach ($results as $result) {
        return utf8_decode(strip_tags($result->nodeValue));
    }
  }
  
  public function retrievePhoto($html) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('.infobox_v3 .thumbinner a.image img');
    
    foreach ($results as $result) {
        return $result->getAttribute('src');
    }
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
