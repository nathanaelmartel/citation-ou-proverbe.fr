<?php

/**
 * Citation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    citations-vi
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7691 2011-02-04 15:43:29Z jwage $
 */
class Citation extends BaseCitation
{
	public function addTags($tags) {
		array_unique($tags, SORT_LOCALE_STRING);
		
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$this->addTag($tag);
			}
		}
	}
	
	public function addTag($tag) {
		$Tag = Doctrine::getTable('Tag')->findOneByName($tag);
		
		foreach ($this->Tags as $Linked_Tag) {
	    if ($Linked_Tag->id == $Tag->id) {
				return false;
			}
		}
		
		try {
			$TagCitation = new TagCitation;
			$TagCitation->tag_id = $Tag->id;
			$TagCitation->citation_id = $this->id;
			if ($TagCitation->isValid()) {
				$TagCitation->save();
				$TagCitation->free(true);
		  	return true;
			}
	  } catch (\PDOException $e) {
	  }
		
		return false;
	}
	
	public function generateSlug() {
		$slug = $this->id;
		
		foreach ($this->Tags as $Tag) {
			$slug .= '-'.$Tag->slug;
		}
		
		if (strlen($slug) == 0) {
			$slug = $this->id.'-'.$hash;
		}
		
		if (strlen($slug) == 0) {
			$slug = $this->id;
		}
		
		$this->slug = $slug;
		$this->save();
		
		return $this->slug;
	}
	
	public function generateColor() {
			include_once(sfConfig::get('sf_web_dir').'/../lib/vendor/simplement/couleur.class.php');
			
			$hue = rand(0, 100)/100;
			
			$rgb = couleur::hsl_rgb($hue, .8, .8);
			$this->color = json_encode($rgb);
			
			$rgb = couleur::hsl_rgb($hue, .5, .25);
			$this->text_color = json_encode($rgb);
			
			$this->save();
	}
	
	public function getRGBColor() {
		if (!$this->color)
			$this->generateColor();
		
		return json_decode($this->color);
	}
	
	public function getRGBColorHex() {
		if (!$this->color)
			$this->generateColor();
		
		include_once(sfConfig::get('sf_web_dir').'/../lib/vendor/simplement/couleur.class.php');
		$rgb = json_decode($this->color);
		$rgb_string = couleur::rgb_in_hex($rgb[0], $rgb[1], $rgb[2]);
		
		return $rgb_string;
	}
	
	public function getTextRGBColor() {
		if (!$this->text_color)
			$this->generateColor();
		
		return json_decode($this->text_color);
	}
	
	public function getTextRGBColorHex() {
		if (!$this->text_color)
			$this->generateColor();
		
		include_once(sfConfig::get('sf_web_dir').'/../lib/vendor/simplement/couleur.class.php');
		$rgb = json_decode($this->text_color);
		$rgb_string = couleur::rgb_in_hex($rgb[0], $rgb[1], $rgb[2]);
		
		return $rgb_string;
	}
	
	public function getShortQuote($limit = 50) {
		if (strlen($this->quote) <= $limit)
			return $this->quote;
		
		$short_quote = substr($this->quote, 0, $limit);
		
		return substr($short_quote, 0, strrpos($short_quote, ' ', $limit)+1 ).'...';
	}
	
	public function getShortUrl($traker = '?pk_campaign=twitter&pk_kwd=twitter') {
		return 'http://citation-ou-proverbe.fr/c/'.$this->id.$traker;
	}
}
