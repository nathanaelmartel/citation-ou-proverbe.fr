<?php

class publishTask extends sfBaseTask
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
    $this->name             = 'publish';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [publish|INFO] task does things.
Call it with:

  [php symfony publish|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    
    $q = Doctrine_Query::create()
    ->select('*')
    ->from('Citation c')
    ->where('is_active = ?', 1)
    ->andWhere('LENGTH(quote) < ?', 200)
    ->offset(rand(0, rand(0, 10000)))
    ->limit(10)
    ->orderBy('last_published_at asc');
    
    $citation = $q->fetchOne();
    
    $citation->last_published_at =  date('Y-m-d G:i:s');
    $citation->save();
    sfTask::log('publish '.$citation->id.' at '.date('r').' '.$citation->quote);
    
    //die;
    
    $keys['app_consumer_key'] = 'WvZumEx5FGZK88pt9YrUSg';
    $keys['app_consumer_secret'] = 'erXM5DLtL639jVKvW8Wlybo483wwPileliC6ye2c';
    $keys['oauth_token'] = '1209730194-WmVecrvrbQvApCIgiMVpoJ80aqb3aYdfg9yA6Oh';
    $keys['oauth_token_secret'] = 'JOSYKJV7jkkCDfJli2jsuQRsO2PMnml0RnSORugxj0';
    $json_keys = json_encode($keys);
    
    $this->twitter_statuses_update($citation->getShortQuote(119).' '.$citation->getShortUrl(), $json_keys);
  }
  
  private function twitter_statuses_update($message, $keys)
  {
    global $aktt;
    $aktt = json_decode($keys);
    
    require_once sfConfig::get('sf_lib_dir').'/vendor/twitteroauth/twitteroauth.php';
    if (!defined('AKTT_API_POST_STATUS'))
    	define('AKTT_API_POST_STATUS', 'https://api.twitter.com/1.1/statuses/update.json');
    
    if ($connection = $this->aktt_oauth_connection()) {
      $connection->post(
        AKTT_API_POST_STATUS
        , array(
          'status' => $message
          , 'source' => 'Citations ou Proverbes'
        )
      );
      if (strcmp($connection->http_code, '200') == 0) {
    		sfTask::log('twitter success ');
        return true;
      } else {
    		sfTask::log('twitter failed : '.$connection->http_code);
      }
    }
    return false;
  }

	private function aktt_oauth_connection() {
	  global $aktt;
	  if ( !empty($aktt->app_consumer_key) && !empty($aktt->app_consumer_secret) && !empty($aktt->oauth_token) && !empty($aktt->oauth_token_secret) ) { 
	    $connection = new TwitterOAuth(
	      $aktt->app_consumer_key, 
	      $aktt->app_consumer_secret, 
	      $aktt->oauth_token, 
	      $aktt->oauth_token_secret
	    );
	    $connection->useragent = 'Citation Francophone';
	    return $connection;
	  }
	  else {
	    return false;
	  }
}
}
