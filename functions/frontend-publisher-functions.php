<?php

	/**
	Front end publisher
	functions
	**/
	
	/******************************************
	Get all categories list
	******************************************/
	function userpro_publish_categories($args){
		$taxonomies=get_taxonomies( array('public' => true) , 'names');
		if (isset($args['allowed_taxonomies'])){
			$allowed = explode(',',$args['allowed_taxonomies']);
		} else {
			$allowed = array('category','post_tag');
		}
		$taxonomies = array_intersect( $taxonomies, $allowed );
		foreach ($taxonomies as $taxonomy ) {
			$the_tax = get_taxonomy( $taxonomy );
			$terms = get_terms( $taxonomy , array('hide_empty' => 0));
			if ($terms) {
				$array["optgroup_b_{$taxonomy}"] = $the_tax->labels->name;
				foreach($terms as $term) {
						$array["" . $term->slug . "#" . $taxonomy . ""] = $term->name;
				}
				$array["optgroup_e_{$taxonomy}"] = $the_tax->labels->name;
			}
		}
		return $array;
	}
	
	/******************************************
	Get post types available for publishing
	******************************************/
	function userpro_publish_types($args){
		if (isset($args['post_type'])){
			$allowed = explode(',',$args['post_type']);
		} else {
			$allowed = array('post');
		}
		
		$types = get_post_types( array('public' => true) , 'objects');
		foreach($types as $type){
			if (in_array($type->name, $allowed ) ) {
				$array[$type->name] = $type->labels->singular_name;
			}
		}
		return $array;
	}
	
	/* Post editor */
	function userpro_post_editor($i, $class, $args) {
		?>
			<div class="userpro-field userpro-field-editor" data-key="<?php echo $class; ?>">
				<div class="userpro-input">
				<?php
					$settings = array(
						'wpautop' => true,
						'media_buttons' => false,
						'teeny' => true,
						'quicktags' => false,
						'editor_class' => $class,
						'textarea_rows' => 10,
						'tinymce' => array( 
							'content_css' => userpro_url . 'css/userpro-editor.css'
						)
					);
					$editor_id = $class;
					$content = '';
					
					wp_editor( $content, $editor_id, $settings );
				?>
				</div>
			</div><div class="userpro-clear"></div>
		<?php
	}
	
	/* Publisher fields */
	function userpro_edit_field_misc( $i, $key, $args, $label=null, $help=null, $placeholder=null ) {
		global $userpro;
		$res = null;
		
		$res .= "<div class='userpro-field' data-key='$key'>";
		
		if ($label) {
			$res .= "<div class='userpro-label'><label for='$key-$i'>".$label."</label></div>";
		}
		
		$res .= "<div class='userpro-input'>";
		
			switch($key) {

				/* set title */
				case 'post_title':
					$res .= "<input type='text' name='$key-$i' id='$key-$i' value='' placeholder='".$placeholder."' />";
					break;
					
				/* set categories */
				case 'post_categories':
					$options = userpro_publish_categories($args);
					$res .= "<select name='".$key.'-'.$i.'[]'."' multiple='multiple' class='chosen-select' data-placeholder='".$placeholder."'>";
					foreach($options as $k=>$v) {
						if (strstr($k, 'optgroup_b')) {
							$res .= "<optgroup label='$v'>";
						} elseif (strstr($k, 'optgroup_e')) {
							$res .= "</optgroup>";
						} else {
							$res .= "<option value='$k'>$v</option>";
						}
					}
					$res .= "</select>";
					break;
				
				/* set post type */
				case 'post_type':
					$options = userpro_publish_types($args);
					$res .= "<select name='".$key.'-'.$i."' id='".$key.'-'.$i."' class='chosen-select' data-placeholder='".$placeholder."'>";

					$i = 0;
					foreach($options as $k=>$v) {
						$i++;
						if ($i == 1){
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						$res .= "<option value='$k' ".$selected.">$v</option>";
					}
					
					$res .= "</select>";
					break;
				
				/* set featured image */
				case 'post_featured_image':
					$value = '<span class="userpro-pic-none">'.__('No featured image was set.','userpro').'</span><img src="" width="" height="" class="modified" />';
					$res .= "<div class='userpro-pic userpro-pic-".$key."' data-remove_text='".__('Remove','userpro')."'>".$value."</div>";
					$res .= "<div class='userpro-pic-upload' data-filetype='picture' data-allowed_extensions='png,gif,jpg,jpeg'>".__('Set Featured Image','userpro')."</div>";
					$res .= "<input type='hidden' name='$key-$i' id='$key-$i' value='' />";
					break;
					
			}
		
			if ( $help ) {
				$res .= "<div class='userpro-help'>".$help."</div>";
			}
		
		$res .= "<div class='userpro-clear'></div>";
		$res .= "</div>";
		$res .= "</div><div class='userpro-clear'></div>";
		
		return $res;
	}