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
    ->offset(0)
    ->limit(5);
    
    $authors = $q->execute();
    
    foreach ($authors as $author) {
      $file = "data/scraper_cache/wikipedia/$author->slug.html";
      $log = '';
      
      if ($author->wikipedia_url == null) {
        if ($url = $this->searchWikipedia($author->name)) {
          
          $author->wikipedia_url = $url;
          $author->save();
          $log = 'url, ';
          
          $html = $this->retrievePage($url, $file);
        } else {
          $author->wikipedia_url = '';
          $author->save();
        }
      }
      
      if (file_exists($file)) {
        $fp = fopen($file, "r");
        $html = fread($fp, filesize($file));
        fclose($fp);
        
        $html = str_replace(
        		array('&#13;', '&#amp;', '&#039;', '’', '&#160;'),
        		array(" ", '&', "'", "'", ' '),
        		$html);
        
        $bio = $this->retrieveBio($html);
        $photo = $this->retrievePhoto($html);
        /*$bio = utf8_encode(htmlentities($bio, ENT_COMPAT, 'UTF-8'));/*
    $bio = str_replace(
      array('&#13;', '&#amp;', '&#039;', '’', '&#160;'), 
      array(" ", '&', "'", "'", ' '),
      utf8_decode($bio));*/
       
        $author->wikipedia_bio = utf8_decode($bio);
        $log .= 'bio, ';
        if ($photo != '') {
        	$author->wikipedia_photo = 'http:'.$photo;
        $log .= 'photo.';
        }
        $author->save();
      }
        
    	sfTask::log('author: '.$author->name.' '.$log);
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
  
  public function retrievePhoto($html) {
    $dom = new Zend_Dom_Query($html);
    $results = $dom->query('.infobox_v3 .thumbinner a.image img');
    
    foreach ($results as $result) {
        return $result->getAttribute('src');
    }
  }
}
