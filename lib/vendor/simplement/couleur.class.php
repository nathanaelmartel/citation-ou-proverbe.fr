<?php


/**
 * couleur
 *
 * @package    http://nathanael.fam-martel.eu
 * @subpackage couleur
 * @author     Nathanaël Martel <nathanael@fam-martel.eu>
 * http://www.easyrgb.com/index.php?X=MATH&H=21#text21
 */
class couleur
{
	/*
	 * HSL from 0 to 1
	 * RGB results from 0 to 255
	 */
	public static function hsl_rgb($H, $S, $L) {
		if ( $S == 0 ) 
		{
			$R = $L * 255;
			$G = $L * 255;
			$B = $L * 255;
		}
		else
		{
			if ( $L < 0.5 ) $var_2 = $L * ( 1 + $S );
			else           	$var_2 = ( $L + $S ) - ( $S * $L );
		
			$var_1 = 2 * $L - $var_2;
		
			$R = 255 * couleur::hue_rgb( $var_1, $var_2, $H + ( 1 / 3 ) );
			$G = 255 * couleur::hue_rgb( $var_1, $var_2, $H );
			$B = 255 * couleur::hue_rgb( $var_1, $var_2, $H - ( 1 / 3 ) );
		}
		
		return array(floor($R), floor($G), floor($B));
	}
	
	public static function hue_rgb($v1, $v2, $vH) {
		if ($vH < 0) $vH += 1;
		if ($vH > 1) $vH -= 1;
	  if ( ( 6 * $vH ) < 1 ) return $v1 + ( $v2 - $v1 ) * 6 * $vH;
	  if ( ( 2 * $vH ) < 1 ) return $v2;
	  if ( ( 3 * $vH ) < 2 ) return $v1 + ( $v2 - $v1 ) * ( ( 2 / 3 ) - $vH ) * 6;
	}
}

