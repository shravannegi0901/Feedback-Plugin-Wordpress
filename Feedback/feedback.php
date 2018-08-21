<?php
	/*
	Plugin Name: Feedback
	Description: You can use this plugin for user feedback for page and post. Also you can hide this form from any post and page. shortcode to call [feedback_embbedids]
	Version: 1.1
	*/
	ob_start();  
	global $wpdb;
	$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."plugins_new_feedback (
		`feedback_id` int(11) NOT NULL AUTO_INCREMENT,
		`post_id` int(11) NOT NULL ,
		`type` varchar(11) NOT NULL ,
		`feedback_negative` varchar(255) NULL,
		`feedback_helpful` varchar(255) NOT NULL,
		`feedback` text NOT NULL,
		`read_status` varchar(255) default 'no',
		`name` varchar(255) NOT NULL,
		`email` varchar(255) NOT NULL,
		`newsletter_sub` varchar(255) NOT NULL,
		`created` datetime NOT NULL,
		PRIMARY KEY (`feedback_id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."plugins_new_feedback_setting (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`post_id` int(11) NOT NULL ,
		`type` varchar(11) NOT NULL ,						
		`created` datetime NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	$wpdb->query("CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."plugins_link_setting(
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`facebook_link` varchar(255) NOT NULL ,
		`pinterset_link` varchar(255) NOT NULL ,						
		`privacy_link` varchar(255) NOT NULL,
		`pagination_count` int(11) default '10', 
		`api_key` varchar(255) default '', 
		`list_key` varchar(255) default '',   
		PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	//$wpdb->query("ALTER TABLE wp_plugins_new_feedback CHANGE `newslatter_sub` `newsletter_sub` varchar(255) NOT NULL");
	add_action('admin_menu', 'plugins_user_feedback');
	function plugins_user_feedback() {
		global $wpdb;
		$read_count = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_new_feedback WHERE read_status = 'no'");
		if(!empty($read_count)){
			$counts = count((array)$read_count);
			$count = '<span class="update-plugins count-5">'.$counts.'</span>';
		}else{
			$count = '';
		}
		add_menu_page('Feedback', 'User Feedback '.$count.'', 'administrator', 'shortcode_list_feedback', 'shortcode_list_data_feedback');
		add_submenu_page('shortcode_list_feedback', 'Feedback Setting', 'Feedback Setting', 'administrator', 'shortcode_list_feedback_setting','shortcode_list_data_feedback_setting' );
		add_submenu_page('shortcode_list_feedback', 'Link Setting', 'Link Setting', 'administrator', 'shortcode_list_social_setting','shortcode_list_data_social_setting' );
	}
	function shortcode_list_data_social_setting(){
		include("shortcode_list_social_setting.php");
	}
	function shortcode_list_data_feedback(){
		include("shortcode_list_feedback.php");
	}
	function shortcode_list_data_feedback_setting(){
		include("shortcode_list_feedback_setting.php");
	}
	function shortcode_list_data_newsletter(){
		include("shortcode_list_newsletter.php");
	}
	function wptuts_styles_with_the_lot_feedback()
	{
		wp_enqueue_style('styleplu', plugin_dir_url( __FILE__ ) . 'css/style.css' );
	}
	add_action( 'wp_enqueue_scripts', 'wptuts_styles_with_the_lot_feedback' );
	function wp_feedback_embbedids_shortcode($attrs){ 
		global $wpdb; 
		$table_name = $wpdb->prefix . "plugins_new_feedback";
	// $result = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $attrs['id'] ));
		$varul = plugins_url();
		$post_id = get_the_ID();
		if(is_page() || is_single() ) {
			$post_content = $wpdb->get_results('select * FROM '.$wpdb->prefix.'plugins_new_feedback_setting WHERE post_id ='.$post_id);
			if($post_content){
				return false;
			}
			$result= $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."plugins_link_setting");
			if(!empty($result)){		
				$fb_link = $result[0]->facebook_link;
				$p_link = $result[0]->pinterset_link;
				$priv_link = $result[0]->privacy_link;
			}else{
				$fb_link = '';
				$p_link = '';
				$priv_link = '';
			}
			$html = '
			<section class="popupCol" >
			<h4 class="">Was this article helpful? <a href="#basic" class="greenBtn basic_open">Yes</a> <a href="#update" class="redButton update_open">No</a></h4>
			<div class="row">
			<form id="feedback_form">
			<div class="shadowCol well popup-model" id="basic" style="max-width:44em;"> <a href="javascript:void(0)" class="basic_close">X</a> 	
			<h4 class="heading1">Awesome!</h4>
			<div class="col-md-6">
			<input type="hidden" name="feedback_helpful" class="feedback_helpful" id="feedback_helpful"  >
			<input type="hidden" name="feedback_negative" class="feedback_negative" id="feedback_negative"  >
			<input type="hidden" name="post_id" id="post_id"  value="'.$post_id.'">
			<label class="width100 heading2">Please tell us how it helped you:</label>
			<textarea rows="4" colo  name="feedback" id="feedback" ></textarea>
			<p style="text-align:left;color:red;" class="errorclass" id="feedback_error"></p>
			</div>
			<div class="col-md-6">
			<label class="width100">Your Name:</label>
			<input class="form-control" type="text" name="name" id="name" value="" >
			<p style="text-align:left;color:red;" class="errorclass" id="name_error"></span>
			</div>
			<div class="col-md-6">
			<label class="width100">Your Email:</label>
			<input type="text" name="email"  id="email" class="email" value="" >
			<p style="text-align:left;color:red;" class="errorclass" id="email_error"></span>
			</div>
			<div class="col-md-12 ">
			<label>
			<input type="checkbox" name="newsletter_sub" id="newsletter_sub" value="1">
			Sign me up for the newsletter</label>
			</div>
			<input type="button" onclick="insert_data()" value="Done">
			</form>
			<p>Your <a href="'.$priv_link.'" target="_blank" >Privacy</a> is important to us</p>
			</div>         
			</div>
			<div class="shadowCol well popup-model" id="social" style="max-width:44em;max-height:64em;text-align:center !importatnt;"> <a href="javascript:void(0)" class="basic_close">X</a>
			<h3 style="text-align:center">We appreciate your helpful feedback!</h3>
			<p>Lets get connected - you can find us on social media.</p>
			<div class="btnOuter">  <a href="'.$fb_link.'" target="_blank" class="fbBtn">Facebook</a>
			<a href="'.$p_link.'" target="_blank" class="pinterest">Pinterest</a>
			</div>     
			</div>
			<div class="shadowCol well popup-model" id="update" style="max-width:44em;"> <a href="javascript:void(0)" class="basic_close">X</a>
			<h4>How can we improve it?</h4>
			<div class="row">
			<div class="col-md-12 improveCol">
			<a href="javascript:void(0)" data-toggle="modal" class="article_incorrect"><img src="'.plugins_url().'/Feedback/images/info.jpg" alt=""> This article contains incorrect information</a>
			</div>
			<div class="col-md-12 improveCol"><a href="javascript:void(0)" class="article_content_incorrect"><img src="'.plugins_url().'/Feedback/images/qstn.jpg" alt="">This article does not have the  information I am looking for</a></div>
			</div>
			</div>
			</section>
			'; 
			?>
			<script src="https://code.jquery.com/jquery-1.8.2.min.js"></script>
			<script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>
			<style>
			.popup-model {padding:20px 30px; background:#fff; text-align:center; position:relative;}
			.popup-model h2, .popup-model p {float:; width:100%; text-align:center; padding:20px 0; font-size:32px; color:#21aced;}
			.popup-model p {color:#5a5b5b; font-size:14px; line-height:30px;}
			.popup-model h2 {border-bottom:1px solid #d5d5d5;}
			.popup-model button.basic_close {position:absolute; top:-25px; right:-25px; height:55px; border-radius:50%; border:0; box-shadow:0px 0px 5px rgba(0,0,0,0.5); width:55px; background:#fff; text-align:center; cursor:pointer; }
		</style>
		<script>
			var js = $.noConflict();
			js(document).ready(function(){
				js('#feedback_form input').keypress(function (e) {
					if (e.which == 13) {	  	
						e.preventDefault();
						insert_data();
						return false;
					}
				});	
			});		
			js(document).ready(function(){		
				js('#update').popup();
				js('#basic').popup();
				js('#social').popup();
				js(".basic_close").click(function(){
					js('.popup_background').trigger('click');
				});
				js(".greenBtn").click(function(){
					js("#feedback_error").html("");
					js("#name_error").html("");
					js("#email_error").html("");
					js(".email").val('');
					js("#feedback").val('');
					js("#name").val('');
					js("#feedback_helpful").val('');
					js("#feedback_negative").val('');
					js('.heading1').html("Awesome!");
					js('.heading2').html("Please tell us how it helped you:");	
					var value = "Yes";					
					js(".feedback_helpful").val(value);
				});
				js(".redButton").click(function(){
					js("#feedback_error").html("");
					js("#name_error").html("");
					js("#email_error").html("");
					js(".email").val('');
					js("#feedback").val('');
					js("#name").val('');
					js("#feedback_helpful").val('');
					js("#feedback_negative").val('');
					var value = "no";			
					js(".feedback_helpful").val(value);
				});
				js(".article_incorrect").click(function(){			
					js('.popup_background').trigger('click');
					js("#feedback_error").html("");
					js("#name_error").html("");
					js("#email_error").html("");
					js(".email").val('');
					js("#feedback").val('');
					js("#name").val('');
					js("#feedback_helpful").val('');
					js("#feedback_negative").val('');
					var value = "missing information";
					js(".feedback_negative").val(value);
					js('.heading1').html("Oh no!");
					js('.heading2').html("Please tell us what was incorrect:");			
					js('#basic').popup('show');
				});		
				js(".article_content_incorrect").click(function(){
					js('.popup_background').trigger('click');
					js("#feedback_error").html("");
					js("#name_error").html("");
					js("#email_error").html("");
					js(".email").val('');
					js("#feedback").val('');
					js("#name").val('');
					js("#feedback_helpful").val('');
					js("#feedback_negative").val('');
					var value = "missing content";
					js('.heading1').html("Oh no!");
					js('.heading2').html("Please tell us what was missing:");
					js(".feedback_negative").val(value);
					js('#basic').popup('show');
				});
			});
			function isEmail(email) {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				return regex.test(email);
			}
			function insert_data(){ 
				var feedback = js("#feedback").val();
				if(feedback == ''){
					js("#feedback_error").html("This Field is Required.");
					return false;
				}	
				js("#feedback_error").html("");
				var name = js("#name").val();
				if(name == ''){
					js("#name_error").html("The Name Field is Required.");
					return false;
				}
				js("#name_error").html("");
				var email = js(".email").val();
				if(email == ''){
					js("#email_error").html("The Email Field is Required.");
					return false;
				}
				if(!isEmail(email)){
					js("#email_error").html("Please Enter Valid E-mail ID.");
					return false;
				}
				js("#email_error").html("");		
				var feedback_helpful = js("#feedback_helpful").val();		
				var feedback_negative = js("#feedback_negative").val();
				var post_id = js("#post_id").val();
				if (js('#newsletter_sub').is(":checked")){
					var newsletter_sub = 'yes';
				}else{
					var newsletter_sub = 'no';
				}
				js.ajax({
					type: "POST",  
					url: "<?php echo plugins_url() .'/Feedback/insert_data.php';?>",
					data: {feedback: feedback,feedback_helpful:feedback_helpful, name: name, email: email, newsletter_sub: newsletter_sub,post_id:post_id,feedback_negative:feedback_negative},
					success: function(response) {
						js(".email").val('');
						js("#feedback").val('');
						js("#name").val('');
						js("#feedback_helpful").val('');
						js("#feedback_negative").val('');
						js('.popup_background').trigger('click');
						js('#social').popup('show');
					}
				});
			}
		</script>
		<?php
	}else{
		return false;
	}
	return $html;
}
add_filter ('the_content', 'wp_feedback_embbedids_shortcode');

add_shortcode('feedback_embbedids', 'wp_feedback_embbedids_shortcode');

register_activation_hook( __FILE__, 'my_activation_func' );

function my_activation_func() {
	file_put_contents( __DIR__ . '/my_loggg.txt', ob_get_contents() );
} 
?>