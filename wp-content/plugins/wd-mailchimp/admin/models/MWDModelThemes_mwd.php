<?php

class MWDModelThemes_mwd {
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
	public function get_rows_data() {
		global $wpdb;
		$where = array();
		if(isset($_POST['search_value']) && (esc_html($_POST['search_value']) != ''))
			$where[] = '`title` LIKE "%' . esc_html($_POST['search_value']) . '%"';

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : ''); 
		$asc_or_desc = ((isset($_POST['asc_or_desc']) && $_POST['asc_or_desc'] == 'desc') ? 'desc' : 'asc');
		$order_by_array = array('id', 'title', 'default');
		$order_by = isset($_POST['order_by']) && in_array(esc_html(stripslashes($_POST['order_by'])), $order_by_array) ? esc_html(stripslashes($_POST['order_by'])) :  'id';
		$order_by = ' ORDER BY `' . $order_by . '` ' . $asc_or_desc;
		if (isset($_POST['page_number']) && $_POST['page_number']) {
			$limit = ((int) $_POST['page_number'] - 1) * 20;
		}
		else {
			$limit = 0;
		}
		$query = "SELECT * FROM " . $wpdb->prefix . "mwd_themes " . $where . $order_by . " LIMIT " . $limit . ",20";
		$rows = $wpdb->get_results($query);
		return $rows;
	}
  
	public function get_row_data($id, $reset) {
		global $wpdb;
		if ($id != 0) {
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_themes WHERE id="%d"', $id));
			if ($reset) {
				if (!$row->default) {
					$row_id = $row->id;
					$row_title = $row->title;
					$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'mwd_themes WHERE default="%d"', 1));
					$row->id = $row_id;
					$row->title = $row_title;
					$row->default = FALSE;
				}
				else {
					$row->params = '';
				}
			}
		}
		else {
			$row = new stdClass();
			$row->id = 0;
			$row->title = '';
			$row->params = '{"nonce_mwd":"ec3d4348ae","task":"","params":"","current_id":"1","default":"1","active_tab":"minimize","title":"Theme 1","GPFontFamily":"tahoma","AGPWidth":"100","AGPSPWidth":"30","AGPPadding":"","AGPMargin":"0 auto","AGPBorderColor":"#ffffff","AGPBorderType":"solid","AGPBorderWidth":"1","AGPBorderRadius":"0","AGPBoxShadow":"","HPAlign":"top","HPBGColor":"#96afab","HPWidth":"100","HTPWidth":"40","HPPadding":"10px 0","HPMargin":"","HPTextAlign":"center","HPBorderColor":"#b7b7b7","HPBorderType":"solid","HPBorderWidth":"1","HPBorderRadius":"0","HTPFontSize":"24","HTPWeight":"normal","HTPColor":"#ffffff","HDPFontSize":"15","HDPColor":"#607370","HIPAlign":"top","HIPWidth":"80","HIPHeight":"","GPBGColor":"#ededed","GPFontSize":"16","GPFontWeight":"normal","GPWidth":"100","GTPWidth":"60","GPAlign":"center","GPBackground":"","GPBackgroundRepeat":"no-repeat","GPBGPosition1":"","GPBGPosition2":"","GPBGSize1":"","GPBGSize2":"","GPColor":"#607370","GPPadding":"10px","GPMargin":"","GPBorderColor":"#ffffff","GPBorderType":"solid","GPBorderWidth":"1","GPBorderRadius":"0","GPMLFontSize":"14","GPMLFontWeight":"normal","GPMLColor":"#868686","GPMLPadding":"0px 5px 0px 0px","GPMLMargin":"0px","SEPBGColor":"#ededed","SEPPadding":"","SEPMargin":"","COPPadding":"15px 20px","COPMargin":"0px","FPWidth":"70","FPPadding":"15px 0 0 0","FPMargin":"0 auto","IPHeight":"26","IPFontSize":"14","IPFontWeight":"normal","IPBGColor":"#ffffff","IPColor":"#868686","IPPadding":"0px 5px","IPMargin":"0px","IPBorderTop":"top","IPBorderRight":"right","IPBorderBottom":"bottom","IPBorderLeft":"left","IPBorderColor":"#dfdfdf","IPBorderType":"solid","IPBorderWidth":"1","IPBorderRadius":"0","IPBoxShadow":"","SBPAppearance":"none","SBPBackground":"images/themes/drop-downs/2.png","SBPBGRepeat":"no-repeat","SBPBGPos1":"95%","SBPBGPos2":"50%","SBPBGSize1":"8%","SBPBGSize2":"32%","SCPBGColor":"#ffffff","SCPWidth":"16","SCPHeight":"16","SCPBorderTop":"top","SCPBorderRight":"right","SCPBorderBottom":"bottom","SCPBorderLeft":"left","SCPBorderColor":"#868686","SCPBorderType":"solid","SCPBorderWidth":"1","SCPMargin":"0px 3px","SCPBorderRadius":"15","SCPBoxShadow":"","SCCPBGColor":"#868686","SCCPWidth":"6","SCCPHeight":"6","SCCPMargin":"5","SCCPBorderRadius":"10","MCPBGColor":"#ffffff","MCPWidth":"16","MCPHeight":"16","MCPBorderTop":"top","MCPBorderRight":"right","MCPBorderBottom":"bottom","MCPBorderLeft":"left","MCPBorderColor":"#868686","MCPBorderType":"solid","MCPBorderWidth":"1","MCPMargin":"0px 3px","MCPBorderRadius":"0","MCPBoxShadow":"","MCCPBGColor":"","MCCPBackground":"images/themes/checkboxes/1.png","MCCPBGRepeat":"no-repeat","MCCPBGPos1":"","MCCPBGPos2":"","MCCPWidth":"16","MCCPHeight":"16","MCCPMargin":"0","MCCPBorderRadius":"0","SPAlign":"left","SPBGColor":"#e74c3c","SPWidth":"","SPHeight":"","SPFontSize":"16","SPFontWeight":"normal","SPColor":"#ffffff","SPPadding":"5px 8px","SPMargin":"0 15px 0 0","SPBorderTop":"top","SPBorderRight":"right","SPBorderBottom":"bottom","SPBorderLeft":"left","SPBorderColor":"#e74c3c","SPBorderType":"solid","SPBorderWidth":"1","SPBorderRadius":"0","SPBoxShadow":"","SHPBGColor":"#701e16","SHPColor":"#ffffff","SHPBorderTop":"top","SHPBorderRight":"right","SHPBorderBottom":"bottom","SHPBorderLeft":"left","SHPBorderColor":"#701e16","SHPBorderType":"solid","SHPBorderWidth":"1","BPBGColor":"#96afab","BPWidth":"","BPHeight":"","BPFontSize":"16","BPFontWeight":"normal","BPColor":"#ffffff","BPPadding":"5px 8px","BPMargin":"0 15px 0 0","BPBorderTop":"top","BPBorderRight":"right","BPBorderBottom":"bottom","BPBorderLeft":"left","BPBorderColor":"#8a8a8a","BPBorderType":"solid","BPBorderWidth":"1","BPBorderRadius":"0","BPBoxShadow":"","BHPBGColor":"#5a7784","BHPColor":"#ffffff","BHPBorderTop":"top","BHPBorderRight":"right","BHPBorderBottom":"bottom","BHPBorderLeft":"left","BHPBorderColor":"#5a7784","BHPBorderType":"solid","BHPBorderWidth":"1","PSAPBGColor":"#e74c3c","PSAPFontSize":"16","PSAPFontWeight":"normal","PSAPColor":"#ffffff","PSAPHeight":"","PSAPLineHeight":"","PSAPPadding":"6px","PSAPMargin":"0 1px 0 0 ","PSAPBorderTop":"top","PSAPBorderRight":"right","PSAPBorderBottom":"bottom","PSAPBorderLeft":"left","PSAPBorderColor":"#e74c3c","PSAPBorderType":"solid","PSAPBorderWidth":"2","PSAPBorderRadius":"0","PSDPBGColor":"#ededed","PSDPFontSize":"14","PSDPFontWeight":"normal","PSDPColor":"#737373","PSDPHeight":"","PSDPLineHeight":"","PSDPPadding":"3px 5px","PSDPMargin":"0 1px 0 0 ","PSDPBorderTop":"top","PSDPBorderRight":"right","PSDPBorderBottom":"bottom","PSDPBorderLeft":"left","PSDPBorderColor":"#ededed","PSDPBorderType":"solid","PSDPBorderWidth":"2","PSDPBorderRadius":"0","PSAPAlign":"right","PSAPWidth":"","PPAPWidth":"100%","NBPBGColor":"","NBPWidth":"","NBPHeight":"","NBPLineHeight":"","NBPColor":"#607370","NBPPadding":"4px 10px","NBPMargin":"0px","NBPBorderColor":"#777777","NBPBorderType":"solid","NBPBorderWidth":"1","NBPBorderRadius":"0","NBPBoxShadow":"","NBHPBGColor":"","NBHPColor":"#96afab","NBHPBorderColor":"#787878","NBHPBorderType":"solid","NBHPBorderWidth":"1","PBPBGColor":"","PBPWidth":"100","PBPHeight":"","PBPLineHeight":"","PBPColor":"#607370","PBPPadding":"","PBPMargin":"0px","PBPBorderColor":"#777777","PBPBorderType":"solid","PBPBorderWidth":"1","PBPBorderRadius":"0","PBPBoxShadow":"","PBHPBGColor":"","PBHPColor":"#96afab","PBHPBorderColor":"#787878","PBHPBorderType":"solid","PBHPBorderWidth":"1","CBPPosition":"absolute","CBPTop":"10px","CBPRight":"10px","CBPBottom":"","CBPLeft":"","CBPBGColor":"","CBPFontSize":"20","CBPFontWeight":"normal","CBPColor":"#777777","CBPPadding":"0px","CBPMargin":"0px","CBPBorderColor":"#ffffff","CBPBorderType":"solid","CBPBorderWidth":"1","CBPBorderRadius":"0","CBHPBGColor":"","CBHPColor":"#e74c3c","CBHPBorderColor":"#737373","CBHPBorderType":"solid","CBHPBorderWidth":"1","MBPBGColor":"#96afab","MBPFontSize":"17","MBPFontWeight":"normal","MBPColor":"#ffffff","MBPTextAlign":"center","MBPPadding":"10px","MBPMargin":"","MBPBorderTop":"top","MBPBorderRight":"right","MBPBorderBottom":"bottom","MBPBorderLeft":"left","MBPBorderColor":"#96afab","MBPBorderType":"solid","MBPBorderWidth":"2","MBPBorderRadius":"0","MBHPBGColor":"#96afab","MBHPColor":"#607370","MBHPBorderTop":"top","MBHPBorderRight":"right","MBHPBorderBottom":"bottom","MBHPBorderLeft":"left","MBHPBorderColor":"#96afab","MBHPBorderType":"solid","MBHPBorderWidth":"2","OPDeInputColor":"#afafaf","OPFontStyle":"normal","OPRColor":"#ff1313","OPDPIcon":"images/themes/date-pickers/2.png","OPDPRepeat":"no-repeat","OPDPPos1":"0%","OPDPPos2":"10%","OPDPMargin":"3px 0 0 -23px","OPFBgUrl":"images/themes/file-uploads/2.png","OPFBGRepeat":"no-repeat","OPFPos1":"0%","OPFPos2":"10%","OPGWidth":"100","CUPCSS":""}';
			$row->default = 0;
		}
		return $row;
	}
  
	public function page_nav() {
		global $wpdb;
		$where = ((isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? 'WHERE title LIKE "%' . esc_html($_POST['search_value']) . '%"'  : '');
		$query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "mwd_themes " . $where;
		$total = $wpdb->get_var($query);
		$page_nav['total'] = $total;
		if (isset($_POST['page_number']) && $_POST['page_number']) {
			$limit = ((int) $_POST['page_number'] - 1) * 20;
		}
		else {
			$limit = 0;
		}
		$page_nav['limit'] = (int) ($limit / 20 + 1);
		return $page_nav;
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