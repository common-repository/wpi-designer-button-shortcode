<?php
class WPiDesBut{	
	public function __construct(){		
		add_action("add_meta_boxes", array($this, "meta_box"));
		add_action("save_post",array($this,"save_post"));
	}	 
	public function fields(){
		$icons_arr=WPiArray::get_icons_arr();
		$icons=WPiTools::array2object($icons_arr);
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$menu_items=array("0"=> "Select Menu");
		$switch=array("0"=>"Don't Send","1"=>"Send");
		foreach($menus as $v){
			$menu_items[$v->slug]=$v->name;
		}		
		$style_ids=WPiDesButCommon::get_style_ids();
		$fields=array(		
			array("label"=>"Style Id", "name"=>'style_id', "type"=>"select",  "section"=>"Button Section",  "group"=>"Button","value"=> "", "list"=> $style_ids),
			array("label"=>"Button Text", "name"=>'text', "type"=>"textarea",  "section"=>"Button Section", "group"=>"Button", "value"=> ""),
			array("label"=>"Icon", "name"=>'icon', "type"=>"select",  "section"=>"Button Section", "group"=>"Button", "value"=> "", "list"=> $icons),
			array("label"=>"Icon Position", "name"=>'icon_position', "type"=>"select",  "section"=>"Button Section", "group"=>"Button", "value"=> "", "list"=>  array("left"=>"Left","right"=>"Right")),
			array("label"=>"Button Type", "name"=>'button_type', "type"=>"select", "section"=>"Link Section", "group"=>"Link", "value"=> "", "list"=>  array("link"=>"Link","popup"=>"Popup","menu"=>"Menu"),"linked"=>"1"),
			array("label"=>"Link", "name"=>'link', "type"=>"link", "section"=>"Link Section", "group"=>"Link", "value"=> "", "linked_field"=>"button_type", "linked_field_item"=>"link"),
			array("label"=>"Popup", "name"=>'popup', "type"=>"textarea", "section"=>"Link Section", "group"=>"Link", "value"=> "", "linked_field"=>"button_type", "linked_field_item"=>"popup"),
			array("label"=>"Popup Type", "name"=>'popup_type', "type"=>"select", "section"=>"Link Section", "group"=>"Link", "value"=> "", "list"=>  array("0"=>"default","preset_1"=>"Full Width"), "linked_field"=>"button_type", "linked_field_item"=>"popup"),
			array("label"=>"Menu", "name"=>'menu', "type"=>"select", "section"=>"Link Section", "group"=>"Link", "value"=> "", "list"=> $menu_items, "linked_field"=>"button_type", "linked_field_item"=>"menu"),
			array("label"=>"Menu Position", "name"=>'menu_position', "type"=>"select", 
				"section"=>"Link Section", "group"=>"Link", "value"=> "", 
				"list"=>  array("0"=>"default","top"=>"Top","bottom"=>"Bottom"), 
				"linked_field"=>"button_type", "linked_field_item"=>"menu"),
			array("label"=>"Target", "name"=>'target', "type"=>"select", "section"=>"Link Section", "group"=>"Link", "value"=> "", "list"=> array("self"=>"Self","_blank"=>"New Window"), "linked_field"=>"button_type", "linked_field_item"=>"link"),	
			array("label"=>"Rel", "name"=>'rel', "type"=>"text", "section"=>"Link Section", "group"=>"Link", "value"=> "", "linked_field"=>"button_type", "linked_field_item"=>"link", "global_enabled"=>"button_rel"),
			array("label"=>"OnClick <span class='help'>ex. javascript:pageTracker._trackPageview (‘example’)</span>", "name"=>'on_click', "type"=>"textarea", "section"=>"Advanced Link Section", "group"=>"Link", "value"=> "", "linked_field"=>"button_type", "linked_field_item"=>"link"),
			array("label"=>"Send custom button data to Contact Form 7", "name"=>'send_data_cf7', "type"=>"boolean", "section"=>"Advanced Link Section", "group"=>"Link", "value"=> "0", "linked_field"=>"button_type", "linked_field_item"=>"popup", "list"=>$switch, "linked"=>"1"),	
			array("label"=>"Custom Field One", "name"=>'custom_field_1', "type"=>"text", "section"=>"Advanced Link Section", "group"=>"Link", "value"=> "0", "linked_field"=>"send_data_cf7", "linked_field_item"=>"1"),	
			array("label"=>"Custom Field Two", "name"=>'custom_field_2', "type"=>"text", "section"=>"Advanced Link Section", "group"=>"Link", "value"=> "0", "linked_field"=>"send_data_cf7", "linked_field_item"=>"1"),	
			array("label"=>"Custom Field Three", "name"=>'custom_field_3', "type"=>"text", "section"=>"Advanced Link Section", "group"=>"Link", "value"=> "0", "linked_field"=>"send_data_cf7", "linked_field_item"=>"1"),	
		);
		return $fields;
	}
	public function meta_box(){		
		add_meta_box("settings", "Style Settings", array($this, "meta_box_html"), "wpi_des_but", "normal", "high");
	}
	
	public function meta_box_html($post){
		wp_nonce_field("wpi_db_meta_box","wpi_db_meta_box_nonce");	
		$output= WPiTemplate::html($post->ID, $this->fields());	
		
		$classes=WPiDesButCommon::get_button_style_class($post->style_id);
		if($post->icon_position=="right"){			
			$icon_position="wpi_icon_right";
		}else{				
			$icon_position="wpi_icon_left";
		}	
		if($post->icon!=""){$icon_class="wpi_icon wpi_icon_".$post->icon." ".$icon_position;}else{$icon_class="";}			
		if(trim($post->text)==""){$no_text_class="wpi_no_text";}else{$no_text_class="";}
		
		$styles_list=WPiDesButCommon::get_styles();	
		
		
		$button_id= "<div class='wpi_id wpi_icon wpi_icon_videocamera'><i></i><div class='wpi_info'><div class='wpi_text'>".$post->ID."</div><div>Button ID: </div></div></div>";		
		$button_id=WPiDesButCommon::get_id(array("id"=>$post->ID, "label"=>"Button ID"));
		$shortcode= "<div id='wpi_shortcode' class='wpi_icon wpi_icon_star'><i></i><div class='wpi_text'>[wpi_designer_button id=".$post->ID."]</div></div>";
		$links="<div id='wpi_links'><a href='post-new.php?post_type=wpi_des_but_sty'>Create New Style</a></div>";
		
		$preview="<div id='wpi_preview' class=' button_wrap'><a class='wpi_designer_button {$classes} {$icon_class} {$no_text_class}' href='#'><i class='wpi_icon_l'></i><span class='wpi_text'>".$post->text."</span><i class='wpi_icon_r'></i></a></div>";
		
		$print="";
		$info="<div class='wpi_info'>".$print."</div>";		
		
		$help=self::help();	
		
		$args=array(
			array("id"=>"wpi_styles", "text"=>"Styles", "content"=>$styles_list), 
			array("id"=>"wpi_icons", "text"=>"icons", "content"=>""), 
			array("id"=>"wpi_help", "text"=>"Help", "content"=>$help, "active"=>true),
		);	
		$tabs=WPiTemplate::create_tabs($args);
		
		$visual_header="<div id='wpi_visual_header'>".$button_id."</div>";
		$action="";	
		
		$content="<div class='wpi_db wpi_des_but'>";			
			$content.="<div id='wpi_designer_button_box' class='wpi_row'>";		
			$content.="<div class='wpi_13 wpi_input'>".$output.$shortcode.$links."</div>";
			$content.="<div class='wpi_23 wpi_visual'><div class='wpi_visual_holder'>";
				$content.=$visual_header;
				$content.=$preview;							
				$content.=$tabs;							
			$content.="</div></div>";	
		$content.="</div>";	
		
		echo $content;
	}
	
	public function save_post($post_id){
		if(!isset($_POST['wpi_db_meta_box_nonce'])) return;
		if(!wp_verify_nonce($_POST["wpi_db_meta_box_nonce"],"wpi_db_meta_box"))return;
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if(!current_user_can("edit_post", $post_id))return;		
		//$field_names=WPiTools::get_field_names($this->fields());		
		WPiData::update_post_meta($post_id, $this->fields());				
	}	
	public function help(){
		$help_args=array("notes"=>array(
		"To add icon to the button select icon from above 'Icons' tab", 
		"To apply style to button select your created style from 'Styles' tab.",
		"Copy generated shortcode from left panel and paste it in any post/page.",
		));
		$help=WPiDesButCommon::get_help_tab($help_args);
		return $help;
	}
}
$buttons_page=new WPiDesBut;

