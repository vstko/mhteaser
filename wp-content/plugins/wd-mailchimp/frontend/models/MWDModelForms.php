<?php
class MWDModelForms {
	////////////////////////////////////////////////////////////////////////////////////////
	// Events                                                                             //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constants                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Variables                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Constructor & Destructor                                                           //
	////////////////////////////////////////////////////////////////////////////////////////
	public function __construct() {
	}
	////////////////////////////////////////////////////////////////////////////////////////
	// Public Methods                                                                     //
	////////////////////////////////////////////////////////////////////////////////////////
	public function showform($id, $type) {
		global $wpdb;
		$form_preview = isset($_GET['form_preview']) ? $_GET['form_preview'] : '';
		$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $id));
		if (!$row || !$row->published || (!$form_preview && $row->type != $type)) {
			return FALSE;
		}
		if (isset($_GET['test_theme']) && (esc_html(stripslashes($_GET['test_theme'])) != '')) {
			$theme_id = esc_html(stripslashes($_GET['test_theme']));
		}
		else {
			$theme_id = $row->theme;
		}
		
		$form_theme = $wpdb->get_var($wpdb->prepare('SELECT `params` FROM ' . $wpdb->prefix . 'mwd_themes WHERE id="%d"', $theme_id));
		if (!$form_theme) {
			$form_theme = $wpdb->get_var('SELECT `params` FROM ' . $wpdb->prefix . 'mwd_themes where `default`=1');
			if (!$form_theme) {
				return FALSE;
			}
		} 
		$cssver = rand();
		$create_css_data = $this->create_css($theme_id, $form_theme, $id, $type);
		wp_enqueue_style('mwd-style-'.$id, MWD_URL . '/css/frontend/mwd-style-'.$id.'.css', array(), $cssver);
		
		$label_id = array();
		$label_type = array();
		$label_all = explode('#****#', $row->label_order);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_type, $label_order_each[1]);
		}
		return array(
			$row,
			$form_theme,
			$label_id,
			$label_type
		);
	}

	public function create_css($theme_id, $form_theme, $form_id, $type){
		global $wpdb;

		$form_theme = json_decode(html_entity_decode($form_theme), true);
		$prefixes = array('HP', 'AGP', 'GP', 'IP', 'SBP', 'SCP', 'MCP', 'SP', 'SHP', 'BP', 'BHP', 'NBP', 'NBHP', 'PBP', 'PBHP', 'PSAP', 'PSDP', 'CBP', 'CBHP', 'MBP', 'MBHP');
		$border_types = array('top', 'left','right', 'bottom');
		$borders = array();
		foreach($prefixes as $prefix){
			$borders[$prefix] = array();
			foreach($border_types as $border_type){
				if(isset($form_theme[$prefix.'Border'.ucfirst($border_type)])){
					array_push($borders[$prefix], $form_theme[$prefix.'Border'.ucfirst($border_type)]);
				}
			}
		}
		clearstatcache();
		$frontend_css = MWD_DIR.'/css/frontend/mwd-style-'.$form_id.'.css';
		$cssfile = fopen($frontend_css, "w");	
		
		array_walk($form_theme, function(&$value, $key) { 
			if(strpos($key, 'Color') > -1 && $value == '') {$value = 'transparent';} 
		}); 
		
		$not_embedded_type_bg = $type != 'embedded' ? '#fff' : 'transparent';
		
		$css_content = 
"#mwd-form".$form_id."{
	padding:".$form_theme['AGPPadding']." !important;
	border-radius:".$form_theme['AGPBorderRadius']."px;
	box-shadow:".$form_theme['AGPBoxShadow'].";
	background: ".$not_embedded_type_bg.";
	border:none !important;
	display:table;
	width:".$form_theme['AGPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
}\r\n";

		
	if($borders['AGP']) {
		foreach($borders['AGP'] as $border){
			if($form_theme['AGPBorderType'] == 'inherit' || $form_theme['AGPBorderType'] == 'initial') {
				$css_content .="
#mwd-form".$form_id." {
	border-".$border.": ".$form_theme['AGPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
#mwd-form".$form_id." {
	border-".$border.": ".$form_theme['AGPBorderWidth']."px ".$form_theme['AGPBorderType']." ".$form_theme['AGPBorderColor']."  !important;
}";	
			}
		}
	}
		
	$css_content .= 
"#mwd-form".$form_id." .mwd-header-bg{
	background-color:".$form_theme['HPBGColor'].";
}\r\n";
	
		
	$css_content .= 
"#mwd-form".$form_id." .mwd-header{
	width:".$form_theme['HPWidth']."%;
	margin:".$form_theme['HPMargin'].";
	border-radius:".$form_theme['HPBorderRadius']."px;
	text-align: ".$form_theme['HPTextAlign'].";
	padding:".$form_theme['HPPadding']." !important;
	border:none !important;
}\r\n";	

	$css_content .= 
"#mwd-form".$form_id." .image_left_right.mwd-header {
	padding: 0 !important;
}\r\n";	

	$css_content .= 
"#mwd-form".$form_id." .image_left_right > div {
	padding:".$form_theme['HPPadding']." !important;
}\r\n";	

	
	if($borders['HP']) {
		foreach($borders['HP'] as $border){
			if($form_theme['HPBorderType'] == 'inherit' || $form_theme['HPBorderType'] == 'initial') {
				$css_content .="
#mwd-form".$form_id." .mwd-header{
	border-".$border.": ".$form_theme['HPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
#mwd-form".$form_id." .mwd-header{
	border-".$border.": ".$form_theme['HPBorderWidth']."px ".$form_theme['HPBorderType']." ".$form_theme['HPBorderColor']."  !important;
}";	
			}
		}
	}

	$css_content .= 
"#mwd-form".$form_id.".header_left_right .wdform-page-and-images{
	display: table-cell;
	width:".$form_theme['GPWidth']."%;
}\r\n";		
	
	
	$css_content .= 
"#mwd-form".$form_id.".header_left_right .mwd-header{
	display: table-cell !important;
	width:".$form_theme['HPWidth']."%;
	vertical-align:middle;
}\r\n";	

	$css_content .= 
".mwd-topbar #mwd-form".$form_id." .mwd-header{
	width:".$form_theme['HTPWidth']."% !important;
}\r\n";

	$css_content .= 
"#mwd-form".$form_id." .mwd-header-title{
	font-size:".$form_theme['HTPFontSize']."px;
	color:".$form_theme['HTPColor'].";
}\r\n";	

	$css_content .= 
"#mwd-form".$form_id." .mwd-header-description{
	font-size:".$form_theme['HDPFontSize']."px;
	color:".$form_theme['HDPColor'].";
}\r\n";	
	
	$css_content .= 
".mwd-form-message".$form_id."{
	font-family:".$form_theme['GPFontFamily'].";
	font-size:".$form_theme['GPFontSize']."px;
	font-weight:".$form_theme['GPFontWeight'].";
	width:100%;
	padding:".$form_theme['GPPadding']." !important;
	margin:".$form_theme['GPMargin'].";
	border-radius:".$form_theme['GPBorderRadius']."px;
	border:none !important;
	text-align: center;
}\r\n";

$css_content .= 
".mwd-success.mwd-form-message".$form_id."{
	background-color:".$form_theme['GPBGColor'].";
	color:".$form_theme['GPColor'].";
}\r\n";

$css_content .= 
"#mwd-minimize-text".$form_id."{
	width:".$form_theme['AGPSPWidth']."%;
}\r\n";

$css_content .= 
"#mwd-minimize-text".$form_id." div{
	background-color: #fff;
	font-size:".$form_theme['MBPFontSize']."px;
	font-weight:".$form_theme['MBPFontWeight'].";
	color: #444;
	padding:".$form_theme['MBPPadding']." !important;
	margin:".$form_theme['MBPMargin'].";
	border-radius:".$form_theme['MBPBorderRadius']."px;
	text-align: ".$form_theme['MBPTextAlign'].";
	border:none !important;
	cursor: pointer;
}\r\n";

if($borders['MBP']) {
	foreach($borders['MBP'] as $border){
		if($form_theme['MBPBorderType'] == 'inherit' || $form_theme['MBPBorderType'] == 'initial') {
			$css_content .="
#mwd-minimize-text".$form_id." div{
	border-".$border.": ".$form_theme['MBPBorderType']." !important;
}";
			break;
		} else{
			$css_content .="
#mwd-minimize-text".$form_id." div{
	border-".$border.": ".$form_theme['MBPBorderWidth']."px ".$form_theme['MBPBorderType']." ".$form_theme['MBPBorderColor']."  !important;
}";	
			}
	}
}

$css_content .= 
"#mwd-minimize-text".$form_id." div:hover {
	background-color:".$form_theme['MBHPBGColor'].";
	color:".$form_theme['MBHPColor'].";
	outline: none;
	border: none !important;
	cursor: pointer;
}\r\n";

if($borders['MBHP']) {
	foreach($borders['MBHP'] as $border){
		if($form_theme['MBHPBorderType'] == 'inherit' || $form_theme['MBHPBorderType'] == 'initial') {
			$css_content .="
#mwd-minimize-text".$form_id." div:hover {
	border-".$border.": ".$form_theme['MBHPBorderType']." !important;
}";
			break;
		} else{
			$css_content .="
#mwd-minimize-text".$form_id." div:hover {
	border-".$border.": ".$form_theme['MBHPBorderWidth']."px ".$form_theme['MBHPBorderType']." ".$form_theme['MBHPBorderColor']."  !important;
}";	
			}
	}
}

	$css_content .= 
"#mwd-form".$form_id." .wdform-page-and-images{
	font-size:".$form_theme['GPFontSize']."px;
	font-weight:".$form_theme['GPFontWeight'].";
	width:".$form_theme['GPWidth']."%;
	color:".$form_theme['GPColor'].";
	padding:".$form_theme['GPPadding'].";
	margin:".$form_theme['GPMargin'].";
	border-radius:".$form_theme['GPBorderRadius']."px;
	border:none !important;
}\r\n";

	$css_content .= 
".mwd-topbar #mwd-form".$form_id." .wdform-page-and-images{
	width:".$form_theme['GTPWidth']."% !important;
}\r\n";
	
	if($borders['GP']) {
		foreach($borders['GP'] as $border){
			if($form_theme['GPBorderType'] == 'inherit' || $form_theme['GPBorderType'] == 'initial') {
				$css_content .="
#mwd-form".$form_id." .wdform-page-and-images,
.mwd-form-message".$form_id.",
#mwd-minimize-text".$form_id."{
	border-".$border.": ".$form_theme['GPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
#mwd-form".$form_id." .wdform-page-and-images,
.mwd-form-message".$form_id.",
#mwd-minimize-text".$form_id."{
	border-".$border.": ".$form_theme['GPBorderWidth']."px ".$form_theme['GPBorderType']." ".$form_theme['GPBorderColor']."  !important;
}";	
			}
		}
	}
	
	$css_content .="
#mwd-form".$form_id." .mini_label {
	font-size:".$form_theme['GPMLFontSize']."px !important;
	font-weight:".$form_theme['GPMLFontWeight'].";
	color:".$form_theme['GPMLColor'].";
	padding:".$form_theme['GPMLPadding']." !important;
	margin:".$form_theme['GPMLMargin'].";
}";
	
	$css_content .="
#mwd-form".$form_id." .check-rad label{
	font-size:".$form_theme['GPMLFontSize']."px !important;
}\r\n";

	
	$css_content .="
#mwd-form".$form_id." .wdform-page-and-images label{
	font-size:".$form_theme['GPFontSize']."px; 
}\r\n";


	if($form_theme['GPAlign'] == 'center'){
	$css_content .=" 
#mwd-form".$form_id." .wdform_section{
	margin:0 auto;
}\r\n";

/* 	$css_content .= 
"#mwd-form".$form_id." .wdform_column{
	float:none;
}\r\n"; */
	} else{
	$css_content .=" 
#mwd-form".$form_id." .wdform_section{
	float:".$form_theme['GPAlign'].";
}\r\n";	
		
	}
	
	$css_content .="
#mwd-form".$form_id." .wdform_section {
	padding:".$form_theme['SEPPadding'].";
	margin:".$form_theme['SEPMargin'].";
	background: transparent;
}";	

	$css_content .="
#mwd-form".$form_id." .wdform_column {
	padding:".$form_theme['COPPadding'].";
	margin:".$form_theme['COPMargin'].";
}";


	$css_content .="
#mwd-form".$form_id." .ui-slider {
	background: ".$form_theme['IPBGColor']." !important;
}";

	$css_content .="
#mwd-scrollbox".$form_id." .mwd-scrollbox-form {
	width:".$form_theme['AGPSPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
	position: relative;
}";

$css_content .="
#mwd-popover".$form_id." .mwd-popover-content {
	width:".$form_theme['AGPSPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
	position: relative;
}";


	$css_content .="
#mwd-pages".$form_id.".wdform_page_navigation {
	width:".$form_theme['AGPWidth']."%;
	margin:".$form_theme['AGPMargin'].";
}";

$css_content .="
#mwd-form".$form_id." .wdform_footer {
	font-size: ".$form_theme['GPFontSize']."px;
	font-weight: ".$form_theme['GPFontWeight'].";
	color: ".$form_theme['GPColor'].";
	width: ".$form_theme['FPWidth']."%;
	margin: ".$form_theme['FPMargin'].";
	padding: ".$form_theme['FPPadding'].";
	/* clear: both; */
}";

	$css_content .="
#mwd-pages".$form_id." .page_active {
	background-color: ".$form_theme['PSAPBGColor'].";
	font-size: ".$form_theme['PSAPFontSize']."px;
	font-weight: ".$form_theme['PSAPFontWeight'].";
	color: ".$form_theme['PSAPColor'].";
	width: ".$form_theme['PSAPWidth']."px;
	height: ".$form_theme['PSAPHeight']."px;
	line-height: ".$form_theme['PSAPLineHeight']."px;
	margin: ".$form_theme['PSAPMargin'].";
	padding: ".$form_theme['PSAPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	cursor: pointer;
}";

	if($borders['PSAP']) {
		foreach($borders['PSAP'] as $border){
			if($form_theme['PSAPBorderType'] == 'inherit' || $form_theme['PSAPBorderType'] == 'initial') {
				$css_content .="
#mwd-pages".$form_id." .page_active {
	border: ".$form_theme['PSAPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
#mwd-pages".$form_id." .page_active {
	border-".$border.": ".$form_theme['PSAPBorderWidth']."px ".$form_theme['PSAPBorderType']." ".$form_theme['PSAPBorderColor']."  !important;
}";
			}
		}
	}				

	$css_content .="
#mwd-pages".$form_id." .page_deactive {
	background-color: ".$form_theme['PSDPBGColor'].";
	font-size: ".$form_theme['PSDPFontSize']."px;
	font-weight: ".$form_theme['PSDPFontWeight'].";
	color: ".$form_theme['PSDPColor'].";
	width: ".$form_theme['PSAPWidth']."px;
	height: ".$form_theme['PSDPHeight']."px;
	line-height: ".$form_theme['PSDPLineHeight']."px;
	margin: ".$form_theme['PSDPMargin'].";
	padding: ".$form_theme['PSDPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	cursor: pointer;
}";

	if($borders['PSDP']) {
		foreach($borders['PSDP'] as $border){
			if($form_theme['PSDPBorderType'] == 'inherit' || $form_theme['PSDPBorderType'] == 'initial') {
				$css_content .="
#mwd-pages".$form_id." .page_deactive {
	border: ".$form_theme['PSDPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
#mwd-pages".$form_id." .page_deactive {
	border-".$border.": ".$form_theme['PSDPBorderWidth']."px ".$form_theme['PSDPBorderType']." ".$form_theme['PSDPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
#mwd-pages".$form_id." .page_percentage_active {
	background-color: ".$form_theme['PSAPBGColor'].";
	font-size: ".$form_theme['PSAPFontSize']."px;
	font-weight: ".$form_theme['PSAPFontWeight'].";
	color: ".$form_theme['PSAPColor'].";
	width: ".$form_theme['PSAPWidth'].";
	height: ".$form_theme['PSAPHeight']."px;
	line-height: ".$form_theme['PSAPLineHeight']."px;
	margin: ".$form_theme['PSAPMargin'].";
	padding: ".$form_theme['PSAPPadding'].";
	border-radius: ".$form_theme['PSAPBorderRadius']."px;
	min-width: 7%;
}";

	if($borders['PSAP']) {
		foreach($borders['PSAP'] as $border){
			if($form_theme['PSAPBorderType'] == 'inherit' || $form_theme['PSAPBorderType'] == 'initial') {
				$css_content .="
#mwd-pages".$form_id." .page_percentage_active {
	border: ".$form_theme['PSAPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
#mwd-pages".$form_id." .page_percentage_active {
	border-".$border.": ".$form_theme['PSAPBorderWidth']."px ".$form_theme['PSAPBorderType']." ".$form_theme['PSAPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
#mwd-pages".$form_id." .page_percentage_deactive {
	background-color: ".$form_theme['PSDPBGColor'].";
	font-size: ".$form_theme['PSDPFontSize']."px;
	font-weight: ".$form_theme['PSDPFontWeight'].";
	color: ".$form_theme['PSDPColor'].";
	width: ".$form_theme['PPAPWidth'].";
	height: ".$form_theme['PSDPHeight']."px;
	line-height: ".$form_theme['PSDPLineHeight']."px;
	margin: ".$form_theme['PSDPMargin'].";
	padding: ".$form_theme['PSDPPadding'].";
	border-radius: ".$form_theme['PSDPBorderRadius']."px;
}";

	if($borders['PSDP']) {
		foreach($borders['PSDP'] as $border){
			if($form_theme['PSDPBorderType'] == 'inherit' || $form_theme['PSDPBorderType'] == 'initial') {
				$css_content .="
#mwd-pages".$form_id." .page_percentage_deactive {
	border: ".$form_theme['PSDPBorderType']." !important;
}";
				break;
			} else {
				$css_content .="
#mwd-pages".$form_id." .page_percentage_deactive {
	border-".$border.": ".$form_theme['PSDPBorderWidth']."px ".$form_theme['PSDPBorderType']." ".$form_theme['PSDPBorderColor']."  !important;
}";
			}
		}
	}
	
	$css_content .="
#mwd-action-buttons".$form_id." {
	font-size:".$form_theme['CBPFontSize']."px;
	font-weight:".$form_theme['CBPFontWeight'].";
	color:".$form_theme['CBPColor'].";
	text-align: center;
  cursor: pointer;
	font-family: monospace;
}";

$css_content .="
#closing-form".$form_id.",
#minimize-form".$form_id." {
	position: ".$form_theme['CBPPosition'].";
	background:".$form_theme['CBPBGColor'].";
	padding:".$form_theme['CBPPadding'].";
	margin:".$form_theme['CBPMargin'].";
	border-radius:".$form_theme['CBPBorderRadius']."px;
	border:none;
}";

	$css_content .="
#closing-form".$form_id." {
	top: ".$form_theme['CBPTop'].";
	right: ".$form_theme['CBPRight'].";
	bottom: ".$form_theme['CBPBottom'].";
	left: ".$form_theme['CBPLeft'].";
}";


$for_mini = $form_theme['CBPLeft'] ? 'left' : 'right';
$css_content .="
#minimize-form".$form_id." {
	top: ".$form_theme['CBPTop'].";
	".$for_mini.": ".(2 * intval($form_theme['CBP'.ucfirst($for_mini)]) + intval($form_theme['CBPFontSize']) + 3)."px;
	bottom: ".$form_theme['CBPBottom'].";
}";
	
	if($borders['CBP']) {
		foreach($borders['CBP'] as $border){
			if($form_theme['CBPBorderType'] == 'inherit' || $form_theme['CBPBorderType'] == 'initial') {
				$css_content .="
#closing-form".$form_id.",
#minimize-form".$form_id." {
	border-".$border.": ".$form_theme['CBPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
#closing-form".$form_id.",
#minimize-form".$form_id." {
	border-".$border.": ".$form_theme['CBPBorderWidth']."px ".$form_theme['CBPBorderType']." ".$form_theme['CBPBorderColor']."  !important;
}";	
			}
		}
	} 
	
	$css_content .="
#closing-form".$form_id.":hover,
#minimize-form".$form_id.":hover {
	background:".$form_theme['CBHPBGColor'].";
	color:".$form_theme['CBHPColor'].";
	border:none;
}";
	
	if($borders['CBHP']) {
		foreach($borders['CBHP'] as $border){
			if($form_theme['CBHPBorderType'] == 'inherit' || $form_theme['CBHPBorderType'] == 'initial') {
				$css_content .="
#closing-form".$form_id.":hover,
#minimize-form".$form_id.":hover {
	border-".$border.": ".$form_theme['CBHPBorderType']." !important;
}";
				break;
			} else{
				$css_content .="
#closing-form".$form_id.":hover,
#minimize-form".$form_id.":hover {
	border-".$border.": ".$form_theme['CBHPBorderWidth']."px ".$form_theme['CBHPBorderType']." ".$form_theme['CBHPBorderColor']."  !important;
}";	
			}
		}
	}
	
	$user_agent = $_SERVER['HTTP_USER_AGENT']; 
    if(stripos( $user_agent, 'Safari') !== false && stripos( $user_agent, 'Chrome') === false) {
        $css_content .="
.mwd-popover-container:before  { 
	position:absolute;
}";	
	}
	
	$css_content .="
#mwd-form".$form_id." .wdform-required {
	color: ".$form_theme['OPRColor'].";
}

#mwd-form".$form_id." .input_deactive {
	color: ".$form_theme['OPDeInputColor']." !important;
	font-style: ".$form_theme['OPFontStyle'].";
}


#mwd-form".$form_id." .file-picker{
	background: url(".MWD_URL.'/'.$form_theme['OPFBgUrl'].") ".$form_theme['OPFBGRepeat']." ".$form_theme['OPFPos1']." ".$form_theme['OPFPos2'].";
	display: inline-block;
}

#mwd-form".$form_id." .wdform-calendar-button {
	background: url(".MWD_URL.'/'.$form_theme['OPDPIcon'].") ".$form_theme['OPDPRepeat']." ".$form_theme['OPDPPos1']." ".$form_theme['OPDPPos2']."; 
	margin: ".$form_theme['OPDPMargin'].";
    position: absolute;
}

#mwd-form".$form_id." .mwd-subscribe-reset{
	float: ".$form_theme['SPAlign'].";
}

#mwd-form".$form_id." .mwd-subscribe-reset div{
	text-align: ".$form_theme['SPAlign'].";
}

#mwd-form".$form_id." .button-submit {
	margin-right: 15px;
}

";

		$defaultStyles = 
"#mwd-form".$form_id."{
	font-family:".$form_theme['GPFontFamily'].";
	background:".$form_theme['GPBGColor'].";
}\r\n


#mwd-form".$form_id." .wdform_section {
	background:".($form_theme['GPBGColor'] != $form_theme['SEPBGColor'] ? $form_theme['SEPBGColor'] : 'transparent').";
}\r\n	

#mwd-form".$form_id." .captcha_img{
	height:".$form_theme['IPHeight']."px;
}

#mwd-form".$form_id." input[type='text'],
#mwd-form".$form_id." input[type=password],
#mwd-form".$form_id." input[type=url],
#mwd-form".$form_id." input[type=email],
#mwd-form".$form_id." textarea,
#mwd-form".$form_id." .ui-spinner-input,
#mwd-form".$form_id." .file-upload-status,
#mwd-form".$form_id." select {
	font-size:".$form_theme['IPFontSize']."px;
	font-weight:".$form_theme['IPFontWeight'].";
	height:".$form_theme['IPHeight']."px;
	line-height:".$form_theme['IPHeight']."px;
	background-color:".$form_theme['IPBGColor'].";
	color:".$form_theme['IPColor'].";
	padding:".$form_theme['IPPadding'].";
	margin:".$form_theme['IPMargin'].";
	border-radius:".$form_theme['IPBorderRadius']."px !important;
	box-shadow:".$form_theme['IPBoxShadow'].";
	border:none;
}";

	if($borders['IP']) {
		foreach($borders['IP'] as $border){
			if($form_theme['IPBorderType'] == 'inherit' || $form_theme['IPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." input[type='text']:not(.ui-spinner-input),
#mwd-form".$form_id." input[type=password],
#mwd-form".$form_id." input[type=url],
#mwd-form".$form_id." input[type=email],
#mwd-form".$form_id." textarea,
#mwd-form".$form_id." .ui-spinner,
#mwd-form".$form_id." .ui-slider,
#mwd-form".$form_id." .ui-slider-handle,
#mwd-form".$form_id." select {
	border-".$border.": ".$form_theme['IPBorderType']." !important;
}";

		$defaultStyles .="
#mwd-form".$form_id." .ui-spinner-button,
	border-left: ".$form_theme['IPBorderType']." !important;
}";

$defaultStyles .="
#mwd-form".$form_id." .ui-slider-range {
}";
				break;
			} else {
	$defaultStyles .="
#mwd-form".$form_id." input[type='text']:not(.ui-spinner-input),
#mwd-form".$form_id." input[type=password],
#mwd-form".$form_id." input[type=url],
#mwd-form".$form_id." input[type=email],
#mwd-form".$form_id." textarea,
#mwd-form".$form_id." .ui-spinner,
#mwd-form".$form_id." .ui-slider,
#mwd-form".$form_id." .ui-slider-handle,
#mwd-form".$form_id." select {
	border-".$border.": ".$form_theme['IPBorderWidth']."px ".$form_theme['IPBorderType']." ".$form_theme['IPBorderColor']."  !important;
}";
	if($border == 'left'){
		$defaultStyles .="
#mwd-form".$form_id." .ui-spinner-button {
	border-left: ".$form_theme['IPBorderWidth']."px ".$form_theme['IPBorderType']." ".$form_theme['IPBorderColor']."  !important;
}";
				}
				
	$defaultStyles .="
#mwd-form".$form_id." .ui-slider-range {
	background: ".$form_theme['IPBorderColor']."  !important;
}";

			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." select {
	appearance: ".$form_theme['SBPAppearance'].";
	-moz-appearance: ".$form_theme['SBPAppearance'].";
	-webkit-appearance: ".$form_theme['SBPAppearance'].";
	background:".$form_theme['IPBGColor']." url(".MWD_URL.'/'.$form_theme['SBPBackground'].") ".$form_theme['SBPBGRepeat']." ".$form_theme['SBPBGPos1']." ".$form_theme['SBPBGPos2'].";
	background-size: ".$form_theme['SBPBGSize1']." ".$form_theme['SBPBGSize2'].";
}";


	$defaultStyles .="
#mwd-form".$form_id." .radio-div label span {
	height:".$form_theme['SCPHeight']."px;
	width:".$form_theme['SCPWidth']."px;
	background-color:".$form_theme['SCPBGColor'].";
	margin:".$form_theme['SCPMargin'].";
	box-shadow:".$form_theme['SCPBoxShadow'].";
	border-radius: ".$form_theme['SCPBorderRadius']."px;
	border: none;
	display: inline-block;
	vertical-align: middle;
}";

	$defaultStyles .="
#mwd-form".$form_id." .radio-div input[type='radio']:checked + label span:after {
	content: '';
	width:".$form_theme['SCCPWidth']."px;
	height:".$form_theme['SCCPHeight']."px;
	background:".$form_theme['SCCPBGColor'].";
    border-radius: ".$form_theme['SCCPBorderRadius']."px;
    margin: ".$form_theme['SCCPMargin']."px;
	display: block;
}";

	if($borders['SCP']) {
		foreach($borders['SCP'] as $border){
			if($form_theme['SCPBorderType'] == 'inherit' || $form_theme['SCPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .radio-div label span {
	border-".$border.": ".$form_theme['SCPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
#mwd-form".$form_id." .radio-div label span {
	border-".$border.": ".$form_theme['SCPBorderWidth']."px ".$form_theme['SCPBorderType']." ".$form_theme['SCPBorderColor']."  !important;
}";
			}
		}
	}

	$defaultStyles .="
#mwd-form".$form_id." .checkbox-div label span {
	height:".$form_theme['MCPHeight']."px;
	width:".$form_theme['MCPWidth']."px;
	background-color:".$form_theme['MCPBGColor'].";
	margin:".$form_theme['MCPMargin'].";
	box-shadow:".$form_theme['MCPBoxShadow'].";
	border-radius: ".$form_theme['MCPBorderRadius']."px;
	border: none;
	display: inline-block;
	vertical-align: middle;
}";

	$defaultStyles .="
#mwd-form".$form_id." .checkbox-div input[type='checkbox']:checked + label span:after {
	content: '';
	width:".$form_theme['MCCPWidth']."px;
	height:".$form_theme['MCCPHeight']."px;
	background:".($form_theme['MCCPBackground'] ? ($form_theme['MCCPBGColor']." url(".MWD_URL.'/'.$form_theme['MCCPBackground'].") ".$form_theme['MCCPBGRepeat']." ".$form_theme['MCCPBGPos1']." ".$form_theme['MCCPBGPos2']) : $form_theme['MCCPBGColor']).";
    border-radius: ".$form_theme['MCCPBorderRadius']."px;
    margin: ".$form_theme['MCCPMargin']."px;
	display: block;
}";

	if($borders['MCP']) {
		foreach($borders['MCP'] as $border){
			if($form_theme['MCPBorderType'] == 'inherit' || $form_theme['MCPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .checkbox-div label span {
	border-".$border.": ".$form_theme['MCPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
#mwd-form".$form_id." .checkbox-div label span {
	border-".$border.": ".$form_theme['MCPBorderWidth']."px ".$form_theme['MCPBorderType']." ".$form_theme['MCPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .button-submit {
	background-color:".$form_theme['SPBGColor']." !important;
	font-size:".$form_theme['SPFontSize']."px !important;
	font-weight:".$form_theme['SPFontWeight']." !important;
	color:".$form_theme['SPColor']." !important;
	height:".$form_theme['SPHeight']."px !important;
	width:".$form_theme['SPWidth']."px !important;
	margin:".$form_theme['SPMargin']." !important;
	padding:".$form_theme['SPPadding']." !important;
	box-shadow:".$form_theme['SPBoxShadow']." !important;
	border-radius: ".$form_theme['SPBorderRadius']."px;
	border: none !important;
	background-image: none !important;
}";

	if($borders['SP']) {
		foreach($borders['SP'] as $border){
			if($form_theme['SPBorderType'] == 'inherit' || $form_theme['SPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .button-submit {
	border-".$border.": ".$form_theme['SPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
#mwd-form".$form_id." .button-submit {
	border-".$border.": ".$form_theme['SPBorderWidth']."px ".$form_theme['SPBorderType']." ".$form_theme['SPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .button-submit:hover {
	background-color:".$form_theme['SHPBGColor']." !important;
	color:".$form_theme['SHPColor']." !important;
}";

	if($borders['SHP']) {
		foreach($borders['SHP'] as $border){
			if($form_theme['SHPBorderType'] == 'inherit' || $form_theme['SHPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .button-submit:hover {
	border-".$border.": ".$form_theme['SHPBorderType']." !important;
}";
				break;
			} else{
				$defaultStyles .="
#mwd-form".$form_id." .button-submit:hover {
	border-".$border.": ".$form_theme['SHPBorderWidth']."px ".$form_theme['SHPBorderType']." ".$form_theme['SHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .button-reset,
#mwd-form".$form_id." button {
	background-color:".$form_theme['BPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['BPColor']." !important;
	height:".$form_theme['BPHeight']."px !important;
	width:".$form_theme['BPWidth']."px !important;
	margin:".$form_theme['BPMargin']." !important;
	padding:".$form_theme['BPPadding']." !important;
	box-shadow:".$form_theme['BPBoxShadow']." !important;
	border-radius: ".$form_theme['BPBorderRadius']."px;
	border: none !important;
	background-image: none !important;
}";

	if($borders['BP']) {
		foreach($borders['BP'] as $border){
			if($form_theme['BPBorderType'] == 'inherit' || $form_theme['BPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .button-reset,
#mwd-form".$form_id." button {
	border-".$border.": ".$form_theme['BPBorderType']." !important;
}";
				break;
			} else{
			$defaultStyles .= "
#mwd-form".$form_id." .button-reset,
#mwd-form".$form_id." button {
	border-".$border.": ".$form_theme['BPBorderWidth']."px ".$form_theme['BPBorderType']." ".$form_theme['BPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .button-reset:hover,
#mwd-form".$form_id." button:hover {
	background-color:".$form_theme['BHPBGColor']." !important;
	color:".$form_theme['BHPColor']." !important;
}";

	if($borders['BHP']) {
		foreach($borders['BHP'] as $border){
			if($form_theme['BHPBorderType'] == 'inherit' || $form_theme['BHPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .button-reset:hover,
#mwd-form".$form_id." button:hover{
	border-".$border.": ".$form_theme['BHPBorderType']." !important;
}";
				break;
			} else {
			$defaultStyles .= "
#mwd-form".$form_id." .button-reset:hover,
#mwd-form".$form_id." button:hover {
	border-".$border.": ".$form_theme['BHPBorderWidth']."px ".$form_theme['BHPBorderType']." ".$form_theme['BHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button {
	background-color:".$form_theme['NBPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['NBPColor']." !important;
	height:".$form_theme['NBPHeight']."px !important;
	width:".$form_theme['NBPWidth']."px !important;
	margin:".$form_theme['NBPMargin']." !important;
	padding:".$form_theme['NBPPadding']." !important;
	box-shadow:".$form_theme['NBPBoxShadow']." !important;
	border-radius: ".$form_theme['NBPBorderRadius']."px;
	border: none !important;
}";

	if($borders['NBP']) {
		foreach($borders['NBP'] as $border){
			if($form_theme['NBPBorderType'] == 'inherit' || $form_theme['NBPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button {
	border-".$border.": ".$form_theme['NBPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button {
	border-".$border.": ".$form_theme['NBPBorderWidth']."px ".$form_theme['NBPBorderType']." ".$form_theme['NBPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button:hover {
	background-color:".$form_theme['NBHPBGColor']." !important;
	color:".$form_theme['NBHPColor']." !important;
}";

	$defaultStyles .= 
"#mwd-minimize-text".$form_id." div{
	background-color:".$form_theme['MBPBGColor'].";
	color:".$form_theme['MBPColor'].";
}\r\n";

	if($borders['NBHP']) {
		foreach($borders['NBHP'] as $border){
			if($form_theme['NBHPBorderType'] == 'inherit' || $form_theme['NBHPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['NBHPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
#mwd-form".$form_id." .next-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['NBHPBorderWidth']."px ".$form_theme['NBHPBorderType']." ".$form_theme['NBHPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button {
	background-color:".$form_theme['PBPBGColor']." !important;
	font-size:".$form_theme['BPFontSize']."px !important;
	font-weight:".$form_theme['BPFontWeight']." !important;
	color:".$form_theme['PBPColor']." !important;
	height:".$form_theme['PBPHeight']."px !important;
	width:".$form_theme['PBPWidth']."px !important;
	margin:".$form_theme['PBPMargin']." !important;
	padding:".$form_theme['PBPPadding']." !important;
	box-shadow:".$form_theme['PBPBoxShadow']." !important;
	border-radius: ".$form_theme['PBPBorderRadius']."px;
	border: none !important;
}";

	if($borders['PBP']) {
		foreach($borders['PBP'] as $border){
			if($form_theme['PBPBorderType'] == 'inherit' || $form_theme['PBPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button {
	border-".$border.": ".$form_theme['PBPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button {
	border-".$border.": ".$form_theme['PBPBorderWidth']."px ".$form_theme['PBPBorderType']." ".$form_theme['PBPBorderColor']."  !important;
}";
			}
		}
	}
	
	$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button:hover {
	background-color:".$form_theme['PBHPBGColor']." !important;
	color:".$form_theme['PBHPColor']." !important;
}";

	if($borders['PBHP']) {
		foreach($borders['PBHP'] as $border){
			if($form_theme['PBHPBorderType'] == 'inherit' || $form_theme['PBHPBorderType'] == 'initial') {
				$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button:hover {
	border-".$border.": ".$form_theme['PBHPBorderType']." !important;
}";
				break;
			} else {
				$defaultStyles .="
#mwd-form".$form_id." .previous-page div.wdform-page-button:hover{
	border-".$border.": ".$form_theme['PBHPBorderWidth']."px ".$form_theme['PBHPBorderType']." ".$form_theme['PBHPBorderColor']."  !important;
}";
			}
		}
	}

	$defaultStyles .="
#mwd-form".$form_id." input[type='checkbox'],
#mwd-form".$form_id." input[type='radio'] {
    display:none;
}";


	if($theme_id != 0){
		$css_content .= $defaultStyles;
	} else {
		$css_content .="
#mwd-form".$form_id." .check-rad.mwd-right input[type='checkbox'],
#mwd-form".$form_id." .check-rad.mwd-right input[type='radio'] {
		float: right;
		margin: 5px;
}";
	}
	
	if($form_theme['CUPCSS']) {
		$pattern = '/\/\/(.+)(\r\n|\r|\n)/';
		$form_theme_css = explode('{', $form_theme['CUPCSS']);
		$count_after_explod_theme = count($form_theme_css);
		for ($i = 0; $i < $count_after_explod_theme; $i++) {
			$body_or_classes[$i] = explode('}', $form_theme_css[$i]);
		}
		for ($i = 0; $i < $count_after_explod_theme; $i++) {
			if ($i == 0) {
				$body_or_classes[$i][0] = "#mwd-form" . $form_id . ' ' . str_replace(',', ", #mwd-form" . $form_id, $body_or_classes[$i][0]);
			}
			else {
				$body_or_classes[$i][1] = "#mwd-form" . $form_id . ' ' . str_replace(',', ", #mwd-form" . $form_id, $body_or_classes[$i][1]);
			}
		}
		for ($i = 0; $i < $count_after_explod_theme; $i++) {
			$body_or_classes_implode[$i] = implode('}', $body_or_classes[$i]);
		}
		$theme = implode('{', $body_or_classes_implode);
		$theme = preg_replace($pattern, ' ', $theme); 
		$css_content .=  str_replace('[SITE_ROOT]', MWD_URL, $theme);
	}
	
		$txtfilecontent = fwrite($cssfile, $css_content);
		fclose($cssfile);
		clearstatcache();
	}
	
	public function savedata($form, $id) {
		global $wpdb;
		$mwd_settings = get_option('mwd_settings');
		$all_files = array();
		$correct = FALSE;
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$id_for_old = $id;
		if (!$form->form_front) {
			$id = '';
		}
		if (isset($_POST["mwd-counter" . $id])) {
			$counter = esc_html($_POST["mwd-counter" . $id]);
			if (isset($_POST["mwd_captcha_input"])) {
				$mwd_captcha_input = esc_html($_POST["mwd_captcha_input"]);
				$session_mwd_captcha_code = isset($_SESSION[$id . '_mwd_captcha_code']) ? $_SESSION[$id . '_mwd_captcha_code'] : '-';
				if (md5($mwd_captcha_input) == $session_mwd_captcha_code) {
					$correct = TRUE;
				}
				else {
					?>
					<script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'mwd-text')); ?>");</script>
					<?php
				}
			}
			elseif (isset($_POST["mwd_arithmetic_captcha_input"])) {
				$mwd_arithmetic_captcha_input = esc_html($_POST["mwd_arithmetic_captcha_input"]);
				$session_mwd_arithmetic_captcha_code = isset($_SESSION[$id . '_mwd_arithmetic_captcha_code']) ? $_SESSION[$id . '_mwd_arithmetic_captcha_code'] : '-';
				if (md5($mwd_arithmetic_captcha_input) == $session_mwd_arithmetic_captcha_code) {
					$correct = TRUE;
				}
				else {
					?>
					<script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'mwd-text')); ?>");</script>
					<?php
				}
			} elseif (isset($_POST["g-recaptcha-response"])){
				$privatekey= isset($mwd_settings['private_key']) ? $mwd_settings['private_key'] : '';	
				$captcha = $_POST['g-recaptcha-response'];
				$url = 'https://www.google.com/recaptcha/api/siteverify';
				$data = array(
					'secret' => $privatekey,
					'response' => $captcha,
					'remoteip' => $_SERVER['REMOTE_ADDR']
				);
    
				$curlConfig = array(
					CURLOPT_URL => $url,
					CURLOPT_POST => true,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_POSTFIELDS => $data
				);

				$ch = curl_init();
				curl_setopt_array($ch, $curlConfig);
				$response = curl_exec($ch);
				curl_close($ch);
    
				$jsonResponse = json_decode($response);

				if ($jsonResponse->success == "true")
					$correct = TRUE;
				else {
					?>
					<script>alert("<?php echo addslashes(__('Error, incorrect Security code.', 'mwd-text')); ?>");</script>
					<?php
				}
			}
			else {
				$correct = TRUE;
			}
			if ($correct) {
				$blocked_ip = $wpdb->get_var($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_forms_blocked WHERE ip="%s"', $ip));
				if ($blocked_ip) {
					$_SESSION['mwd_message_after_submit' . $id] = array('msg' => addslashes(__('Your ip is blacklisted. Please contact the website administrator.', 'mwd-text')), 'type' => 'error');
					wp_redirect($_SERVER["REQUEST_URI"]);
					exit;
				}
		
				$result_temp = $this->save_db($counter, $id_for_old);
				$all_files = $result_temp[0];
				if (is_numeric($all_files)) {
					$this->remove($all_files);
				}
				elseif (isset($counter)) {
					$this->save_mailchimp($id_for_old, $result_temp[2], $result_temp[1], $counter, $all_files, $result_temp[3], $result_temp[4]);
				}
			}    
			return $all_files;
		}
		return $all_files;
	}
  
	public function save_db($counter, $id) {
		global $wpdb;
		$current_user = wp_get_current_user();
		if ($current_user->ID != 0) {
			$wp_userid =  $current_user->ID;
			$wp_username =  $current_user->display_name;
			$wp_useremail =  $current_user->user_email;
		}
		else {
			$wp_userid = '';
			$wp_username = '';
			$wp_useremail = '';
		}
		$ip = $_SERVER['REMOTE_ADDR']; 
		$chgnac = TRUE;
		$all_files = array();
		$paypal = array();
		$paypal['item_name'] = array();
		$paypal['quantity'] = array();
		$paypal['amount'] = array();
		$is_amount=false;
		$paypal['on_os'] = array();
		$total = 0;
		$form_currency = '$';
		$currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
		$form = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id= %d", $id));
		$id_old = $id;
		
		if (!$form->form_front) {
			$id = '';
		}
		if ($form->payment_currency) {
			$form_currency = $currency_sign[array_search($form->payment_currency, $currency_code)];
		}
		
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		try {
			$mchlists = $api->call( 'lists/list' , array( 'apikey' => $apikey) );
		} catch (Exception $e) {
			return;
		}

		$label_id = array();
		$label_label = array();
		$label_type = array();
		$disabled_fields = explode(',', (isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : ""));
		$disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);
    
		$label_all = explode('#****#',$form->label_order_current);		
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			array_push($label_id, $label_id_each[0]);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}
		$max = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix . "mwd_forms_submits");
	
		$merge_vars = json_decode(html_entity_decode($form->merge_variables), true);
		/* $ids_and_labels = array_combine($label_id, $label_label);
		$GLOBALS['merge_vars_messages'] = array_filter(array_map( function($value) use ($ids_and_labels) { return isset($ids_and_labels[$value]) ? $ids_and_labels[$value] : ''; }, $merge_vars)); */
		
		$groupings = json_decode(stripslashes($_POST['groupings'.$id]), true);
		
		$email_field_key = $merge_vars['EMAIL'];
		/* $merge_vars['EMAIL'] = !in_array($email_field_key, $label_id) ? null : $merge_vars['EMAIL']; */
		
		$email_field = in_array('type_submitter_mail', $label_type) ? true : false;
		$action_field = in_array('type_action', $label_type) ? true : false;
		$list_choice = in_array('type_list', $label_type) ? true : false;
		
		$fparams = array('email_field' => $email_field, 'action_field' => $action_field, 'list_choice' => $list_choice );

		$mergeVarGroups = array();
		$form_action = '';
	
		foreach ($label_type as $key => $type) {
			$value = '';
			if ($type == "type_submit_reset" or $type == "type_editor" or  $type == "type_captcha" or $type == "type_arithmetic_captcha" or  $type == "type_recaptcha" or  $type == "type_button" or $type == "type_paypal_total" or $type == "type_send_copy") 
				continue;
        
			$i = $label_id[$key];

			if(!in_array($i, $disabled_fields)) {
				switch ($type) {
					case 'type_text':
					case 'type_password':
					case 'type_textarea':
					case "type_date":
					case "type_country":				
					case "type_number": 
					case "type_submitter_mail":{
						$value = isset($_POST['wdform_'.$i."_element".$id]) ? esc_html($_POST['wdform_'.$i."_element".$id]) : "";
						break;
					}
					
					case "type_action":{
						$w_type = isset($_POST['w_type'.$id]) ? esc_html($_POST["w_type".$id]) : "";
						if($w_type != 'checkbox' && isset($_POST['wdform_'.$i."_element".$id])){
							$fparams['subscribe_action'] = esc_html($_POST['wdform_'.$i."_element".$id]);
							$value = esc_html($_POST['wdform_'.$i."_element".$id]);
						} else{
							for($j = 0; $j < 2; $j++) {						
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)){
									$fparams['subscribe_action'] = $element;
									$value = $element;
									break;
								}
							}
						}
						
						break;
					} 
					case "type_list":{
						$w_type = isset($_POST['w_type'.$id]) ? esc_html($_POST["w_type".$id]) : "";
						if($w_type != 'checkbox' && isset($_POST['wdform_'.$i."_element".$id])){
							$fparams['list_ids'][] = esc_html($_POST['wdform_'.$i."_element".$id]);
							$value = esc_html($_POST['wdform_'.$i."_element".$id]);
						} else{
							$value = '';
							for($j = 0; $j < $mchlists['total']; $j++) {						
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)){
									$fparams['list_ids'][] = $element;
									$value = $value.$element. '***br***';
								}
							}
						}
						
						break;
					}
		
					case "type_date_fields": {
						if(isset($_POST['wdform_'.$i."_day".$id]) || isset($_POST['wdform_'.$i."_month".$id]) || isset($_POST['wdform_'.$i."_year".$id])){
							$value = (isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "") . '-' . (isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "");
						}	
						break;
					}					
					
					case "type_time": {
						if(isset($_POST['wdform_'.$i."_hh".$id]) || isset($_POST['wdform_'.$i."_mm".$id]) || isset($_POST['wdform_'.$i."_ss".$id])) {
							$ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
							if(isset($ss)) {
								$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : "");
							}
							else {
								$value = (isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : "") . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
							}								
							$am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
							if(isset($am_pm)) {
								$value = $value . ' ' . $am_pm;
							}
						}
						break;
					}					
						
					case "type_file_upload": {
						$files = isset($_FILES['wdform_'.$i.'_file'.$id]) ? $_FILES['wdform_'.$i.'_file'.$id] : NULL;
						foreach($files['name'] as $file_key => $file_name) {
							if($file_name) {
								$untilupload = $form->form_fields;
								$untilupload = substr($untilupload, strpos($untilupload,$i.'*:*id*:*type_file_upload'), -1);
								$untilupload = substr($untilupload, 0, strpos($untilupload,'*:*new_field*:'));
								$untilupload = explode('*:*w_field_label_pos*:*',$untilupload);
								$untilupload = $untilupload[1];
								$untilupload = explode('*:*w_destination*:*',$untilupload);
								$destination = $untilupload[0];
								$destination = str_replace(site_url() . '/', '', $destination);
								$untilupload = $untilupload[1];
								$untilupload = explode('*:*w_extension*:*',$untilupload);
								$extension 	 = $untilupload[0];
								$untilupload = $untilupload[1];
								$untilupload = explode('*:*w_max_size*:*',$untilupload);
								$max_size 	 = $untilupload[0];
								$untilupload = $untilupload[1];
								$fileName = $files['name'][$file_key];
								$fileSize = $files['size'][$file_key];

								if($fileSize > $max_size * 1024) {
									echo "<script> alert('" . addslashes(__('The file exceeds the allowed size of', 'mwd-text')) . $max_size . " KB');</script>";
									return array($max+1);
								}

								$uploadedFileNameParts = explode('.',$fileName);
								$uploadedFileExtension = array_pop($uploadedFileNameParts);
								$to = strlen($fileName) - strlen($uploadedFileExtension) - 1;
							  
								$fileNameFree = substr($fileName, 0, $to);
								$invalidFileExts = explode(',', $extension);
								$extOk = false;

								foreach($invalidFileExts as $key => $valuee) {
									if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension)))) {
										$extOk = true;
									}
								}
							   
								if ($extOk == false) {
									echo "<script> alert('" . addslashes(__('Sorry, you are not allowed to upload this type of file.', 'mwd-text')) . "');</script>";
									return array($max+1);
								}
							  
								$fileTemp = $files['tmp_name'][$file_key];
								$p = 1;
							  
								if(!file_exists($destination))
									mkdir($destination , 0777);
								if (file_exists($destination . "/" . $fileName)) {
									$fileName1 = $fileName;
									while (file_exists($destination . "/" . $fileName1)) {
										$to = strlen($file_name) - strlen($uploadedFileExtension) - 1;
										$fileName1 = substr($fileName, 0, $to) . '(' . $p . ').' . $uploadedFileExtension;
										$p++;
									}
									$fileName = $fileName1;
								}
								
								if(!move_uploaded_file($fileTemp, ABSPATH . $destination . '/' . $fileName)) {	
									echo "<script> alert('" . addslashes(__('Error, file cannot be moved.', 'mwd-text')) . "');</script>";
									return array($max+1);
								}
								$value = site_url() . '/' . $destination . '/' . $fileName . '*@@url@@*';
								$files['tmp_name'][$file_key]=$destination . "/" . $fileName;
								$temp_file = array( "name" => $files['name'][$file_key], "type" => $files['type'][$file_key], "tmp_name" => $files['tmp_name'][$file_key], "key" => $i);

								array_push($all_files, $temp_file);
							}
						}
						break;
					}
				
					case "type_address": {
						$value = '*#*#*#';
						$element = isset($_POST['wdform_'.$i."_street1".$id]) ? esc_html($_POST['wdform_'.$i."_street1".$id]) : NULL;
						if(isset($element)) {
							$value = $_POST['wdform_'.$i."_street1".$id] . '  ' .$_POST['wdform_'.($i+1)."_street2".$id] . '  ' . $_POST['wdform_'.($i+2)."_city".$id] .'  '. $_POST['wdform_'.($i+3)."_state".$id] .'  '. $_POST['wdform_'.($i+4)."_postal".$id].'  '.$_POST['wdform_'.($i+5)."_country".$id];
							if(in_array($i, $merge_vars)){
								$merge_vars[$i] = $_POST['wdform_'.$i."_street1".$id] . '  ' .$_POST['wdform_'.($i+1)."_street2".$id] . '  ' . $_POST['wdform_'.($i+2)."_city".$id] .'  '. $_POST['wdform_'.($i+3)."_state".$id] .'  '. $_POST['wdform_'.($i+4)."_postal".$id].'  '.$_POST['wdform_'.($i+5)."_country".$id];
							}
							$key +=6;
						}
						break;
					}
				
					case "type_hidden": {
						$value = isset($_POST[$label_label[$key]]) ? esc_html($_POST[$label_label[$key]]) : "";
						break;
					}
				
					case "type_own_select":	{	
						$value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
						if(isset($groupings[$i])){
							if($value){
								array_push($groupings[$i]['groups'], $value);
								$mergeVarGroups[] = $groupings[$i];
							} /* else{
								unset($groupings[$i]);
							} */
						} 
						break;
					}
					
					case "type_radio": {
						$element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
						if(isset($element)) {
							$value = $element;	
						} else{
							$value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
						}
						
						if(isset($groupings[$i])){
							if($value){
								array_push($groupings[$i]['groups'], $value);
								$mergeVarGroups[] = $groupings[$i];
							} /* else{
								unset($groupings[$i]);
							} */
						} 
						
						break;
					}
				
					case "type_checkbox": {
						$value = '';
						$start = -1;
						for($j = 0; $j < 300; $j++) {						
							$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
							if(isset($element)) {
								$start = $j;
								break;
							}
						}
					
						$other_element_id = -1;
						$is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
						if($is_other == "yes") {
							$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
						}
					
						if($start != -1) {
							for($j = $start; $j < 300; $j++) {
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)) {
									if($j == $other_element_id) {
										$value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';
									}
									else {								
										$value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '***br***';
				
										if(isset($groupings[$i])){
											if($_POST['wdform_'.$i."_element".$id.$j]){
												array_push($groupings[$i]['groups'], $_POST['wdform_'.$i."_element".$id.$j]);
												$mergeVarGroups[] = $groupings[$i];
											}
										}
									}
								}
							}
						} /* else{
							unset($groupings[$i]);
						} */
						
						break;
					}
				
					case "type_paypal_price":	{
						$value = isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] ? $_POST['wdform_'.$i."_element".$id] : 0;  
						$total += (float)($value);						
						$paypal_option = array();

						if($value != 0) {
							$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
							array_push ($paypal['item_name'], $label_label[$key]);
							array_push ($paypal['quantity'], $quantity);
							array_push ($paypal['amount'], $value);
							$is_amount=true;
							array_push ($paypal['on_os'], $paypal_option);
						}
						$value = $form_currency.$value;
						break;
					}
				
					case "type_paypal_select": {
						if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] !='') {
							$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : "") . ' : ' . $_POST['wdform_'.$i."_element".$id]. $form_currency;
						}
						else {
							$value = '';
						}
						
						$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
						$total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
						array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
						array_push ($paypal['quantity'], $quantity);
						array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : ""));
						if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
							$is_amount=true;
						}
						$element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
						if(isset($element_quantity) && $value != '') {
							$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
						}
						$paypal_option = array();
						$paypal_option['on'] = array();
						$paypal_option['os'] = array();

						for($k = 0; $k < 50; $k++) {
							$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
							if(isset($temp_val) && $value != '') {
								array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
								array_push ($paypal_option['os'], (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : ""));
								$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . (isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : "") . '***property***';
							}
						}
						array_push ($paypal['on_os'], $paypal_option);
						break;
					}
            
					case "type_paypal_radio": {
						if(isset($_POST['wdform_'.$i."_element_label".$id])) {
							$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
						}
						else {
							$value = '';
						}
						$quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : 1);
						$total += (float)(isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0) * $quantity;
						array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : ""));
						array_push ($paypal['quantity'], $quantity);
						array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : 0));
						if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != 0) {
							$is_amount=true;
						}
				  
						$element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
						if(isset($element_quantity) && $value != '') {
							$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
						}					
				  
						$paypal_option = array();
						$paypal_option['on'] = array();
						$paypal_option['os'] = array();

						for($k = 0; $k < 50; $k++) {
							$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
							if(isset($temp_val) && $value != '') {
								array_push ($paypal_option['on'], (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : ""));
								array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
								$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
							}
						}
						array_push ($paypal['on_os'], $paypal_option);
						break;
					}

					case "type_paypal_shipping": {
						if(isset($_POST['wdform_'.$i."_element_label".$id])) {
							$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;
						}
						else {
							$value = '';
						}
						$value = (isset($_POST['wdform_'.$i."_element_label".$id]) ? $_POST['wdform_'.$i."_element_label".$id] : "") . ' - ' . (isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "") . $form_currency;						
						$paypal['shipping'] = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id]  : "";
						break;
					}

					case "type_paypal_checkbox": {
						$start = -1;
						$value = '';
						for($j = 0; $j < 100; $j++) {						
							$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
							if(isset($element)) {
								$start = $j;
								break;
							}
						}
				  
						$other_element_id = -1;
						$is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
						if($is_other == "yes") {
							$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
						}
				  
						if($start != -1) {
							for($j = $start; $j < 100; $j++) {
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)) {
									if($j == $other_element_id) {
										$value = $value . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '***br***';									
									}
									else {
										$value = $value . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "") . ' - ' . (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] == '' ? '0' : (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "")) . $form_currency . '***br***';
										
										
										$quantity = ((isset($_POST['wdform_' . $i . "_element_quantity" . $id]) && ($_POST['wdform_' . $i . "_element_quantity" . $id] >= 1)) ? $_POST['wdform_'.$i . "_element_quantity" . $id] : 1);
										$total += (float)(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : 0) * (float)($quantity);
										array_push ($paypal['item_name'], $label_label[$key] . ' ' . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : ""));
										array_push ($paypal['quantity'], $quantity);
										array_push ($paypal['amount'], (isset($_POST['wdform_'.$i."_element".$id.$j]) ? ($_POST['wdform_'.$i."_element".$id.$j] == '' ? '0' : $_POST['wdform_'.$i."_element".$id.$j]) : ""));
										if (isset($_POST['wdform_'.$i."_element".$id.$j]) && $_POST['wdform_'.$i."_element".$id.$j] != 0) {
											$is_amount = TRUE;
										}
										$paypal_option = array();
										$paypal_option['on'] = array();
										$paypal_option['os'] = array();

										for($k = 0; $k < 50; $k++) {
											$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
											if(isset($temp_val)) {
												array_push ($paypal_option['on'], isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "");
												array_push ($paypal_option['os'], $_POST['wdform_'.$i."_property".$id.$k]);
											}
										}
										array_push ($paypal['on_os'], $paypal_option);
									}
								}
							}
					
							$element_quantity = isset($_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
							if(isset($element_quantity)) {
								$value .= (isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : "") . ': ' . $_POST['wdform_'.$i."_element_quantity".$id] . '***quantity***';
							}
							for($k = 0; $k < 50; $k++) {
								$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
								if(isset($temp_val)) {
									$value .= '***br***' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $_POST['wdform_'.$i."_property".$id.$k] . '***property***';
								}
							}							
						}

						break;
					}
				
					case "type_star_rating": {
						if(isset($_POST['wdform_'.$i."_selected_star_amount".$id]) && $_POST['wdform_'.$i."_selected_star_amount".$id] == "") {
							$selected_star_amount = 0;
						}
						else {
							$selected_star_amount = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
						}						
						$value = $selected_star_amount . '/' . (isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : "");
						break;
					}
			  
					case "type_scale_rating": {
						$value = (isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0) . '/' . (isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : "");
						break;
					}
				
					case "type_spinner": {
						$value = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : "";
						break;
					}
				
					case "type_slider": {
						$value = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : "";
						break;
					}
				
					case "type_range": {
						$value = (isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : "") . '-' . (isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : "");
						break;
					}
				
					case "type_grading": {
						$value = "";
						$items = explode(":", isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "");
						for($k = 0; $k < sizeof($items) - 1; $k++) {
							$value .= (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : "") . ':';
						}
						$value .= (isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : "") . '***grading***';				
						break;
					}
				
					case "type_matrix": {
						$rows_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
						$rows_count = sizeof($rows_of_matrix) - 1;
						$column_of_matrix = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
						$columns_count = sizeof($column_of_matrix) - 1;						
				
						if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "radio") {
							$input_value = "";
							for($k = 1; $k <= $rows_count; $k++) {
								$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0) . "***";
							}
						}
						if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "checkbox") {
							$input_value = "";							
							for($k = 1; $k <= $rows_count; $k++) {
								for($j = 1; $j <= $columns_count; $j++) {
									$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : 0)."***";
								}
							}
						}
				  
						if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "text") {
							$input_value = "";
							for($k = 1; $k <= $rows_count; $k++) {
								for($j = 1; $j <= $columns_count; $j++) {
									$input_value .= (isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? esc_html($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) : "") . "***";
								}
							}
						}
				  
						if(isset($_POST['wdform_'.$i."_input_type".$id]) && $_POST['wdform_'.$i."_input_type".$id] == "select") {
							$input_value = "";
							for($k = 1; $k <= $rows_count; $k++) {
								for($j = 1; $j <= $columns_count; $j++) {
									$input_value .= (isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "") . "***";	
								}
							}
						}
				  
						$value = $rows_count . (isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "") . '***' . $columns_count . (isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "") . '***' . (isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "") . '***' . $input_value . '***matrix***';
						break;
					}
				}

				if(	$value == '*#*#*#') {
					continue;
				}
				
				if(in_array($i, $merge_vars)){
					$merge_var_key = array_search($i, $merge_vars);
					$merge_vars[$merge_var_key] = str_replace(array("*@@url@@*", "***grading***", "***br***"), array(" ", "", " ", " ", " ", ", "), stripslashes($value));
	
				}
				
				if($type == "type_text" or $type == "type_password" or $type == "type_textarea" or $type == "type_submitter_mail" or $type == "type_number") {					
					$untilupload = $form->form_fields;
					$untilupload = substr($untilupload, strpos($untilupload, $i.'*:*id*:*'.$type), -1);
					$untilupload = substr($untilupload, 0, strpos($untilupload, '*:*new_field*:'));
					$untilupload = explode('*:*w_required*:*', $untilupload);
					$untilupload = $untilupload[1];
					$untilupload = explode('*:*w_unique*:*', $untilupload);
					$unique_element = $untilupload[0];
					if(strlen($unique_element)>3)
						$unique_element = substr($unique_element, -3);
			
					if($unique_element == 'yes') {						
						$unique = $wpdb->get_col($wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "mwd_forms_submits WHERE form_id= %d  and element_label= %s and element_value= %s", $id, $i, addslashes($value)));
						if ($unique) {
							echo "<script> alert('" . addslashes(__('This field %s requires a unique entry and this value was already submitted.', 'mwd-text')) . "'.replace('%s','" . $label_label[$key] . "'));</script>";
							return array($max + 1);
						}
					}
				}
				
				$save_or_no = TRUE;
				if ($form->savedb) {
					$save_or_no = $wpdb->insert($wpdb->prefix . "mwd_forms_submits", array(
						'form_id' => $id,
						'element_label' => $i,
						'element_value' => stripslashes($value),
						'group_id' => ($max + 1),
						'date' => date('Y-m-d H:i:s'),
						'ip' => $_SERVER['REMOTE_ADDR'],
						'user_id_wd' => $current_user->ID,
					), array(
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%d'
					));
				}
				if (!$save_or_no) {
					return FALSE;
				}
				$chgnac = FALSE;
			}
		}

		$str = '';
		if ($form->paypal_mode)	{
			if ($paypal['item_name'])	{
				if ($is_amount)	{
					$tax = $form->tax;
					$currency = $form->payment_currency;
					$business = $form->paypal_email;
					$ip = $_SERVER['REMOTE_ADDR'];       
					$total2 = round($total, 2);
					$save_or_no = $wpdb->insert($wpdb->prefix . "mwd_forms_submits", array(
						'form_id' => $id,
						'element_label' => 'item_total',
						'element_value' => $total2 . $form_currency,
						'group_id' => ($max + 1),
						'date' => date('Y-m-d H:i:s'),
						'ip' => $ip,
						'user_id_wd' => $current_user->ID,
					), array(
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%d'
					));
					if (!$save_or_no) {
						return false;
					}
					$total = $total + ($total * $tax) / 100;
					if (isset($paypal['shipping'])) {
						$total = $total + $paypal['shipping'];
					}
					$total = round($total, 2);        
					$save_or_no = $wpdb->insert($wpdb->prefix . "mwd_forms_submits", array(
						'form_id' => $id,
						'element_label' => 'total',
						'element_value' => $total . $form_currency,
						'group_id' => ($max + 1),
						'date' => date('Y-m-d H:i:s'),
						'ip' => $ip,
						'user_id_wd' => $current_user->ID,
					), array(
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%d'
					));
					if (!$save_or_no) {
						return false;
					}
					$save_or_no = $wpdb->insert($wpdb->prefix . "mwd_forms_submits", array(
						'form_id' => $id,
						'element_label' => '0',
						'element_value' => 'In progress',
						'group_id' => ($max + 1),
						'date' => date('Y-m-d H:i:s'),
						'ip' => $ip,
						'user_id_wd' => $current_user->ID,
					), array(
						'%d',
						'%s',
						'%s',
						'%d',
						'%s',
						'%s',
						'%d'
					));
					if (!$save_or_no) {
						return false;
					}
					
					if ($form->checkout_mode == "production") {
						$str .= "https://www.paypal.com/cgi-bin/webscr?";
					}
					else {
						$str .= "https://www.sandbox.paypal.com/cgi-bin/webscr?";
					}
					$str .= "currency_code=" . $currency;
					$str .= "&business=" . urlencode($business);
					$str .= "&cmd=" . "_cart";
					$str .= "&notify_url=" . admin_url('admin-ajax.php?action=mwdcheckpaypal%26form_id=' . $id . '%26group_id=' . ($max + 1));
					$str .= "&upload=" . "1";
					$str .= "&charset=UTF-8";
					if (isset($paypal['shipping'])) {
						$str = $str . "&shipping_1=" . $paypal['shipping'];
						$str = $str."&no_shipping=2";
					}
					$i=0;
					foreach ($paypal['item_name'] as $pkey => $pitem_name) {
						if($paypal['amount'][$pkey]) {
							$i++;
							$str = $str."&item_name_".$i."=".urlencode($pitem_name);
							$str = $str."&amount_".$i."=".$paypal['amount'][$pkey];
							$str = $str."&quantity_".$i."=".$paypal['quantity'][$pkey];
							if ($tax) {
								$str = $str . "&tax_rate_" . $i . "=" . $tax;
							}
							if ($paypal['on_os'][$pkey]) {
								foreach ($paypal['on_os'][$pkey]['on'] as $on_os_key => $on_item_name) {
									$str = $str."&on".$on_os_key."_".$i."=".$on_item_name;
									$str = $str."&os".$on_os_key."_".$i."=".$paypal['on_os'][$pkey]['os'][$on_os_key];
								}
							}
						}
					}
				}
			}
		}

		$merge_vars['groupings'] = $mergeVarGroups;
		
		unset($_SESSION['hash']);
		unset($_SESSION['unique']);
		unset($_SESSION['gid']);
		$_SESSION['gid'] = $max + 1;
		$_SESSION['hash'] = $_SESSION['unique']= md5($ip.rand());	
		if($form->double_optin && !$form->use_mailchimp_email){	
			$optin_confirmation = get_option('mwd_confirmation-'.$id, array());
			$optin_confirmation[$max + 1][$email_field_key] = $_SESSION['hash'];
			$optin_confirmation[$max + 1]['merge_vars'] = $merge_vars;
			update_option('mwd_confirmation-'.$id, $optin_confirmation);
		}
		if(strpos($form->final_welcome_email, "%unsubscribe_link%") > -1){
			$unsubscribe = get_option('mwd_unsubscribe', array());
			$unsubscribe[$id][$max + 1] = $_SESSION['unique'];
			update_option('mwd_unsubscribe', $unsubscribe);
		}

		if ($chgnac) {
			if ($form->submit_text_type != 5) {
				if($form->empty_submit_message) {
					$_SESSION['mwd_message_after_submit' . $id] = array('msg' => addslashes(__($form->empty_submit_message, 'mwd-text')), 'type' => 'error');
				} else {
					$_SESSION['mwd_message_after_submit' . $id] = array('msg' => addslashes(__('Nothing was submitted.', 'mwd-text')), 'type' => 'error');
				}
			}

			wp_redirect($_SERVER["REQUEST_URI"]);
			exit;
		}
		

		return array($all_files, $str, $merge_vars, ($max + 1), $fparams);
	}

	public function save_mailchimp($id, $merge_vars, $str, $counter, $all_files, $group_id, $fparams) {
		global $wpdb;
		
		$https = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://');
		$form = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id= %d", $id));
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);	

		$subscribe_to_mailchimp = $fparams['action_field'] ? isset($fparams['subscribe_action']) : true;
		$list_ids = $fparams['list_choice'] ? (isset($fparams['list_ids']) ? $fparams['list_ids'] : array()) : explode(',', $form->list_id);
		$use_mailchimp_email = $form->use_mailchimp_email;
		$double_optin = $form->double_optin;
		$update_existing = $form->update_subscriber;
		$send_welcome = $form->welcome_email;
		$replace_interests = $form->replace_interest_groups;
		$delete_member = $form->delete_member;
		$email_type = $form->mail_mode_user == 1 ? 'html' : 'text';
		$send_goodbye = $use_mailchimp_email == 1 ? $form->send_goodbye : 0;
		$send_notify = $use_mailchimp_email == 1 ? $form->send_notify : 0;
		$response_data = '';
		/* $merge_vars_messages = $GLOBALS['merge_vars_messages']; form field label not mailchimp field */
	
		if($fparams['email_field'] && $subscribe_to_mailchimp && $list_ids){
			$subscribe_action = $fparams['action_field'] ? ($fparams['subscribe_action'] == 'subscribe' ? 1 : 0) : $form->subscribe_action;

			foreach($list_ids as $list_id) {
				$updating = false;
				if($subscribe_action == 1) {	
					$mwd_merge_vars = $api->call( 'lists/merge-vars' , array( 'apikey' => $apikey , 'id' => array($list_id ) ) );
					$mwd_merge_vars = $mwd_merge_vars['data'][0]['merge_vars'];
					$req_merges = array();
					foreach($mwd_merge_vars as $mwd_merge_var){
						if($mwd_merge_var['req']){
							$req_merges[$mwd_merge_var['tag']] = $mwd_merge_var['name'];
						}
					}
					
					if($use_mailchimp_email == 1) {
						$_params = array( 
							'api_key' => $apikey,
							'id' => $list_id,
							'email' => array( 'email' => sanitize_email($merge_vars['EMAIL'])),
							'merge_vars' => $merge_vars,
							'email_type' => $email_type,
							'double_optin' => $double_optin,
							'update_existing' => $update_existing,
							'send_welcome' => $send_welcome,
							'replace_interests' => $replace_interests
						);

						try {
							$response_data = $api->call('/lists/subscribe', $_params );
					
							if($form->success_message) {
								if($update_existing != 1)
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'success_message', $response_data), 'type' => 'success');
								else{
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'update_message', $response_data), 'type' => 'success');
								}
							}
						} catch ( Exception $error ) { 
							$error_response = $error->getMessage();
							if (strpos( $error_response, 'should include an email') !== false && $merge_vars['EMAIL'] !== NULL) {
								if($form->invalid_email_message) {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->invalid_email_message, 'type' => 'error');
								}
							} else if ( strpos( $error_response, 'already subscribed' ) !== false ) {
								if($form->already_subscribed_message) {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->already_subscribed_message, 'type' => 'error');
								}
							}
							else if ( strpos( $error_response, 'must be provided' ) !== false ) {
								if($req_merges){
									foreach($req_merges as $req_merge_tag => $req_merge){
										if(!$merge_vars[$req_merge_tag]){
											if($form->required_message) {
												$_SESSION['mwd_message_after_submit' . $id] = array('msg' => sprintf(__($form->required_message, 'mwd-text'), $req_merge), 'type' => 'error');
											} 
										}
									}
								} else{
									if($form->required_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => sprintf(__($form->required_message, 'mwd-text'), $error_label), 'type' => 'error');
									}	
								}
							}			
							else if ( strpos( $error_response, 'has unsubscribed' ) !== false ) {
								$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $error_response, 'type' => 'error');
							} else {
								if($form->gen_error_message) {
								$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->gen_error_message, 'type' => 'error');
								} else {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $error_response, 'type' => 'error');
								}
							}
							
							$this->remove($group_id);
							$_SESSION['error_occurred' . $id] = 1;
							$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
							wp_redirect($redirect_url);
							exit;
						}	
					} 
					else {
						if($double_optin != 1) {
							$_params = array( 
								'api_key' => $apikey,
								'id' => $list_id,
								'email' => array( 'email' => sanitize_email($merge_vars['EMAIL'])),
								'merge_vars' => $merge_vars,
								'double_optin' => 0,
								'update_existing' => $update_existing,
								'send_welcome' => 0,
								'replace_interests' => $replace_interests
							);
						
							try {
								$response_data = $api->call('/lists/subscribe', $_params );
								
								if($form->success_message) {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'success_message', $response_data), 'type' => 'success');
								} else {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => ($use_mailchimp_email == 1 && $double_optin == 1) ? __( "Thank you for subscribing! Check your email for the confirmation message." , 'mwd-text' ) : __( "Thank you for subscribing!" , 'mwd-text'), 'type' => 'success');
								}
								
							} catch ( Exception $error ) { 
								$error_response = $error->getMessage();
								
								if (strpos( $error_response, 'should include an email') !== false && $merge_vars['EMAIL'] !== NULL) {
									if($form->invalid_email_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->invalid_email_message, 'type' => 'error');
									}
								} else if ( strpos( $error_response, 'already subscribed' ) !== false ) {
									if($form->already_subscribed_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->already_subscribed_message, 'type' => 'error');
									}		
								}
								else if ( strpos( $error_response, 'must be provided' ) !== false ) {
									$error_key = substr( $error_response, 0, strpos( $error_response, ' must be provided' ));
									$error_label = isset($req_merges[$error_key]) ? $req_merges[$error_key] : $error_key;
									if($form->required_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => sprintf(__($form->required_message, 'mwd-text'), $error_label), 'type' => 'error');
									} 		
								}
								else { 
									if($form->gen_error_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->gen_error_message, 'type' => 'error');
									} else{
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $error_response, 'type' => 'error');
									}
								}
								$this->remove($group_id);
								$_SESSION['error_occurred' . $id] = 1;
								$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
								wp_redirect($redirect_url);
								exit;
							}	
						}
						else{
							$err = false;
							if($merge_vars['EMAIL'] === NULL){
								if($form->gen_error_message) {
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->gen_error_message, 'type' => 'error');
								} 
								$err = true;
							}
							else {
								if($req_merges){
									foreach($req_merges as $req_merge_tag => $req_merge){
										if(!$merge_vars[$req_merge_tag]){
											if($form->required_message) {
												$_SESSION['mwd_message_after_submit' . $id] = array('msg' => sprintf(__($form->required_message, 'mwd-text'), $req_merge), 'type' => 'error');
											} 
											$err = true;
										}
									}
								}
								
								$emails_list = array();
								$subscribers_list = $api->call('lists/members', array( 'id' => $list_id));
								if($subscribers_list['total']){
									$emails_list = array_map(function($member) { return $member['email']; }, $subscribers_list['data']);
								}
					
								if(in_array($merge_vars['EMAIL'], $emails_list) && $update_existing !=1){
									if($form->already_subscribed_message) {
										$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->already_subscribed_message, 'type' => 'error');
									} 
									$err = true;
								}

								if(!filter_var($merge_vars['EMAIL'], FILTER_VALIDATE_EMAIL)){
									if($row->invalid_email_message) {
										$confirmation_message = array('msg' => $row->invalid_email_message, 'type' => 'error');
									} 
									$err = true;
								}
							} 
							
							if($err){
								$this->remove($group_id);
								$_SESSION['error_occurred' . $id] = 1;
								$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
								wp_redirect($redirect_url);
								exit;
							} else {
								if(in_array($merge_vars['EMAIL'], $emails_list) && $update_existing){
									$updating = true;
									$_params = array( 
										'api_key' => $apikey,
										'id' => $list_id,
										'email' => array( 'email' => sanitize_email($merge_vars['EMAIL'])),
										'merge_vars' => $merge_vars,
										'double_optin' => 0,
										'update_existing' => 1,
										'send_welcome' => 0,
										'replace_interests' => $replace_interests
									);
								
									try {
										$response_data = $api->call('/lists/subscribe', $_params );
										if($form->success_message) {
											$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'update_message', $response_data), 'type' => 'success');
										} 
										
									} catch ( Exception $error ) { 
										$error_response = $error->getMessage();
										if ( strpos( $error_response, 'must be provided' ) !== false ) {
											$error_key = substr( $error_response, 0, strpos( $error_response, ' must be provided' ));
											$error_label = isset($req_merges[$error_key]) ? $req_merges[$error_key] : $error_key;
											if($form->required_message) {
												$_SESSION['mwd_message_after_submit' . $id] = array('msg' => sprintf(__($form->required_message, 'mwd-text'), $error_label), 'type' => 'error');
											} 		
										}
										else { 
											if($form->gen_error_message) {
												$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->gen_error_message, 'type' => 'error');
											}
										}
										$this->remove($group_id);
										$_SESSION['error_occurred' . $id] = 1;
										$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
										wp_redirect($redirect_url);
										exit;
									}	
								
								} else{
									$response_data = array('email' => sanitize_email($merge_vars['EMAIL']), 'euid' => 0, 'leid' => 0);
									$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'success_message', $response_data), 'type' => 'success');
								}
							}
						}
					}
				}
				else {
					$_params = array( 
						'api_key' => $apikey,
						'id' => $list_id,
						'email' => array('email' => sanitize_email($merge_vars['EMAIL'])),
						'delete_member' => $delete_member,
						'send_goodbye' => $send_goodbye,
						'send_notify' => $send_notify
					);

					try {
						$response_data = $api->call('/lists/unsubscribe', $_params);
						$response_data['email'] = sanitize_email($merge_vars['EMAIL']);
				
						if($form->unsubscribe_message) {
							$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $this->get_message($id, 'unsubscribe_message', $response_data), 'type' => 'success');
						} 
					} catch ( Exception $error ) { 
						$error_response = $error->getMessage();
						
						if (strpos( $error_response, 'no record of the email') !== false) {
							if($form->invalid_email_message) {
								$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->invalid_email_message, 'type' => 'error');
							}
						} else if ( strpos( $error_response, 'is not subscribed' ) !== false ) {
							if($form->not_subscribed_message) {
								$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->not_subscribed_message, 'type' => 'error');
							}			
						}
						else { 
							if($form->gen_error_message) {
								$_SESSION['mwd_message_after_submit' . $id] = array('msg' => $form->gen_error_message, 'type' => 'error');
							} 
						}
						
						$this->remove($group_id);
						$_SESSION['error_occurred' . $id] = 1;
						$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
						wp_redirect($redirect_url);
						exit;
					} 
				}
			}
		}
		else{
			$_SESSION['mwd_message_after_submit' . $id] = array('msg' => addslashes(__('Your form was successfully submitted.', 'mwd-text')), 'type' => 'success');
		}

		//gen mail here
		if($use_mailchimp_email != 1)
			$this->gen_mail($counter, $all_files, $id, $str, $response_data, $subscribe_action, $updating);

		switch ($form->submit_text_type) {
			case "3":
			case "4": {
				if($form->type == 'popover' || $form->type == 'topbar'){
					$_SESSION['hide_form_after_submit' . $id] = 1;
				}
				if ($form->article_id) {
					$redirect_url = $form->article_id;
				}
				else {
					$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				}
				break;
			}
			case "1": {
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
			case "5": {
				if($form->type == 'popover' || $form->type == 'topbar'){
					$_SESSION['hide_form_after_submit' . $id] = 1;
				}
				$redirect_url = $form->url;
				break;
			}
			default: {
				$_SESSION['hide_form_after_submit' . $id] = 1;
				$redirect_url = $https . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				break;
			}
		}
		
		if (!$str) {
			wp_redirect($redirect_url);
			exit;
		}
		else {
			$_SESSION['redirect_paypal'.$id] = 1;
			$str .= "&return=" . urlencode($redirect_url);
			wp_redirect($str);
			exit;
		}

	}	
	
	public function gen_mail($counter, $all_files, $id, $str, $response_data, $subscribe_action, $updating) {
        global $wpdb; 
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);	
		$replyto = '';
		$label_order_original = array();
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id=%d", $id));
		$merge_vars = json_decode(html_entity_decode($row->merge_variables), true);
		$update_existing = $row->update_subscriber;
		$replace_interests = $row->replace_interest_groups;
		

		$label_all = explode('#****#', $row->label_order);    
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
		}
	
		$all_form_variables = $this->gen_custom_fields($id, $response_data, $all_files);
		$list = $all_form_variables['all'][1];
		$list_text_mode = $all_form_variables['all'][0];
		$custom_fields = $all_form_variables['custom_fields'];
		$special_fields = $all_form_variables['special_fields'];

		/* send email to subscriber */
		$send_user = $subscribe_action == 1 ? (!$updating && ($row->double_optin || (!$row->double_optin && $row->welcome_email))) : $row->send_goodbye;
		if($send_user){
			$fromname_user = $row->mail_from_name_user;  
			$subject_user = $subscribe_action == 1 ? ($row->double_optin ? ($row->mail_subject_user ? $row->mail_subject_user : $row->title) : ($row->mail_subject_user_final ? $row->mail_subject_user_final : $row->title)) : $row->mail_subject_unsubscribe;
			$replyto_user = $row->reply_to_user ? $row->reply_to_user : '';
		
			$attachment_user = array(); 	
			if ($row->mail_attachment_user) {
				for ($k = 0; $k < count($all_files); $k++) {
					if (isset($all_files[$k]['tmp_name'])) {
						$attachment_user[$k] = $all_files[$k]['tmp_name'];
					}
				}
			}
			
			$email_content = $subscribe_action == 1 ? ($row->double_optin ? $row->optin_confirmation_email : $row->final_welcome_email) : $row->goodbye_email;
			if ($row->mail_mode_user) {
				$content_type = "text/html";
				$mode = 1;
				$list_user = wordwrap($list, 70, "\n", true);
				$new_script = wpautop($email_content);
			}	
			else {
				$content_type = "text/plain";
				$mode = 0; 
				$list_user = wordwrap($list_text_mode, 1000, "\n", true);
				$new_script = str_replace(array('<p>','</p>'), '', $email_content);
			}
			
			$special_fields['all'] = $list_user;
			foreach($special_fields as $key => $special_field) {
				if(strpos($new_script, "%".$key."%") > -1)
					$new_script = str_replace("%".$key."%", $special_field, $new_script);

				if(strpos($fromname_user, "%".$key."%") > -1)
					$fromname_user = str_replace("%".$key."%", $special_field, $fromname_user);
				
				if(strpos($subject_user, "%".$key."%") > -1)
					$subject_user = str_replace("%".$key."%", $special_field, $subject_user);
			}	
			
			foreach($label_order_original as $key => $label_each) {
				if(strpos($new_script, "%".$label_each."%") > -1) {
					$new_script = str_replace("%".$label_each."%", $custom_fields[$key], $new_script);
				}
				
				if(strpos($fromname_user, "%".$label_each."%")>-1) {	
					$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
					$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
					$fromname_user = str_replace("%".$label_each."%", $new_value, $fromname_user);							
				}	
				
				if(strpos($subject_user, "%".$label_each."%")>-1) {	
					$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
					$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
					$subject_user = str_replace("%".$label_each."%", $new_value, $subject_user);							
				}
			}
			
			$recipient = $response_data['email'];
			$cca = $row->mail_cc_user;
			$bcc = $row->mail_bcc_user;
			$from = '';
			if ($row->mail_from_user ) {
				if ($fromname_user != '') {
					$from = "From: '" . $fromname_user . "' <" . str_replace("%list_from_email%", $special_fields['list_from_email'], $row->mail_from_user) . ">" . "\r\n";
				}	
				else {
					$from = "From: '' <" .str_replace("%list_from_email%", $special_fields['list_from_email'], $row->mail_from_user) . ">" . "\r\n";
				}
			}

			$headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
			if ($replyto_user) {
				$headers .= "Reply-To: <" .  str_replace("%list_from_email%", $special_fields['list_from_email'], $replyto_user) . ">\r\n";
			}
			if ($cca) {
				$headers .= "Cc: " . $cca . "\r\n";          
			}
			if ($bcc) {
				$headers .= "Bcc: " . $bcc . "\r\n";          
			}

			$body = $new_script;
			$send_copy = isset($_POST["wdform_send_copy_".$id]) ? $_POST["wdform_send_copy_".$id] : NULL;
			if(isset($send_copy)) {
				$send = true;
			}
			else {
				if($subscribe_action == 1){
					$optin_confirmation_post_id = (int)$wpdb->get_var($wpdb->prepare('SELECT optin_confirmation_post_id FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $id));
					$confirmation_link = get_post( $optin_confirmation_post_id );
					if(strpos($new_script, "%confirmation_link%") > -1 && $confirmation_link !== NULL) {
						$conf_link = add_query_arg(array('gid' => $_SESSION['gid'], 'h' => $_SESSION['hash'].'@'.$merge_vars['EMAIL'], 'form_id' => $id), get_post_permalink($optin_confirmation_post_id));
						
						$body = $row->double_optin && !$row->use_mailchimp_email ? str_replace("%confirmation_link%", $conf_link, $new_script) : str_replace("%confirmation_link%", '', $new_script);
					}
					
					set_transient('mwd-custom-vars'.$id.'-'.$_SESSION['gid'], array('all' => $all_form_variables['all'][$mode], 'custom_fields' => $all_form_variables['custom_fields'], 'special_fields' => $all_form_variables['special_fields'], 0));
					
					$unsubscribe_post_id = (int)$wpdb->get_var($wpdb->prepare('SELECT unsubscribe_post_id FROM ' . $wpdb->prefix . 'mwd_forms WHERE id="%d"', $id));
					$unsubscribe_link = get_post( $unsubscribe_post_id );
					if(strpos($new_script, "%unsubscribe_link%") > -1 && $unsubscribe_link !== NULL) {
						$unsub_link = add_query_arg(array('gid' => $_SESSION['gid'], 'u' => $_SESSION['unique'], 'email' => $recipient, 'list_ids' => $row->list_id, 'form_id' => $id), get_post_permalink($unsubscribe_post_id));
						
						$body = str_replace("%unsubscribe_link%", $unsub_link, $new_script);
					}
				}
				
	
				if($recipient) {
					$send = wp_mail(str_replace(' ', '', $recipient), $subject_user, stripslashes($body), $headers, $attachment_user);
				}
				
			}
		}

		
		/* send email to administrator */
		if($row->reply_to) {
			$replyto = isset($_POST['wdform_'.$row->reply_to."_element".$id]) ? $_POST['wdform_'.$row->reply_to."_element".$id] : NULL;
			if(!isset($replyto)) {
				$replyto = $row->reply_to;
			}
		}
		$recipient = $row->mail;
		$subject = $row->mail_subject ?  $row->mail_subject : $row->title;
		$fromname = $row->from_name ? $row->from_name : '';

		$attachment = array(); 
		if ($row->mail_attachment) {
			for ($k = 0; $k < count($all_files); $k++) {
				if (isset($all_files[$k]['tmp_name'])) {
					$attachment[$k] = $all_files[$k]['tmp_name'];
				}
			}
		}
		
		if ($row->mail_mode) {
			$content_type = "text/html";
			$mode = 1; 
			$list = wordwrap($list, 70, "\n", true);
			$new_script = wpautop($row->script_mail);
		}	
		else {
			$content_type = "text/plain";
			$mode = 0; 
			$list = wordwrap($list_text_mode, 1000, "\n", true);
			$new_script = str_replace(array('<p>','</p>'),'',$row->script_mail);
		}
			
		$special_fields['all'] = $list;
		foreach($special_fields as $key => $special_field) {
			if(strpos($new_script, "%".$key."%") > -1)
				$new_script = str_replace("%".$key."%", $special_field, $new_script);

			if(strpos($fromname, "%".$key."%") > -1)
				$fromname = str_replace("%".$key."%", $special_field, $fromname);
			
			if(strpos($subject, "%".$key."%") > -1)
				$subject = str_replace("%".$key."%", $special_field, $subject);
		}	
		
		foreach($label_order_original as $key => $label_each) {
			if(strpos($new_script, "%".$label_each."%") > -1) {
				$new_script = str_replace("%".$label_each."%", $custom_fields[$key], $new_script);
			}
			
			if(strpos($fromname, "%".$label_each."%")>-1) {	
				$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
				$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
				$fromname = str_replace("%".$label_each."%", $new_value, $fromname);							
			}	
			
			if(strpos($subject, "%".$label_each."%")>-1) {	
				$new_value = str_replace('<br>', ', ', $custom_fields[$key]);		
				$new_value = substr($new_value, -2) == ', ' ? substr($new_value, 0, -2) : $new_value;
				$subject = str_replace("%".$label_each."%", $new_value, $subject);							
			}
		}
			
		$from = '';
		if ($row->from_mail) {
			$from = isset($_POST['wdform_'.$row->from_mail."_element".$id]) ? $_POST['wdform_'.$row->from_mail."_element".$id] : NULL;
			if (!isset($from)) {
				$from = $row->from_mail;
			}
			$from = "From: '" . $fromname . "' <" . str_replace("%list_from_email%", $special_fields['list_from_email'], $from) . ">" . "\r\n";
		}
		
		$headers =  $from . " Content-Type: " . $content_type . "; charset=\"" . get_option('blog_charset') . "\"\n";
		if ($replyto) {
			$headers .= "Reply-To: <" . str_replace("%list_from_email%", $special_fields['list_from_email'], $replyto) . ">\r\n";
		}
		$cca = $row->mail_cc;
		$bcc = $row->mail_bcc;
		if ($cca) {
			$headers .= "Cc: " . $cca . "\r\n";          
		}
		if ($bcc) {
			$headers .= "Bcc: " . $bcc . "\r\n";          
		}
	
		$admin_body = $new_script;
		
		if($recipient) {
			$send = wp_mail(str_replace(' ', '', $recipient), $subject, stripslashes($admin_body), $headers, $attachment);
		}
		
		if($row->mail) {
			if($send) {
				if ($send !== true ) {
					$_SESSION['mwd_message_after_submit' . $id] = array('msg' => addslashes(__('Error, email was not sent.', 'mwd-text')), 'type' => 'error');
				}
			}
		}

	}

	public function gen_custom_fields($id, $response_data, $all_files = array()) {
		global $wpdb;
		$form_currency = '$';
		$current_user =  wp_get_current_user();
		$userid = $current_user->ID != 0 ? $current_user->ID : '';
		$username = $current_user->ID != 0 ? $current_user->display_name : '';
		$useremail = $current_user->ID != 0 ? $current_user->user_email : '';

		$ip = $_SERVER['REMOTE_ADDR'];
		$all_form_variables = array();
		$special_fields = array();
		$label_order_original = array();
		$label_order_ids = array();
		$label_label = array();
		$label_type = array();
		
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id=%d", $id));
		$subid = $wpdb->get_var("SELECT MAX( group_id ) FROM " . $wpdb->prefix ."mwd_forms_submits" );
		
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);	
	
		$mchlists = $api->call( 'lists/list' , array( 'apikey' => $apikey) );
		
		$profile_info = $api->call( '/users/profile' , array( 'apikey' => $apikey ) );
		$mwd_list = $api->call( 'lists/list' , array( 'apikey' => $apikey, 'filters' => array('list_id' => $row->list_id)) );
		$mwd_list = $mwd_list['data'][0];
		
		$list_id = $mwd_list['id'];
		$list_name = $mwd_list['name'];
		$list_subject = $mwd_list['default_subject'];
		$list_from_email = $mwd_list['default_from_email'];
		$list_from_name = $mwd_list['default_from_name'];
		$list_owner_email = $profile_info['email'];
		$user_mailchimp_id = $row->subscribe_action == 1 && isset($response_data['euid']) ? $response_data['euid'] : '';
		
		$merge_vars = json_decode(html_entity_decode($row->merge_variables), true);
		
		$currency_code = array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'MXN', 'HKD', 'HUF', 'NOK', 'NZD', 'SGD', 'SEK', 'PLN', 'AUD', 'DKK', 'CHF', 'CZK', 'ILS', 'BRL', 'TWD', 'MYR', 'PHP', 'THB');
		$currency_sign = array('$', '&#8364;', '&#163;', '&#165;', 'C$', 'Mex$', 'HK$', 'Ft', 'kr', 'NZ$', 'S$', 'kr', 'zl', 'A$', 'kr', 'CHF', 'Kc', '&#8362;', 'R$', 'NT$', 'RM', '&#8369;', '&#xe3f;');
		if ($row->payment_currency) {
			$form_currency = $currency_sign[array_search($row->payment_currency, $currency_code)];
		}
		
		$label_all = explode('#****#', $row->label_order);    
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			array_push($label_order_ids, $label_id);
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
			$label_type[$label_id] = $label_order_each[1];
			array_push($label_label, $label_order_each[0]);
			array_push($label_type, $label_order_each[1]);
		}

		$special_fields = array('ip' => $ip, 'userid' => $userid, 'useremail' => $useremail, 'username' => $username, 'subid' => $subid, 'list_from_email' => $list_from_email, 'list_from_name' => $list_from_name, 'list_name' => $list_name, 'list_id' => $list_id, 'list_subject' => $list_subject, 'user_mailchimp_id' => $user_mailchimp_id, 'list_owner_email' => $list_owner_email);	
	
		$disabled_fields = explode(',', isset($_REQUEST["disabled_fields".$id]) ? $_REQUEST["disabled_fields".$id] : "");
		$disabled_fields = array_slice($disabled_fields,0, count($disabled_fields)-1);   
		
		$list = '<table border="1" cellpadding="3" cellspacing="0" style="width:600px;">';
		$list_text_mode = '';
		$custom_fields = array();
		foreach($label_order_ids as $key => $label_order_id) {
			$i = $label_order_id;
			$type = $label_type[$i];

			if($type == "type_submit_reset" || $type == "type_editor" || $type == "type_captcha" || $type == "type_arithmetic_captcha" || $type == "type_recaptcha" || $type == "type_button")
				continue;
			
				$element_label = $label_order_original[$i];
				if(!in_array($i, $disabled_fields)) {
					switch ($type) {
						case "type_text":
						case "type_password":
						case "type_textarea":
						case "type_date":
						case "type_own_select":					
						case "type_country":				
						case "type_number":{
							$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
							if(isset($element)) {
								if($this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td>' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
									$custom_fields[$i] = $element;
								}
							}	
							break;
						}
						
						case "type_action":{
							$w_type = isset($_POST['w_type'.$id]) ? esc_html($_POST["w_type".$id]) : "";
							if($w_type != 'checkbox' && isset($_POST['wdform_'.$i."_element".$id])){
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element)) {
									$list = $list . '<tr valign="top"><td>' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
									$custom_fields[$i] = $element;
								}	
							} else{
								for($j = 0; $j < 2; $j++) {						
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
									if(isset($element)){
										$list = $list . '<tr valign="top"><td>' . $element_label . '</td><td>' . $element . '</td></tr>';
										$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
										$custom_fields[$i] = $element;
										break;
									}
								}
							}
							
							break;
						} 
						case "type_list":{
							$w_type = isset($_POST['w_type'.$id]) ? esc_html($_POST["w_type".$id]) : "";
							if($w_type != 'checkbox' && isset($_POST['wdform_'.$i."_element".$id])){
								$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
								if(isset($element)) {
									$list = $list . '<tr valign="top"><td>' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
									$custom_fields[$i] = $element;
								}
							} else{
								$list = $list . '<tr valign="top"><td>' . $element_label . '</td><td>';
								$list_text_mode = $list_text_mode . '' . $element_label . ' - ';
								for($j = 0; $j < $mchlists['total']; $j++) {						
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
									if(isset($element)){
										$list = $list  . $element.'<br>';
										$list_text_mode = $list_text_mode.$element.", ";
										$custom_fields[$i] = isset($custom_fields[$i]) ? $custom_fields[$i].$element . '<br>' : $element . '<br>';
									}
								}
								$list = $list . '</td></tr>';
								$list_text_mode = $list_text_mode."\r\n";
							}
							
							break;
						}
						
						case "type_file_upload": {
							if($all_files) {
								foreach($all_files as &$all_file){
									$fileTemp = $all_file['tmp_name'];
									$fileName = $all_file['name'];
									$fieldKey = $all_file['key'];
									
									if($fieldKey == $i){
										$uploadedFileNameParts = explode('.', $fileTemp);
										$uploadedFileExtension = array_pop($uploadedFileNameParts);
										$invalidFileExts = array('gif', 'jpg', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc', 'aiff', 'wbmp', 'xbm' );
										$extOk = false;
										
										foreach($invalidFileExts as $key => $valuee) {
											if(is_numeric(strpos(strtolower($valuee), strtolower($uploadedFileExtension) )) )
												$extOk = true;
										}
										
										if ($extOk == true) 
											$custom_fields[$i] = isset($custom_fields[$i]) ? $custom_fields[$i].'<img src="'.site_url().'/'.$fileTemp.'" alt="'.$fileName.'"/>' : '<img src="'.site_url().'/'.$fileTemp.'" alt="'.$fileName.'"/>';
									}
								}
							
							}	
							break;
						}
						
						case "type_hidden": {
							$element = isset($_POST[$element_label]) ? $_POST[$element_label] : NULL;
							if(isset($element)) {
								if($this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td>' . $element . '</td></tr>';
									$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
									$custom_fields[$i] = $element;
								}
							}
							break;
						}
												
						case "type_submitter_mail": {
							$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
							if(isset($element)) {
								if($this->empty_field($element, $row->mail_emptyfields)) {
									$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
									$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
									
									$custom_fields[$i] = $element;
								}
							}
							break;		
						}						
						
						case "type_time": {							
							$hh = isset($_POST['wdform_'.$i."_hh".$id]) ? $_POST['wdform_'.$i."_hh".$id] : NULL;
							$am_pm = isset($_POST['wdform_'.$i."_am_pm".$id]) ? $_POST['wdform_'.$i."_am_pm".$id] : NULL;
							if(isset($hh)){
								$ss = isset($_POST['wdform_'.$i."_ss".$id]) ? $_POST['wdform_'.$i."_ss".$id] : NULL;
								if(isset($ss)) {
									if($this->empty_field($hh, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_mm".$id], $row->mail_emptyfields) || $this->empty_field($ss, $row->mail_emptyfields)) {
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . $ss;
										$list_text_mode = $list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "").':'.$ss;
										if(isset($am_pm)) {
											$list = $list . ' ' . $am_pm . '</td></tr>';
											$list_text_mode = $list_text_mode.$am_pm."\r\n";
											$custom_fields[$i] = $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . $ss.  $am_pm;
										}
										else {
											$list = $list.'</td></tr>';
											$list_text_mode = $list_text_mode."\r\n";
											$custom_fields[$i] = $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "") . ':' . $ss;
										}
									}	
								}
								else {
									if($this->empty_field($hh, $row->mail_emptyfields) || $this->empty_field($_POST['wdform_'.$i."_mm".$id], $row->mail_emptyfields)) {
										$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : '');
										$list_text_mode = $list_text_mode.$element_label.' - '.$hh.':'.(isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : '');
										
										$custom_fields[$i] = $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : '');
										
										if(isset($am_pm)) {
											$list = $list . ' ' . $am_pm . '</td></tr>';
											$list_text_mode = $list_text_mode.$am_pm."\r\n";
											$custom_fields[$i] = $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
										}
										else {
											$list = $list.'</td></tr>';
											$list_text_mode = $list_text_mode."\r\n";
											$custom_fields[$i] = $hh . ':' . (isset($_POST['wdform_'.$i."_mm".$id]) ? $_POST['wdform_'.$i."_mm".$id] : "");
										}
									}
								}
							}	
							break;
						}
					  				
						case "type_address": {
							$element = isset($_POST['wdform_'.$i."_street1".$id]) ? $_POST['wdform_'.$i."_street1".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							$element = isset($_POST['wdform_'.$i."_street2".$id]) ? $_POST['wdform_'.$i."_street2".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							$element = isset($_POST['wdform_'.$i."_city".$id]) ? $_POST['wdform_'.$i."_city".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							$element = isset($_POST['wdform_'.$i."_state".$id]) ? $_POST['wdform_'.$i."_state".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							$element = isset($_POST['wdform_'.$i."_postal".$id]) ? $_POST['wdform_'.$i."_postal".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							$element = isset($_POST['wdform_'.$i."_country".$id]) ? $_POST['wdform_'.$i."_country".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $label_order_original[$i] . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$label_order_original[$i].' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}
							break;							
						}
						
						case "type_date_fields": {
							$day = isset($_POST['wdform_'.$i."_day".$id]) ? $_POST['wdform_'.$i."_day".$id] : NULL;
							$month = isset($_POST['wdform_'.$i."_month".$id]) ? $_POST['wdform_'.$i."_month".$id] : "";
							$year = isset($_POST['wdform_'.$i."_year".$id]) ? $_POST['wdform_'.$i."_year".$id] : "";
							if(isset($day) && ($this->empty_field($day, $row->mail_emptyfields) || $this->empty_field($month, $row->mail_emptyfields) || $this->empty_field($year, $row->mail_emptyfields))) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' .$day . '-' . $month . '-' . $year. '</td></tr>';
								$list_text_mode=$list_text_mode.$element_label.( $day.'-'.$month.'-'.$year)."\r\n";
								
								$custom_fields[$i] = $day.'-'.$month.'-'.$year;
							}
							break;
						}
						
						case "type_radio": {
							$element = isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
								break;
							}								
							$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
							}
							break;	
						}	
						
						case "type_checkbox": {
							$start = -1;
							for($j = 0; $j < 200; $j++) {
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)) {
									$start = $j;
									break;
								}
							}								
							$other_element_id = -1;
							$is_other = isset($_POST['wdform_'.$i."_allow_other".$id]) ? $_POST['wdform_'.$i."_allow_other".$id] : "";
							if($is_other == "yes") {
								$other_element_id = isset($_POST['wdform_'.$i."_allow_other_num".$id]) ? $_POST['wdform_'.$i."_allow_other_num".$id] : "";
							}
			
							if($start != -1 || ($start == -1 && $row->mail_emptyfields))
							{
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';
								$list_text_mode = $list_text_mode.$element_label.' - '; 
							}
							
							if($start != -1) {
								for($j = $start; $j < 200; $j++) {									
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
									if(isset($element)) {
										if($j == $other_element_id) {
											$list = $list . (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "") . '<br>';
											$list_text_mode = $list_text_mode.(isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "").', ';	
											$custom_fields[$i] =  isset($custom_fields[$i]) ? $custom_fields[$i].(isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "").', ' : (isset($_POST['wdform_'.$i."_other_input".$id]) ? $_POST['wdform_'.$i."_other_input".$id] : "").', ';
										}
										else {									
											$list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '<br>';
											$list_text_mode = $list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "").', ';
											$custom_fields[$i] = isset($custom_fields[$i]) ? $custom_fields[$i].(isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '<br>' : (isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : "") . '<br>';
										}
									}
								}
							}
							
							if($start != -1 || ($start == -1 && $row->mail_emptyfields))
							{
								$list = $list . '</td></tr>';
								$list_text_mode = $list_text_mode."\r\n";
							}	
							break;
						}
						
						case "type_paypal_price":	{
							$value = 0;
							if(isset($_POST['wdform_'.$i."_element".$id])) {
								$value = $_POST['wdform_'.$i."_element".$id];
							}
					
							if($this->empty_field($value, $row->mail_emptyfields) && $value!='.') {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . ($value == '' ? '' : $form_currency) . $value . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$value.$form_currency."\r\n";
								$custom_fields[$i] = $value.$form_currency;
							}	
							break;
						}							
				  
						case "type_paypal_select": {
							$value = '';
							if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id] != '') {
								$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $_POST['wdform_'.$i."_element".$id] . $form_currency;
							
								$element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : '';
								$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
								if(isset($element_quantity)) {
									$value .= '<br/>' . $element_quantity_label . ' : ' . $element_quantity;
								}
							
								for($k = 0; $k < 50; $k++) {
									$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
									if(isset($temp_val)) {			
										$value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : '') . ' : ' . $temp_val;
									}
								}
							}
							
							if($this->empty_field($value, $row->mail_emptyfields)){	
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
								$custom_fields[$i] = $value;
							}	
							break;
						}
				  
						case "type_paypal_radio": {
							$value = '';
							if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id]) {
								$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $_POST['wdform_'.$i."_element".$id] . $form_currency;
						  
								$element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : '';
								$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
								if (isset($element_quantity)) {
									$value .= '<br/>' . $element_quantity_label . ' : ' . $element_quantity;
								}
								for($k = 0; $k < 50; $k++) {
									$temp_val = isset($_POST['wdform_'.$i."_property".$id.$k]) ? $_POST['wdform_'.$i."_property".$id.$k] : NULL;
									if(isset($temp_val)) {
										$value .= '<br/>' . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : '') . ' : ' . $temp_val;
									}
								}
							}

							if($this->empty_field($value, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.str_replace('<br/>',', ',$value)."\r\n";
								$custom_fields[$i] = $value;
							}
							break;	
						}

						case "type_paypal_shipping": {	
							$value = '';						
							if(isset($_POST['wdform_'.$i."_element".$id]) && $_POST['wdform_'.$i."_element".$id]) {
								$value = $_POST['wdform_'.$i."_element_label".$id] . ' : ' . $_POST['wdform_'.$i."_element".$id] . $form_currency;
							}
							
							if($this->empty_field($value, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $value . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$value."\r\n";
								$custom_fields[$i] = $value;
							}		
							break;
						}

						case "type_paypal_checkbox": {
							$start = -1;
							for($j = 0; $j < 200; $j++) {
								$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? $_POST['wdform_'.$i."_element".$id.$j] : NULL;
								if(isset($element)) {
									$start = $j;
									break;
								}
							}	
						
							if($start != -1 || ($start == -1 && $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >';		
								$list_text_mode = $list_text_mode.$element_label.' - ';  
								$custom_fields[$i] = '';
							}
							if($start!=-1) {
								for($j = $start; $j < 200; $j++) {									
									$element = isset($_POST['wdform_'.$i."_element".$id.$j]) ? ($_POST['wdform_'.$i."_element".$id.$j] ? $_POST['wdform_'.$i."_element".$id.$j] : 0) : NULL;
									if(isset($element)) {
										$list = $list . (isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : '') . ' - ' . $element. $form_currency . '<br />';
										$list_text_mode = $list_text_mode.(isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : "").' - '.$element.$form_currency.', ';
										$custom_fields[$i] = $custom_fields[$i].(isset($_POST['wdform_'.$i."_element".$id.$j."_label"]) ? $_POST['wdform_'.$i."_element".$id.$j."_label"] : '') . ' - ' . $element. $form_currency . '<br />';
									}
								}
							
								$element_quantity_label = isset($_POST['wdform_'.$i."_element_quantity_label".$id]) ? $_POST['wdform_'.$i."_element_quantity_label".$id] : '';
								$element_quantity = (isset($_POST['wdform_'.$i."_element_quantity".$id]) && $_POST['wdform_'.$i."_element_quantity".$id]) ? $_POST['wdform_'.$i."_element_quantity".$id] : NULL;
								if (isset($element_quantity)) {
									$list = $list . $element_quantity_label . ' : ' . $element_quantity;
									$list_text_mode = $list_text_mode.$element_quantity_label . ' : ' . $element_quantity.', ';
									$custom_fields[$i] = $custom_fields[$i].$element_quantity_label . ' : ' . $element_quantity.'<br />';								
								}
								for($k = 0; $k < 50; $k++) {
									$temp_val = isset($_POST['wdform_'.$i."_element_property_value".$id.$k]) ? $_POST['wdform_'.$i."_element_property_value".$id.$k] : NULL;
									if(isset($temp_val)) {			
										$list = $list . (isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ' : ' . $temp_val;
										$list_text_mode = $list_text_mode.(isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ': ' . $temp_val.', ';	
										$custom_fields[$i] = $custom_fields[$i].(isset($_POST['wdform_'.$i."_element_property_label".$id.$k]) ? $_POST['wdform_'.$i."_element_property_label".$id.$k] : "") . ' : ' . $temp_val.'<br />';	
									}
								}
							}
							if($start != -1 || ($start == -1 && $row->mail_emptyfields)) {
								$list = $list . '</td></tr>';
								$list_text_mode = $list_text_mode."\r\n";	
							}
							break;
						}
					  
						case "type_paypal_total": {
							$element = isset($_POST['wdform_'.$i."_paypal_total".$id]) ? $_POST['wdform_'.$i."_paypal_total".$id] : '';
							if($this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
							}	
							break;
						}
					
						case "type_star_rating": {
							$element = isset($_POST['wdform_'.$i."_star_amount".$id]) ? $_POST['wdform_'.$i."_star_amount".$id] : NULL;
							$selected = isset($_POST['wdform_'.$i."_selected_star_amount".$id]) ? $_POST['wdform_'.$i."_selected_star_amount".$id] : 0;
							if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
								$custom_fields[$i] = $selected.'/'.$element;
							}
							break;
						}
					  
						case "type_scale_rating": {
							$element = isset($_POST['wdform_'.$i."_scale_amount".$id]) ? $_POST['wdform_'.$i."_scale_amount".$id] : NULL;
							$selected = isset($_POST['wdform_'.$i."_scale_radio".$id]) ? $_POST['wdform_'.$i."_scale_radio".$id] : 0;
							if(isset($element) && $this->empty_field($selected, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $selected . '/' . $element . '</td></tr>';	
								$list_text_mode = $list_text_mode.$element_label.' - '.$selected.'/'.$element."\r\n";
								$custom_fields[$i] = $selected.'/'.$element;
							}
							break;
						}
					  
						case "type_spinner": {
							$element = isset($_POST['wdform_'.$i."_element".$id]) ? $_POST['wdform_'.$i."_element".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
							}
							break;
						}
					  
						case "type_slider": {
							$element = isset($_POST['wdform_'.$i."_slider_value".$id]) ? $_POST['wdform_'.$i."_slider_value".$id] : NULL;
							if(isset($element) && $this->empty_field($element, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
							}
							break;
						}
					  
						case "type_range": {
							$element0 = isset($_POST['wdform_'.$i."_element".$id.'0']) ? $_POST['wdform_'.$i."_element".$id.'0'] : NULL;
							$element1 = isset($_POST['wdform_'.$i."_element".$id.'1']) ? $_POST['wdform_'.$i."_element".$id.'1'] : NULL;
							if((isset($element0) && $this->empty_field($element0, $row->mail_emptyfields)) || (isset($element1) && $this->empty_field($element1, $row->mail_emptyfields))) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >From:' . $element0 . '<span style="margin-left:6px">To</span>:' . $element1 . '</td></tr>';					
								$list_text_mode = $list_text_mode.$element_label.' - From:'.$element0.' To:'.$element1."\r\n";
								$custom_fields[$i] = $element0 .'-'. $element1;
							}
							break;
						}
					  
						case "type_grading": {
							$hidden_element = isset($_POST['wdform_'.$i."_hidden_item".$id]) ? $_POST['wdform_'.$i."_hidden_item".$id] : '';
							$grading = explode(":", $hidden_element);
							$items_count = sizeof($grading) - 1;							
							$element = '';
							$total = '';	
							$form_empty_field = 0;
							for($k = 0;$k < $items_count; $k++) {
								$element .= $grading[$k] . " : " . (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : ' ') . ' ';
								$total += (isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) ? $_POST['wdform_'.$i."_element".$id.'_'.$k] : 0);
								if(isset($_POST['wdform_'.$i."_element".$id.'_'.$k]) && $_POST['wdform_'.$i."_element".$id.'_'.$k] != 0) {
									$form_empty_field = 1;
								}
							}
							$element .= "Total:" . $total;
						
							if(isset($element) && $this->empty_field($form_empty_field, $row->mail_emptyfields)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $element . '</td></tr>';
								$list_text_mode = $list_text_mode.$element_label.' - '.$element."\r\n";
								$custom_fields[$i] = $element;
							}
							break;
						}
					
						case "type_matrix": {
							$input_type = isset($_POST['wdform_'.$i."_input_type".$id]) ? $_POST['wdform_'.$i."_input_type".$id] : "";
							$mat_rows = explode("***", isset($_POST['wdform_'.$i."_hidden_row".$id]) ? $_POST['wdform_'.$i."_hidden_row".$id] : "");
							$rows_count = sizeof($mat_rows) - 1;
							$mat_columns = explode("***", isset($_POST['wdform_'.$i."_hidden_column".$id]) ? $_POST['wdform_'.$i."_hidden_column".$id] : "");
							$columns_count = sizeof($mat_columns) - 1;
							$matrix = "<table>";
							$matrix .= '<tr><td></td>';							
							for($k = 1; $k < count($mat_columns); $k++) {
								$matrix .= '<td style="background-color:#BBBBBB; padding:5px; ">' . $mat_columns[$k] . '</td>';
							}
							$matrix .= '</tr>';							
							$aaa = Array();							
							for($k = 1; $k <= $rows_count; $k++) {
								$matrix .= '<tr><td style="background-color:#BBBBBB; padding:5px;">' . $mat_rows[$k] . '</td>';
								if($input_type == "radio") {
									$mat_radio = isset($_POST['wdform_'.$i."_input_element".$id.$k]) ? $_POST['wdform_'.$i."_input_element".$id.$k] : 0;
									if($mat_radio == 0) {
										$checked = "";
										$aaa[1] = "";
									}
									else {
										$aaa = explode("_", $mat_radio);
									}
									for($j = 1; $j <= $columns_count; $j++) {
										if($aaa[1] == $j) {
											$checked = "checked";
										}
										else {
											$checked = "";
										}
										$matrix .= '<td style="text-align:center"><input  type="radio" ' . $checked . ' disabled /></td>';
									}
								}
								else {
									if($input_type == "checkbox") {                
										for($j = 1; $j <= $columns_count; $j++) {
											$checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : "";
											if($checked == 1) {
												$checked = "checked";
											}
											else {
												$checked = "";
											}
											$matrix .= '<td style="text-align:center"><input  type="checkbox" ' . $checked . ' disabled /></td>';									
										}								
									}
									else {
										if($input_type == "text") {																  
											for($j = 1; $j <= $columns_count; $j++) {
												$checked = isset($_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_input_element".$id.$k.'_'.$j] : "";
												$matrix .= '<td style="text-align:center"><input  type="text" value="' . $checked . '" disabled /></td>';								
											}										
										}
										else {
											for($j = 1; $j <= $columns_count; $j++) {
												$checked = isset($_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j]) ? $_POST['wdform_'.$i."_select_yes_no".$id.$k.'_'.$j] : "";
												$matrix .= '<td style="text-align:center">' . $checked . '</td>';
											}
										}									
									}									
								}
								$matrix .= '</tr>';							
							}
							$matrix .= '</table>';	
							if(isset($matrix)) {
								$list = $list . '<tr valign="top"><td >' . $element_label . '</td><td >' . $matrix . '</td></tr>';
								$custom_fields[$i] = $matrix;
							}						
							break;
						}
						
						default: break;
					}
				}
				else {
					$custom_fields[$key] = '';
				}
							
		}

		$list = $list . '</table>';
		$all_form_variables = array('all' => array($list_text_mode, $list), 'custom_fields' => $custom_fields, 'special_fields' => $special_fields);

		return $all_form_variables;
	}	
	
	public function remove($group_id) {
		global $wpdb;
		$wpdb->query($wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'mwd_forms_submits WHERE group_id= %d', $group_id));
	}
	
	public function get_message($form_id, $message, $response_data) {
		global $wpdb;
		$label_order_original = array();
		$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "mwd_forms WHERE id= %d", $form_id));
		$message_text = $wpdb->get_var("SELECT `".$message."` FROM " . $wpdb->prefix . "mwd_forms WHERE id='" . $form_id."'");
		$all_form_variables = $this->gen_custom_fields($form_id, $response_data);
		$list = $all_form_variables['all'][1];
		$custom_fields = $all_form_variables['custom_fields'];
		$special_fields = $all_form_variables['special_fields'];
		
		$label_all = explode('#****#',$row->label_order);
		$label_all = array_slice($label_all, 0, count($label_all) - 1);
		foreach ($label_all as $key => $label_each) {
			$label_id_each = explode('#**id**#', $label_each);
			$label_id = $label_id_each[0];
			$label_order_each = explode('#**label**#', $label_id_each[1]);
			$label_order_original[$label_id] = $label_order_each[0];
		}
		$special_fields['all'] = $list;
		foreach($special_fields as $key => $special_field) {
			if(strpos($message_text, "%".$key."%") > -1)
				$message_text = str_replace("%".$key."%", $special_field, $message_text);
		}
		
		foreach($label_order_original as $key => $label_each) {
			if(strpos($message_text, "%".$label_each."%") > -1)	 {				
				$message_text = str_replace("%".$label_each."%", $custom_fields[$key], $message_text);
			}
		}

		return $message_text;
	}
  
	public function increment_views_count($id) {
		global $wpdb;
		$vives_form = $wpdb->get_var($wpdb->prepare("SELECT views FROM " . $wpdb->prefix . "mwd_forms_views WHERE form_id=%d", $id));
		if (isset($vives_form)) {
		$vives_form = $vives_form + 1;
		$wpdb->update($wpdb->prefix . "mwd_forms_views", array(
			'views' => $vives_form,
			), array('form_id' => $id), array(
			'%d',
			), array('%d'));
		}
		else {
			$wpdb->insert($wpdb->prefix . 'mwd_forms_views', array(
				'form_id' => $id,
				'views' => 1
				), array(
				  '%d',
				  '%d'
			));
		}
	}

	public function empty_field($element, $mail_emptyfields) {		
		if(!$mail_emptyfields)
			if(empty($element))
				return 0;

		return 1;
	}
	
	public function all_forms() {		
		global $wpdb;

		$forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix .'mwd_display_options as display INNER JOIN ' . $wpdb->prefix . 'mwd_forms as forms ON display.form_id = forms.id WHERE display.type != "embedded" and forms.published=1');
		return $forms;
	}
	
	public function display_options($id){
		global $wpdb;
		$row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix .'mwd_display_options as display WHERE form_id = '.(int)$id);
		return $row;
	}
	
	public function getGroupFields($list_ids) {
		$this->clear_mailchimp_api_cache();
		$apikey = get_option('mwd_api_key', '');
		$api = new Mailchimp($apikey);
		try {
			$mchlists = $api->call('lists/list' , array( 'apikey' => $apikey, 'filters' => array('list_id' => $list_ids) ));
		} catch (Exception $e) {
			//echo '<h4>Error: '. $e->getMessage().'</h4>';
			return;
		}
		
		$groups = array();
		$interest_groups = array();
		if ( is_array($mchlists)) {
			foreach ( $mchlists['data'] as $list ) {
				try {
					$interest_groups = $api->call( 'lists/interest-groupings' , array( 'apikey' => $apikey , 'id' => $list['id'] , 'counts' => true ) );
				} catch( Exception $e ) {
				}
			}
		}

		if($interest_groups)
			foreach ( $interest_groups as $int_group ) {
				$groups[$int_group['id']] = $int_group['name'];
			}

		return $groups; 
	}
	
	public function clear_mailchimp_api_cache() {
		delete_transient('mwd-list-info');
		delete_transient('mwd-profile-info');
		delete_transient('mwd-account-details');
		delete_transient('mwd-lists');
		delete_transient('mwd-subscribers-data');
		delete_transient('mwd-list-data');
		delete_transient('mwd-subscriber-data');
	}
	
	public function mwd_searcharray($value, $key, $array) {
		if($array){
			foreach ($array as $k => $val) {
				if ($val[$key] == $value) {
				   return true;
				}
			}
		}
		return false;
	}

 
	////////////////////////////////////////////////////////////////////////////////////////
	// Getters & Setters                                                                  //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Private Methods                                                                    //
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	// Listeners                                                                          //
	////////////////////////////////////////////////////////////////////////////////////////
}