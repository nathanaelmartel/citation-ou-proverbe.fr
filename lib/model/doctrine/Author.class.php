<?php

/**
 * Author
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    citations-vi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Author extends BaseAuthor
{
	public function getNbCitations() {
		return count($this->Citations);
	}
	
	public function hasDBPedia() {
		return count($this->DBPedia) != 0;
	}
	
	public function getHasDBPedia() {
		return $this->hasDBPedia();
	}
	
	public function hasWikipedia() {
		return count($this->Wikipedia) != 0;
	}
	
	public function getHasWikipedia() {
		return $this->hasWikipedia();
	}
}
