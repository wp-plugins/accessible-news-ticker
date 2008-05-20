<?php
/*
Plugin Name: Accessible RSS News Ticker
Plugin URI: http://pixline.net/wordpress-plugins/accessible-rss-news-ticker-widget/
Description: Display latest posts or RSS news in an accessible/unobtrusive scroll box. Based on Chris Heilmann's <a href="http://onlinetools.org/tools/domnews/">DOMnews 1.0</a>.
Author: Pixline
Version: 0.3.1
Author URI: http://pixline.net/

ANT Plugin (C) 2007 Paolo Tresso / Pixline - http://pixline.net/
DOMnews 1.0 (C) Chris Heilmann - http://onlinetools.org/tools/domnews/)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.


*/

$parts = pathinfo(__FILE__);
define("CACHE_PATH",str_replace("plugins/accessible-news-ticker","cache-feed",$parts['dirname']));

function widget_ant_install(){
if(!is_dir(CACHE_PATH) && !is_writeable(CACHE_PATH)){ mkdir(CACHE_PATH, 0777); }
$ant_defaults = array("title"=>"Latest News", "howmany"=>5, "content"=>"posts", "category"=>"", "feedurl"=> "");
add_option('widget_ant_options',$ant_defauls);
}

register_activation_hook(__FILE__, 'widget_ant_install');
#include_once(get_bloginfo('url')."/wp-content/plugins/accessible-news-ticker/includes/simplepie.inc");
include_once("includes/simplepie.inc");

function ant_trim_sentence($string, $num){
//taglia frase e aggiunge ...
   $done = 0;
   $letters = 0;
   $sentence = '';

   $words = explode(" ", trim($string));
   $totalwords = count($words);
   for($i = 0; $i < $totalwords; $i++) {
       $word_array = preg_split('//', $words[$i], -1, PREG_SPLIT_NO_EMPTY);
       $letters = $letters + count($word_array);
      
       if (($letters > $num) && ($done == 0)) {
           $sentence = trim($sentence) . "...";
           $done = 1;
       }
       if ($done == 0) {
           $sentence .= $words[$i] . " ";
       }
   }
   return ($sentence);
}

function widget_ant_init() { 
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) 
        return;

function widget_ant_headscript(){
		echo "
		<link rel='stylesheet' type='text/css' media='screen' href='".get_bloginfo('url')."/wp-content/plugins/accessible-news-ticker/includes/domnews.css' />
		";
        $options = get_option('widget_ant_options'); 
#        $minheight = empty($options['height']) ? '2em' : $options['height']; 	// deprecated/obsolete
		$speed = empty($options['speed']) ? 'slow' : $options['speed'];
		?>
<?php
}

function widget_ant_footscript(){
?>
<!-- Accessible News Ticker wordpress plugin 0.3 by Pixline - http://pixline.net -->
<script type='text/javascript'>
var dn_startpos=0; 				// start position of the first item
var dn_endpos=-600; 			// end of the 'cart'. more items = higher number
var dn_speed=40;				// higher number = slower scroller 
var dn_newsID='accessible-news-ticker';			
var dn_classAdd='hasJS';		
var dn_stopMessage='Stop scroller';	
var dn_paraID='DOMnewsstopper';
</script>
<?php
wp_register_script( 'ant-domnews', '/wp-content/plugins/accessible-news-ticker/includes/domnews.js');
wp_enqueue_script('ant-domnews');
wp_print_scripts();
}

function widget_ant($args) { 
global $cat;
        extract($args); 
        $options = get_option('widget_ant_options'); 
		$title = empty($options['title']) ? 'Latest News' : $options['title']; 
        $howmany = empty($options['howmany']) ? 5 : $options['howmany']; 
        $kind = empty($options['content']) ? 'posts' : $options['content']; 
        $antcat = empty($options['category']) ? '' : $options['category']; 
        $url = empty($options['feedurl']) ? '' : $options['feedurl']; 

        echo $before_widget; 
        echo $before_title . "<div class='ant-head'>" . $title ."</div>". $after_title; 

switch($kind):
	case 'posts':
#	if(isset($cat) && $cat != 0) $menocat = "&exclude=".$cat; else $menocat = "";	// if global post list, but inside cat, exclude double posts
	$news = get_posts("numberposts=".$howmany."&category=".$antcat.$menocat);
	echo "<div id='accessible-news-ticker'>";
		echo "<ul>";
			foreach($news as $new):
			$split = explode(" ",$new->post_date);
			$datasplit = explode("-",$split[0]);
			$data = $datasplit[2]."/".$datasplit[1];
				echo "<li><a href='".$new->guid."'>".$new->post_title."</a><br/>"
				."<small>(".$data.") ".ant_trim_sentence(strip_tags($new->post_content),80)."</small>"
				."</li>";
			endforeach;
		echo "</ul>";
		echo "</div>";
	break;
		
	case 'rss':
	$feed = new SimplePie();
	if ($url){
		$feed->set_feed_url($url);
		$feed->set_cache_location(CACHE_PATH);
		$feed->init();
	}
	$feed->handle_content_type();
	?>
	<div id="accessible-news-ticker">
		<ul>
		<?php if ($feed->data): ?>
			<?php $items = $feed->get_items(0,$howmany);
			foreach($items as $item): ?>
				<li><a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a><br/>
				(<?php echo $item->get_date('d/m'); ?>) 
				<?php echo ant_trim_sentence(strip_tags($item->get_content()),80); ?></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>	
	<?php
	break;
endswitch;

echo $after_widget; 
} 


    function widget_ant_control() { 
		$selected = "";
        $options = get_option('widget_ant_options'); 

        if ( $_POST['ant-submit'] ) { 
            $newoptions['title'] = strip_tags(stripslashes($_POST['ant-title'])); 
            $newoptions['howmany'] = strip_tags(stripslashes($_POST['ant-howmany'])); 
			$newoptions['content'] = strip_tags(stripslashes($_POST['ant-content']));
			$newoptions['category'] = strip_tags(stripslashes($_POST['ant-category']));
			$newoptions['feedurl'] = strip_tags(stripslashes($_POST['ant-feedurl']));

       	if ( $options != $newoptions ) { 
			$options = $newoptions;
            update_option('widget_ant_options', $newoptions); 
        	}
    	} 
        $options = get_option('widget_ant_options'); 
?> 
        <div> 

        <label for="ant-title" style="line-height:35px;display:block;"><?php _e("Title:"); ?>
			<input class="widefat" type="text" id="ant-title" name="ant-title" value="<?php echo $options['title']; ?>" />
		</label> 
        
		<label for="ant-howmany" style="line-height:35px;display:block;"><?php _e('Number of posts to show:'); ?>  
			<select class="widefat" id='ant-howmany' name="ant-howmany">
			<?php
			$quanti = array("1","2","5","10");
			foreach($quanti as $num):
				if($options['howmany'] == $num) $selected2 = " selected='selected'"; else $selected2 = "";
				echo "<option value='".$num."' label='".$num."'".$selected2.">".$num."</option>";
			endforeach;
			?>
			</select>
		</label> 

        <label for="ant-content" style="line-height:35px;display:block;">Content: 
			<select class="widefat" id='ant-content' name="ant-content">
				<?php
				$opzioni = array("posts"=>"Latest Posts","rss"=>"RSS Feed");

				foreach($opzioni as $chiave=>$opzione):
					if($options['content'] == $chiave) $selected3 = " selected='selected'"; else $selected3 = "";
					echo "<option value='".$chiave."' label='".$opzione."'".$selected3.">".$opzione."</option>";
				endforeach;
				?>
			</select>
		</label> 

        <label for="ant-category" style="line-height:35px;display:block;">Post Category: 
			<select class="widefat" id='ant-category' name="ant-category">
				<option value='' label='All Categories'>All Categories</option>
				<?php
#				$opzioni = array("posts"=>"Latest Posts","rss"=>"RSS Feed");
				$opzioni = get_categories('type=post&hide_empty=1&hierarchical=0');

				foreach($opzioni as $opzione):
					if($options['category'] == $opzione->cat_ID) $selected3 = " selected='selected'"; else $selected3 = "";
					echo "<option value='".$opzione->cat_ID."' label=' ".$opzione->cat_name."'".$selected3."> ".$opzione->cat_name."</option>";
#					print_r($opzione);
				endforeach;
				?>
			</select>
		</label> 


        <label for="ant-feedurl" style="line-height:35px;display:block;">RSS Feed URL:    
			<?php	if($options['feedurl'] != "") $value = $options['feedurl'];	?>
			<input class="widefat" type='text' name='ant-feedurl' id='ant-feedurl' size='20' value='<?php echo $value; ?>'/>
		</label> 

        <input type="hidden" name="ant-submit" id="ant-submit" value="1" /> 
		
		<p style="margin-top:15px;"><a href='http://pixline.net/wordpress-plugins/accessible-rss-news-ticker-widget/en/'>Accessible News Ticker</a> widget.<br /><small>&copy;GPL 2008 Pixline |  <a href="http://talks.pixline.net/forum.php?id=4">Support Forum</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paolo%40pixline%2enet&item_name=Support%20to%20opensource%20projects%20at%20Pixline&no_shipping=1&cn=Something%20to%20say%3f&tax=0&currency_code=EUR&lc=IT&bn=PP%2dDonationsBF&charset=UTF%2d8">Donate!</a></small></p>

        </div> 
    <?php 

    } 


	$widget_ops = array('classname' => 'ant_news_ticker', 'description' => __( "Accessible news ticker, with latest posts / RSS support.") );
    wp_register_sidebar_widget('ant', 'ANT News Ticker', 'widget_ant', $widget_ops); 
    wp_register_widget_control('ant', 'ANT News Ticker', 'widget_ant_control'); 
	if(!is_admin()):
	add_action('wp_head','widget_ant_headscript');
	add_action('wp_footer','widget_ant_footscript');
	endif;
} 


add_action('plugins_loaded', 'widget_ant_init');
?>