<?php
class MFN_Options_upload extends MFN_Options{

	/**
	 * Constructor
	 */
	function __construct( $field = array(), $value = '', $prefix = false ){

		$this->field = $field;
		$this->value = $value;

		// theme options 'opt_name'
		$this->prefix = $prefix;

	}

	/**
	 * Render
	 */
	function render( $meta = false ){

		// data
		$data 	= isset( $this->field[ 'data' ] ) ? $this->field[ 'data' ] : 'image';

		// name -----------------------------------------------------
		if( $meta == 'new' ){

			// builder new
			$name = 'data-name="'. $this->field['id'] .'"';

		} elseif( $meta ){

			// page mata & builder existing items
			$name = 'name="'. $this->field['id'] .'"';

		} else {

			// theme options
			$name = 'name="'. $this->prefix .'['. $this->field['id'] .']"';

		}

		// value is empty -------------------------------------------
		if( $this->value == '' ){
			$remove = 'style="display:none;"';
			$upload = '';
		} else {
			$remove = '';
			$upload = 'style="display:none;"';
		}

		// echo -----------------------------------------------------
		echo '<div class="mfn-upload-field">';

			echo '<input type="text" '. $name .' value="'. $this->value .'" class="'. $data .'" />';

			echo '&nbsp;<a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="mfn-opts-upload" '. $upload .'><span></span>'. __('Browse', 'mfn-opts') .'</a>';
			echo ' <a href="javascript:void(0);" class="mfn-opts-upload-remove" '. $remove .'>'.__('Remove Upload', 'mfn-opts').'</a>';

			if( 'image' == $data  ){
				echo '<img class="mfn-opts-screenshot '. $data .'" src="'. $this->value .'" />';
			}

			if( isset( $this->field['desc'] ) ){
				echo '<span class="description '. $data .'">'. $this->field['desc'] .'</span>';
			}

		echo '</div>';
	}

    /**
     * Enqueue
     */
    function enqueue() {

			wp_enqueue_media();
			wp_enqueue_script( 'mfn-opts-field-upload-js', MFN_OPTIONS_URI .'fields/upload/field_upload.js', array( 'jquery' ), THEME_VERSION, true );

    }
}
