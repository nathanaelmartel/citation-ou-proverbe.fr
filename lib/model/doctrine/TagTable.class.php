<?php

/**
 * TagTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TagTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object TagTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Tag');
    }
    
  	public static function addTag($tag) {
  		
      if (($tag != '') && (strlen($tag)>0))
      {
        $tags = Doctrine::getTable('Tag')->findByName($tag);
        foreach ($tags as $Tag) {
	        if (scraper::toAscii($Tag->name) == scraper::toAscii($tag))
	        {
	        	return false;
	        }
        }
        try {
		      $newTag = new Tag;
		      $newTag->name = $tag;
		      $newTag->is_active = true;
		      if ($newTag->isValid()) {
		      	$newTag->save();
						$newTag->free(true);
		      	return true;
		      }
	      } catch (\PDOException $e) {
	      }
      }
      
      return false;
  	}
}