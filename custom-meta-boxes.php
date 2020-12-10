<?php
class customMetaBoxes {
	function __construct($options) {
		$this->options = $options;
		$this->prefix = $this->options['id'] .'_';
		$this->plugin_path = str_replace('/classes', '', plugin_dir_path(__FILE__));
		$this->plugin_url = str_replace('/classes', '', plugin_dir_url(__FILE__));

		add_action( 'add_meta_boxes', array( &$this, 'create' ) );
		add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'include_scripts' ) );
	}

	/**
	 * Init custom metabox
	 */
	function create() {
		foreach ($this->options['post'] as $post_type) {
			if (current_user_can( $this->options['cap'])) {
				add_meta_box($this->options['id'], $this->options['name'], array(&$this, 'fill'), $post_type, $this->options['pos'], $this->options['pri']);
			}
		}
	}

	/**
	 * Print custom metabox
	 */
	function fill(){
		global $post;

		wp_nonce_field( $this->options['id'], $this->options['id'].'_wpnonce', false, true );
	?>

		<table class="form-table">
			<tbody>
				<?php foreach ( $this->options['args'] as $param ) {
					if (!current_user_can($param['cap'])) {
						continue;
					} ?>

					<tr>
						<?php if (!$value = get_post_meta($post->ID, $this->prefix .$param['id'] , true)){
							$value = $param['std'];
						}

						switch ($param['type']){
                            case 'media':
                                $image = $value == '' ? '' : wp_get_attachment_url($value);
                            ?>
                                <th scope="row">
                                    <label for="<?php echo $this->prefix .$param['id'] ?>">
			                            <?php echo $param['title'] ?>
                                    </label>
                                </th>

                                <td>
                                    <input name="<?php echo $this->prefix .$param['id'] ?>" class="custom_media_input" type="hidden" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>">
                                    <img src="<?php echo $image; ?>" class="custom_media_image" style="<?php echo $image ? '' : 'display:none'?>" width="350">
                                    <br>

                                    <a href="javascript:void(0)" class="button button-secondary custom_media_upload_btn" onclick="CMB_uploadImage(this)">
                                        <?php _e('Upload Image'); ?>
                                    </a>

                                    <a href="javascript:void(0)" class="button button-link-delete custom_media_delete_btn" style="<?php echo $image ? '' : 'display:none'?>" onclick="CMB_deleteImage(this)">
	                                    <?php _e('Delete Image'); ?>
                                    </a>
                                </td>
                            <?php break;

							case 'text': ?>
								<th scope="row">
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<?php echo $param['title'] ?>
									</label>
								</th>

								<td>
									<input name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>" placeholder="<?php echo $param['placeholder'] ?>" class="regular-text" /><br />
									<span class="description"><?php echo $param['desc'] ?></span>
								</td>
							<?php break;

							case 'textarea': ?>
								<th scope="row">
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<?php echo $param['title'] ?>
									</label>
								</th>

								<td>
									<textarea name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>" value="<?php echo $value ?>" placeholder="<?php echo $param['placeholder'] ?>" class="large-text" />
										<?php echo $value ?>
									</textarea>
									<br />
									<span class="description">
										<?php echo $param['desc'] ?>
									</span>
								</td>
							<?php break;

							case 'checkbox': ?>
								<th scope="row">
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<?php echo $param['title'] ?>
									</label>
								</th>

								<td>
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<input name="<?php echo $this->prefix .$param['id'] ?>" type="<?php echo $param['type'] ?>" id="<?php echo $this->prefix .$param['id'] ?>"<?php echo ($value=='on') ? ' checked="checked"' : '' ?> />
										<?php echo $param['desc'] ?>
									</label>
								</td>
							<?php break;

							case 'select': ?>
								<th scope="row">
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<?php echo $param['title'] ?>
									</label>
								</th>

								<td>
									<label for="<?php echo $this->prefix .$param['id'] ?>">
										<select name="<?php echo $this->prefix .$param['id'] ?>" id="<?php echo $this->prefix .$param['id'] ?>">
											<option>...</option>
											<?php foreach ($param['args'] as $val => $name){ ?>
												<option value="<?php echo $val ?>"<?php echo ( $value == $val ) ? ' selected="selected"' : '' ?>>
													<?php echo $name ?>
												</option>
											<?php } ?>
										</select>
									</label>

									<br />
									<span class="description"><?php echo $param['desc'] ?></span>
								</td>
							<?php break;
						} ?>
					</tr>
				<?php } ?>

			</tbody>
		</table>
	<?php }

	function include_scripts(){
        if (!did_action('wp_enqueue_media')){
            wp_enqueue_media();
        }

        wp_enqueue_script(
            'custom_meta_boxes',
            $this->plugin_url.'js/custom-meta-boxes.js',
            array('jquery'),
            null,
            false
        );
	}

	/**
	 * Saving metaboxes data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return bool|void
	 */
	function save($post_id, $post){
		if ( !wp_verify_nonce( $_POST[ $this->options['id'].'_wpnonce' ], $this->options['id'] ) ) {
			return false;
		}

		if ( !current_user_can( 'edit_post', $post_id ) ){
			return false;
		}

		if ( !in_array($post->post_type, $this->options['post'])){
			return;
		}

		foreach ( $this->options['args'] as $param ) {
			if ( current_user_can( $param['cap'] ) ) {
				if ( isset( $_POST[ $this->prefix . $param['id'] ] ) && trim( $_POST[ $this->prefix . $param['id'] ] ) ) {
					update_post_meta( $post_id, $this->prefix . $param['id'], trim($_POST[ $this->prefix . $param['id'] ]) );
				} else {
					delete_post_meta( $post_id, $this->prefix . $param['id'] );
				}
			}
		}
	}
}