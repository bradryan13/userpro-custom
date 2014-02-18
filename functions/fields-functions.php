<?php

	/* Get fields as arrays */
	function userpro_fields_group_by_template( $template, $group='default' ) {
		$array = get_option("userpro_fields_groups");
		if (isset($array[$template][$group]))
			if (count($array[$template][$group]) > 0)
				return (array)$array[$template][$group];
		return array('');
	}
	
	/* Get specific fields only */
	function userpro_get_fields( $fields=array() ) {
		$array = get_option("userpro_fields_builtin");
		return array_intersect_key($array, array_flip($fields));
	}
	
	/* Get all field keys */
	function userpro_retrieve_metakeys() {
		$fields = get_option('userpro_fields');
		$array = array_keys($fields);
		return $array;
	}
	
	/* Retrieves a field */
	function userpro_add_field($field, $hideable=0, $hidden=0, $required=0, $ajaxcheck=null) {
		$fields = get_option('userpro_fields');
		$array = $fields[$field];
		$array['hideable'] = $hideable;
		$array['hidden'] = $hidden;
		$array['required'] = $required;
		$array['ajaxcheck'] = $ajaxcheck;
		return $array;
	}
	
	/* Assign a section */
	function userpro_add_section($name, $collapsible=0, $collapsed=0) {
		$array = array(
			'heading' => $name,
			'collapsible' => $collapsible,
			'collapsed' => $collapsed
		);
		return $array;
	}
	
	/* Get file type icon */
	function userpro_file_type_icon( $file ) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		switch($ext){
			default:
				$type = 'file';
				break;
			case 'txt':
				$type = 'txt';
				break;
			case 'pdf':
				$type = 'pdf';
				break;
			case 'zip':
				$type = 'zip';
				break;
		}
		return 'class="'.$type.'"';
	}
	
	/* If field has special roles */
	function userpro_field_by_role($key, $user_id){
		$test = userpro_get_option($key.'_roles');
		if ($user_id > 0 && is_array($test) && !current_user_can('manage_options') ){
			
			$user = get_userdata( $user_id );
			$user_role = array_shift($user->roles);
			if (!in_array($user_role, $test)){
				return false;
			}
		}
		return true;
	}
	
	/* Edit a field */
	function userpro_edit_field( $key, $array, $i, $args, $user_id=null ) {
		global $userpro;
		
		extract($array);
		extract($args);
		$res = null;
		
		/**
		include & exclude
		done by custom shortcode
		params 
		start here 
		**/
		
		if (isset($args['exclude_fields']) && $args['exclude_fields'] != '' ){
			if (in_array( $key, explode(',',$args['exclude_fields']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['exclude_fields_by_name']) && $args['exclude_fields_by_name'] != '' ){
			if (in_array( $array['label'], explode(',',$args['exclude_fields_by_name']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['exclude_fields_by_type']) && $args['exclude_fields_by_type'] != '' ){
			if (isset($array['type']) && in_array( $array['type'], explode(',',$args['exclude_fields_by_type']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields']) && $args['include_fields'] != '' ){
			if (!in_array( $key, explode(',',$args['include_fields']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields_by_name']) && $args['include_fields_by_name'] != '' ){
			if (!in_array( $array['label'], explode(',',$args['include_fields_by_name']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields_by_type']) && $args['include_fields_by_type'] != '' ){
			if (isset($array['type']) && !in_array( $array['type'], explode(',',$args['include_fields_by_type']) ) || !isset($array['type']) ) {
				$res = '';
				return false;
			}
		}
		
		/**
		end here
		thanks please do not edit 
		here unless you know what you do
		**/
		
		/* get field data */
		$data = null;
		
		/* default ajax callbacks/checks */
		if ($key == 'user_login' && $args['template'] == 'register') {
			if (!isset($array['ajaxcheck']) || $array['ajaxcheck'] == ''){
				$array['ajaxcheck'] = 'username_exists';
			}
		}
		if ($key == 'user_email' && $args['template'] == 'register') {
			if (!isset($array['ajaxcheck']) || $array['ajaxcheck'] == ''){
				$array['ajaxcheck'] = 'email_exists';
			}
		}
		if ($key == 'display_name' && $args['template'] == 'edit') {
			if (!isset($array['ajaxcheck']) || $array['ajaxcheck'] == ''){
				$array['ajaxcheck'] = 'display_name_exists';
			}
		}
		if ($key == 'display_name' && $args['template'] == 'register') {
			if (!isset($array['ajaxcheck']) || $array['ajaxcheck'] == ''){
				$array['ajaxcheck'] = 'display_name_exists';
			}
		}
		
		foreach($array as $data_option=>$data_value){
			if (!is_array($data_value)){
				$data .= " data-$data_option='$data_value'";
			}
		}
		
		/* disable editing */
		if (userpro_user_cannot_edit($array)){
			$data .= ' disabled="disabled"';
		}

		/* if editing an already user */
		if ($user_id){
			$is_hidden = userpro_profile_data('hide_'.$key, $user_id);
			$value = userpro_profile_data( $key, $user_id );
			if (isset($array['type']) && $array['type'] == 'picture'){
				if ($key == 'profilepicture') {
					$value = get_avatar($user_id, 64);
				} else {
					$crop = userpro_profile_data( $key, $user_id );
					if (!$crop){
						$value = '<span class="userpro-pic-none">'.__('No file has been uploaded.','userpro').'</span>';
					} else {
						$value = '';
					}
					
					if (isset($array['width'])){
						$width = $array['width'];
						$height = $array['height'];
					} else {
						$width = '';
						$height = '';
					}
					
					$value .= '<img src="'.$crop.'" width="'.$width.'" height="'.$height.'" alt="" class="modified" />';
				}
			}
			if (isset($array['type']) && $array['type'] == 'file') {
				$value = '<span class="userpro-pic-none">'.__('No file has been uploaded.','userpro').'</span>';
				$file = userpro_profile_data( $key, $user_id );
				if ($file){
					$value = '<div class="userpro-file-input"><a href="'.$file.'" '.userpro_file_type_icon($file).'>'.basename( $file ).'</a></div>';
				}
			}
		} else {
			
			// perhaps in registration
			if (isset($array['type']) && $array['type'] == 'picture'){
				if ($key == 'profilepicture') {
					$array['default'] = get_avatar(0, 64);
				}
			}
			
			if (isset($array['hidden'])){
			$is_hidden = $array['hidden'];
			}
			
			if (isset($array['default'])){
			$value = $array['default'];
			}
			
		}
		
		if (!isset($value)) $value = null;
		
		if (!isset($array['placeholder'])) $array['placeholder'] = null;
		
		/* remove passwords */
		if (isset($array['type']) && $array['type'] == 'password') $value = null;
		
		/* display a section */
		if ($allow_sections && isset( $array['heading'] ) ) {
		$res .= "<div class='userpro-section userpro-column userpro-collapsible-".$array['collapsible']." userpro-collapsed-".$array['collapsed']."'>".$array['heading']."</div>";
		}
		
		/* display a field */
		if (!$user_id) $user_id = 0;
		if (isset( $array['type'] ) && userpro_field_by_role( $key, $user_id ) ) {
		$res .= "<div class='userpro-field ".userpro_private_field_class($array)."' data-key='$key'>";
		
		if ( $array['label'] && $array['type'] != 'passwordstrength' ) {
		
		if ($args['field_icons'] == 1) {
		$res .= "<div class='userpro-label iconed'>";
		} else {
		$res .= "<div class='userpro-label'>";
		}
		$res .= "<label for='$key-$i'>".$array['label']."</label>";
					
					if ($args['field_icons'] == 1 && $userpro->field_icon($key)) {
						$res .= '<span class="userpro-field-icon"><i class="userpro-icon-'. $userpro->field_icon($key) .'"></i></span>';
					}
					
					if ($args['template'] != 'login' && isset( $array['help'] ) && $array['help'] != '' ) {
						$res .= '<span class="userpro-tip" title="'.stripslashes( $array['help'] ).'"></span>';
					}
					
		$res .= "</div>";
		}
		
		$res .= "<div class='userpro-input'>";
		
			/* switch field type */
			switch($array['type']) {
			
				case 'picture':
					if (!isset($array['button_text']) || $array['button_text'] == '' ) $array['button_text'] = __('Upload photo','userpro');
					$res .= "<div class='userpro-pic userpro-pic-".$key."' data-remove_text='".__('Remove','userpro')."'>".$value."</div>";
					$res .= "<div class='userpro-pic-upload' data-filetype='picture' data-allowed_extensions='png,gif,jpg,jpeg'>".$array['button_text']."</div>";
					if ($user_id && userpro_profile_data( $key, $user_id ) ){
					$res .= "<input type='button' value='".__('Remove','userpro')."' class='userpro-button red' />";
					}
					$res .= "<input type='hidden' name='$key-$i' id='$key-$i' value='".userpro_profile_data( $key, $user_id )."' />";
					break;
					
				case 'file':
					if (!isset($array['button_text']) || $array['button_text'] == '') $array['button_text'] = __('Upload file','userpro');
					$res .= "<div class='userpro-pic' data-remove_text='".__('Remove','userpro')."'>".$value."</div>";
					$res .= "<div class='userpro-pic-upload' data-filetype='file' data-allowed_extensions='".$array['allowed_extensions']."'>".$array['button_text']."</div>";
					if ($user_id && userpro_profile_data( $key, $user_id ) ){
					$res .= "<input type='button' value='".__('Remove','userpro')."' class='userpro-button red' />";
					}
					$res .= "<input type='hidden' name='$key-$i' id='$key-$i' value='".userpro_profile_data( $key, $user_id )."' />";
					break;
					
				case 'datepicker':
					$res .= "<input data-fieldtype='datepicker' class='userpro-datepicker' type='text' name='$key-$i' id='$key-$i' value='".$value."' placeholder='".$array['placeholder']."' $data />";
					
					/* allow user to make it hideable */
					if ( isset($array['hideable']) && $array['hideable'] == 1) {
						$res .= "<label class='userpro-checkbox hide-field'><span";
						if (checked( $hideable, $is_hidden, 0 )) { $res .= ' class="checked"'; }
						$res .= "></span><input type='checkbox' value='$hideable' name='hide_$key-$i'";
						$res .= checked( $hideable, $is_hidden, 0 );
						$res .= " />".__('Make this field hidden from public','userpro')."</label>";
					}
					
					break;
					
				case 'text':
				
					$res .= "<input type='text' name='$key-$i' id='$key-$i' value='".$value."' placeholder='".$array['placeholder']."' $data />";
					
					/* allow user to make it hideable */
					if ( isset($array['hideable']) && $array['hideable'] == 1) {
						$res .= "<label class='userpro-checkbox hide-field'><span";
						if (checked( $hideable, $is_hidden, 0 )) { $res .= ' class="checked"'; }
						$res .= "></span><input type='checkbox' value='$hideable' name='hide_$key-$i'";
						$res .= checked( $hideable, $is_hidden, 0 );
						$res .= " />".__('Make this field hidden from public','userpro')."</label>";
					}
					
					break;
					
				case 'antispam':
					
					$rand1 = rand(1, 10);
					$rand2 = rand(1, 10);
					$res .= sprintf(__('Answer: %s + %s','userpro'), $rand1, $rand2);
					$res .= "<input type='text' name='$key-$i' id='$key-$i' value='' $data />";
					$res .= "<input type='hidden' name='answer-$i' id='answer-$i' value='".($rand1 + $rand2)."' />";
					
					break;
					
				case 'textarea':
					if (isset($array['size'])) {
						$size = $array['size'];
					} else {
						$size = 'normal';
					}
					$res .= "<textarea class='$size' type='text' name='$key-$i' id='$key-$i' $data >$value</textarea>";
					
					/* allow user to make it hideable */
					if ($array['hideable'] == 1) {
						$res .= "<label class='userpro-checkbox hide-field'><span";
						if (checked( $hideable, $is_hidden, 0 )) { $res .= ' class="checked"'; }
						$res .= "></span><input type='checkbox' value='$hideable' name='hide_$key-$i'";
						$res .= checked( $hideable, $is_hidden, 0 );
						$res .= " />".__('Make this field hidden from public','userpro')."</label>";
					}
					
					break;
					
				case 'password':
					$res .= "<input type='password' name='$key-$i' id='$key-$i' value='".$value."' placeholder='".$array['placeholder']."' autocomplete='off' $data />";
					break;
					
				case 'passwordstrength' :
					$res .= '<span class="strength-text" '.$data.'>'.__('Password Strength','userpro').'</span><div class="userpro-clear"></div><span class="strength-container"><span class="strength-plain"></span><span class="strength-plain"></span><span class="strength-plain"></span><span class="strength-plain"></span><span class="strength-plain"></span></span><div class="userpro-clear"></div>';
					break;
					
				case 'select':
					if (isset($options)){
					
					if ($key == 'role') {
					
						$options = userpro_get_roles( userpro_get_option('allowed_roles') );
						if (!isset( $value )) $value = 0;
						$res .= "<select name='$key-$i' id='$key-$i' class='chosen-select' data-placeholder='".$array['placeholder']."' $data >";
						if (is_array($options)) {
							if (isset($array['placeholder']) && !empty($array['placeholder'])){
								$res .= '<option value="" '.selected(0, $value, 0).'></option>';
							}
							foreach($options as $k=>$v) {
								$v = stripslashes($v);
								$res .= '<option value="'.$k.'" '.selected($k, $value, 0).'>'.$v.'</option>';
							}
						}
						$res .= "</select>";
						
					} else {
					
						if (!isset( $value )) $value = 0;
						if (isset($array['default']) && !$value) $value = $array['default'];
						$res .= "<select name='$key-$i' id='$key-$i' class='chosen-select' data-placeholder='".$array['placeholder']."' $data >";
						if (is_array($options)) {
							if (isset($array['placeholder']) && !empty($array['placeholder'])){
								$res .= '<option value="" '.selected(0, $value, 0).'></option>';
							}
							foreach($options as $k=>$v) {
								$v = stripslashes($v);
								$res .= '<option value="'.$v.'" '.selected($v, $value, 0).'>'.$v.'</option>';
							}
						}
						$res .= "</select>";
					
					}
					
					/* allow user to make it hideable */
					if ($array['hideable'] == 1) {
						$res .= "<label class='userpro-checkbox hide-field'><span";
						if (checked( $hideable, $is_hidden, 0 )) { $res .= ' class="checked"'; }
						$res .= "></span><input type='checkbox' value='$hideable' name='hide_$key-$i'";
						$res .= checked( $hideable, $is_hidden, 0 );
						$res .= " />".__('Make this field hidden from public','userpro')."</label>";
					}
					
					}
					break;
					
				case 'multiselect':
					if (isset($options)){
					$res .= "<select name='".$key.'-'.$i.'[]'."' multiple='multiple' class='chosen-select' data-placeholder='".$array['placeholder']."'>";
					foreach($options as $k=>$v) {
						$v = stripslashes($v);
						if (strstr($k, 'optgroup_b')) {
							$res .= "<optgroup label='$v'>";
						} elseif (strstr($k, 'optgroup_e')) {
							$res .= "</optgroup>";
						} else {
							$res .= '<option value="'.$v.'" ';
							if ( ( is_array( $value ) && in_array($v, $value ) ) || $v == $value ) { $res .= 'selected="selected"'; }
							$res .= '>'.$v.'</option>';
						}
					}
					$res .= "</select>";
					}
					break;
					
				case 'checkbox':
					if (isset($options)){
					$res .= "<div class='userpro-checkbox-wrap' data-required='".$array['required']."'>";
					foreach($options as $k=>$v) {
						$v = stripslashes($v);
						$res .= "<label class='userpro-checkbox'><span";
						if ( ( is_array( $value ) && in_array($v, $value ) ) || $v == $value ) { $res .= ' class="checked"'; }
						$res .= '></span><input type="checkbox" value="'.$v.'" name="'.$key.'-'.$i.'[]" ';
						if ( ( is_array( $value ) && in_array($v, $value ) ) || $v == $value ) { $res .= 'checked="checked"'; }
						$res .= " />$v</label>";
					}
					$res .= "</div>";
					}
					break;
					
				case 'checkbox-full':
					if (isset($options)){
					$res .= "<div class='userpro-checkbox-wrap' data-required='".$array['required']."'>";
					foreach($options as $k=>$v) {
						$v = stripslashes($v);
						$res .= "<label class='userpro-checkbox full'><span";
						if ( ( is_array( $value ) && in_array($v, $value ) ) || $v == $value ) { $res .= ' class="checked"'; }
						$res .= '></span><input type="checkbox" value="'.$v.'" name="'.$key.'-'.$i.'[]" ';
						if ( ( is_array( $value ) && in_array($v, $value ) ) || $v == $value ) { $res .= 'checked="checked"'; }
						$res .= " />$v</label>";
					}
					$res .= "</div>";
					}
					break;
					
				case 'mailchimp':
					if (!isset($array['list_text'])){
						$array['list_text'] = __('Subscribe to our newsletter','userpro');
					}
					
					if ( $userpro->mailchimp_is_subscriber($user_id, $array['list_id']) ) {
					
					$res .= "<div class='userpro-checkbox-wrap'>";
					$res .= "<div class='userpro-help'><i class='userpro-icon-ok'></i>".__('You are currently subscribed to this newsletter.','userpro')."</div>";
					$res .= "<label class='userpro-checkbox full'><span";
					$res .= '></span><input type="checkbox" value="subscribed" name="'.$key.'-'.$i.'" ';
					$res .= " />".__('Unsubscribe from this newsletter','userpro')."</label>";
					$res .= "</div>";
					
					} else {
					
					$res .= "<div class='userpro-checkbox-wrap'>";
					$res .= "<label class='userpro-checkbox full'><span";
					$res .= '></span><input type="checkbox" value="unsubscribed" name="'.$key.'-'.$i.'" ';
					$res .= " />".$array['list_text']."</label>";
					$res .= "</div>";
					
					}
					break;
				
				case 'radio':
					if (isset($options)){
					$res .= "<div class='userpro-radio-wrap' data-required='".$array['required']."'>";
					foreach($options as $k=>$v) {
						$v = stripslashes($v);
						$res .= "<label class='userpro-radio'><span";
						if (checked( $v, $value, 0 )) { $res .= ' class="checked"'; }
						$res .= '></span><input type="radio" value="'.$v.'" name="'.$key.'-'.$i.'" ';
						$res .= checked( $v, $value, 0 );
						$res .= " />$v</label>";
					}
					$res .= "</div>";
					}
					break;
					
				case 'radio-full':
					if (isset($options)){
					$res .= "<div class='userpro-radio-wrap' data-required='".$array['required']."'>";
					foreach($options as $k=>$v) {
						$v = stripslashes($v);
						$res .= "<label class='userpro-radio full'><span";
						if (checked( $v, $value, 0 )) { $res .= ' class="checked"'; }
						$res .= '></span><input type="radio" value="'.$v.'" name="'.$key.'-'.$i.'" ';
						$res .= checked( $v, $value, 0 );
						$res .= " />$v</label>";
					}
					$res .= "</div>";
					}
					break;
			
			} /* end switch field type */
			
		/* add action for each field */
		$hook = apply_filters("userpro_field_filter", $key, $user_id);
		$res .= $hook;
		
		$res .= "<div class='userpro-clear'></div>";
		$res .= "</div>";
		$res .= "</div><div class='userpro-clear'></div>";
		}
		
		return $res;
	}
	
	/* Show a field */
	function userpro_show_field( $key, $array, $i, $args, $user_id=null ) {
		global $userpro;
		
		extract($array);
		extract($args);
		$res = null;
		
		/**
		include & exclude
		done by custom shortcode
		params 
		start here 
		**/
		
		if (isset($args['exclude_fields']) && $args['exclude_fields'] != '' ){
			if (in_array( $key, explode(',',$args['exclude_fields']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['exclude_fields_by_name']) && $args['exclude_fields_by_name'] != '' ){
			if (in_array( $array['label'], explode(',',$args['exclude_fields_by_name']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['exclude_fields_by_type']) && $args['exclude_fields_by_type'] != '' ){
			if (isset($array['type']) && in_array( $array['type'], explode(',',$args['exclude_fields_by_type']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields']) && $args['include_fields'] != '' ){
			if (!in_array( $key, explode(',',$args['include_fields']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields_by_name']) && $args['include_fields_by_name'] != '' ){
			if (!in_array( $array['label'], explode(',',$args['include_fields_by_name']) ) ) {
				$res = '';
				return false;
			}
		}
		
		if (isset($args['include_fields_by_type']) && $args['include_fields_by_type'] != '' ){
			if (isset($array['type']) && !in_array( $array['type'], explode(',',$args['include_fields_by_type']) ) || !isset($array['type']) ) {
				$res = '';
				return false;
			}
		}
		
		/**
		end here
		thanks please do not edit 
		here unless you know what you do
		**/
		
		if ($user_id){
			$value = userpro_profile_data( $key, $user_id );
			if (isset($array['type']) && $key != 'role' && in_array($array['type'], array('select','multiselect','checkbox','checkbox-full','radio','radio-full') ) ) {
				$value = userpro_profile_data_nicename( $key, userpro_profile_data( $key, $user_id ) );
			}
			if ( ( isset($array['html']) && $array['html'] == 0 ) ) {
				$value =  userpro_profile_nohtml( $value );
			}
			if (isset($array['type']) && $array['type'] == 'picture'){
				if ($key == 'profilepicture') {
					$value = get_avatar($user_id, 64);
				} else {
					$crop = userpro_profile_data( $key, $user_id );
					if ($crop){
					if (isset($array['width'])){
						$width = $array['width'];
						$height = $array['height'];
					} else {
						$width = '';
						$height = '';
					}
					$value = '<img src="'.$crop.'" width="'.$width.'" height="'.$height.'" alt="" class="modified" />';
					}
				}
			}
			if (isset($array['type']) && $array['type'] == 'file'){
				$file = userpro_profile_data( $key, $user_id );
				if ($file){
				$value = '<div class="userpro-file-input"><a href="'.$file.'" '.userpro_file_type_icon($file).'>'.basename( $file ).'</a></div>';
				}
			}
			$value = userpro_filter_url($value, $args['link_target'] );
		}
		
		/* display a section */
		if ($allow_sections && isset($array['heading']) ) {
		$res .= "<div class='userpro-section userpro-column userpro-collapsible-".$array['collapsible']." userpro-collapsed-".$array['collapsed']."'>".$array['heading']."</div>";
		}
		
		/* display a field */
		if (!$user_id) $user_id = 0;
		if (isset($array['type']) && userpro_field_by_role( $key, $user_id ) && !empty($value) && userpro_field_is_viewable( $key, $user_id, $args )  && !in_array($key, $userpro->fields_to_hide_from_view() ) && $array['type'] != 'mailchimp' ) {
		$res .= "<div class='userpro-field ".userpro_private_field_class($array)." userpro-field-$template' data-key='$key'>";
		
		if ( $array['label'] && $array['type'] != 'passwordstrength' ) {
		
		if ($args['field_icons'] == 1) {
		$res .= "<div class='userpro-label view iconed'>";
		} else {
		$res .= "<div class='userpro-label view'>";
		}
		$res .= "<label for='$key-$i'>".$array['label']."</label>";
		
			if ($args['field_icons'] == 1 && $userpro->field_icon($key)) {
				$res .= '<span class="userpro-field-icon"><i class="userpro-icon-'. $userpro->field_icon($key) .'"></i></span>';
			}
					
		$res .= "</div>";
		
		}
		
		$res .= "<div class='userpro-input'>";
		
		/* before value display filter */
		$value = apply_filters('userpro_before_value_is_displayed', $value, $key, $array);
		
		if ($key == 'role'){
			$res .= userpro_user_role($value);
		} else {
			$res .= $value;
		}
		
		/* hidden field notice */
		if (userpro_field_is_viewable($key, $user_id, $args) && ( userpro_profile_data( 'hide_'.$key, $user_id ) || userpro_field_default_hidden( $key, $template, $args[ $template . '_group' ] ) ) ) {
			$res .= '<div class="userpro-help">'.sprintf(__('(Your %s will not be visible to public)','userpro'), strtolower($array['label'])).'</div>';
		}
		
		$res .= "<div class='userpro-clear'></div>";
		$res .= "</div>";
		$res .= "</div><div class='userpro-clear'></div>";
		
		}
		
		return $res;
	}