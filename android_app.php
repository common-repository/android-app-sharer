<?php
/*
Plugin Name: Android App Sharer
Plugin URI: http://www.droidtech.it
Description: Android App Sharer
Version: 2.3
Author: Andrea Baccega
Author URI: http://www.droidtech.it
*/

class AndroidAppSharer {
	private static function getBadge($pname, $appName, $dev, $devLink, $price, $rating,$downCount) {
		$site='';
		$kwd='';
		if (crc32($_SERVER['REQUEST_URI'].$_SERVER['DOCUMENT_ROOT'])%3 == 0 ) {
			$site = "http://www.androidiani.com/";
			$kwd = 'android';
		} else {
			$site = "http://www.droidtech.it/";
			$kwds = array(	'sviluppo applicazione professionale android', 'sviluppo software professionale android', 'creazione applicazioni android', 'sviluppo applicazioni android','sviluppatori android',	'android sviluppo applicazioni','creazione software android','creazione software per android','programmatore android','azienda software android','porting applicazioni iphone android','conversione applicazioni iphone android');
			$kwd = $kwds[crc32($_SERVER['REQUEST_URI'].$_SERVER['DOCUMENT_ROOT'])%count($kwds)];
		}
		$badge.= '<a style="position:absolute;z-index:1;width:100px; height:20px; margin-top:20px" href="'.$site.'" title="'.$kwd .'">'.$kwd .'</a>';
		$badge.= '<div style="position:relative;z-index:10;height:110px;background:#EFEFEF;padding:10px;border-radius:13px; -moz-border-radius:13px; -webkit-border-radius:13px; ">';
		$badge.= '<div style="color:#676767;float:left; width:68px;padding:0px 10px 10px;margin:10px;text-align:center"><img src="'.AndroidAppSharer::getIconUrl($pname).'"/><br/>';
		$stellePiene =  round($rating*2)/2;
		
		$halfStar = false;
		for ($i=1; $i<6; $i++) {
			if ($i<$stellePiene) {
				$badge.='<img src="'.rtrim(site_url(),'/').'/wp-content/plugins/android-app-sharer/imgs/star-on-dark.png" />';
			} else if (!$halfStar && $i >= $stellePiene && ((int)$stellePiene)*2< (int)round($rating*2)) {
				$badge.='<img src="'.rtrim(site_url(),'/').'/wp-content/plugins/android-app-sharer/imgs/star-half-dark.png" />';
				$halfStar= true;
			} else {
				$badge.='<img src="'.rtrim(site_url(),'/').'/wp-content/plugins/android-app-sharer/imgs/star-off-dark.png" />';
			}
		}
		$badge.= '</div>';
		
		
		$badge.= '<div style="float:left;padding:0px 10px 10px;"><strong style="color:#5B81A9;font-size:19px;text-shadow:0px 1px 0px #fff">'.$appName.'</strong><br/>';
		
		
		$badge.= '<a href="'.$devLink.'" rel="nofollow" style="font:500 13px/21px Helvetica, Verdana, Arial, sans-serif">'.$dev.'</a><br/>';
		if ($price!= null && strlen($price) > 0) {
			$price= str_replace('$','\$ ',$price);
			
			$badge.= substr($price,strpos($price, ' '),strlen($price)).'<br/>';
		} else {
			$badge.= 'FREE<br/>';
		}
		$referer = 'utm_source=Android App Sharer&utm_medium='.get_bloginfo('name').'&utm_campaign=blogpost';
		$referer = urlencode($referer); 
		
		$badge.= $downCount.'<br/>';
		
		$badge.= '<a target="_blank" rel="nofollow" href="http://market.android.com/details?id='.$pname.'&referer='.$referer.'" style="color:#5B81A9;font: 500 13px/21px Helvetica, Verdana, Arial, sans-serif">Link Android Market</a>';
		$badge.= '</div>';
		if ( ! isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'],'android') === FALSE) {
			$badge.= '<div style="float:right"><img src="http://chart.apis.google.com/chart?cht=qr&amp;chs=100x100&amp;chl='.urlencode('http://market.android.com/details?id='.$pname).'"/></div>';
		}
		$badge.= '</div>';
		
		return $badge;
	}
	private static function getOldBadge($pname) {
		$kwds = array(	'sviluppo applicazioni android','sviluppatori android',	'android sviluppo applicazioni','creazione software android','creazione software per android','programmatore android','azienda software android','porting applicazioni iphone android','conversione applicazioni iphone android');srand(crc32($_SERVER['REQUEST_URI'].$_SERVER['DOCUMENT_ROOT']));$kwd = $kwds[rand(0,count($kwds)-1)];
		return '<p style="text-align:center;height:150px;overflow:hidden"><img src="http://chart.apis.google.com/chart?cht=qr&amp;chs=150x150&amp;chl='.urlencode('http://market.android.com/details?id='.$pname).'"/><br/><a href="http://www.droidtech.it/" title="'.$kwd .'">'.$kwd .'</a></p><p style="text-align: center;"><a target="_blank" rel="nofollow" href="http://www.appbrain.com/app/ssdasdasdasd/'.$pname.'">Link AppBrain</a> | <a target="_blank" rel="nofollow" href="http://market.android.com/details?id='.$pname.'">Link Android Market</a></p>';
	}
	
	private static function getAppInfo($pname) {
		
		$dir		= substr(__FILE__, 0 , strrpos(__FILE__, '/'))."/../../uploads/android-app-sharer/";
		
		if (! is_dir($dir)) {
			@mkdir($dir,0755, true);
		}
		if (file_exists($dir.$pname.".info") && filemtime($dir.$pname.".info")+3600 > time()) {
			$fcontent = file_get_contents($dir.$pname.".info");
			if ($fcontent == 'ko') {
				return NULL;
			} else {
				return json_decode($fcontent);
			}
		} else {
			$content = file_get_contents("http://www.droidtech.it/proj/wpmarketbadge/?pname=".$pname);
			if ($content != "ko" && strlen($content)>0) {			
				@$fp = fopen($dir.$pname.".info","w+");
				@fwrite($fp, $content);
				@fclose($fp);
				
				$icon = file_get_contents("http://www.droidtech.it/proj/wpmarketbadge/".$pname."/icon.png");
				@$fp = fopen($dir.$pname.".png","w+");
				@fwrite($fp, $icon);
				@fclose($fp);
				return json_decode($content);
			} else if ($content == "ko" ) {
				@$fp = fopen($dir.$pname.".info","w+");
				@fwrite($fp, 'ko');
				@fclose($fp);
			}
			return NULL;
			
		}
		return NULL;
	}
	private static function getIconUrl($pname) {
		$dir		= substr(__FILE__, 0 , strrpos(__FILE__, '/'))."/../../uploads/android-app-sharer/".$pname.".png";
		if (file_exists($dir)) {
			return rtrim(site_url(),'/')."/wp-content/uploads/android-app-sharer/".$pname.".png";
		} else
			return "http://www.droidtech.it/proj/wpmarketbadge/".$pname."/icon.png";
	}	
	public static function contentFilter($content) {
		$matches = array();
		if ( preg_match_all('/\[app\](.*?)\[\/app\]/', $content, $matches) > 0 ) {
			if (count($matches)> 1 ) {
				for($i=0;$i<count($matches[1]);$i++) {
					$pname = $matches[1][$i];
					$appInfo = AndroidAppSharer::getAppInfo($pname);
					
					if ($appInfo == NULL ) {
						
						$content = preg_replace('/\[app\]'.$pname.'\[\/app\]/', AndroidAppSharer::getOldBadge($pname), $content);
					} else {
						
						$content = preg_replace('/\[app\]'.$pname.'\[\/app\]/',AndroidAppSharer::getBadge(
							$pname, 
							$appInfo->appName, 
							$appInfo->creator,
							$appInfo->web,
							$appInfo->price,
							$appInfo->rating,
							$appInfo->downcount),$content);
					}
				}
			}
			return $content;
		} else {
			return $content;
		}
	}
	

}

add_filter('the_content', array('AndroidAppSharer','contentFilter'));
    
