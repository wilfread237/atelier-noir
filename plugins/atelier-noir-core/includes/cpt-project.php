<?php
/**
 * CPT Project — full-screen media sections.
 *
 * @package atelier-noir-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register an_project post type.
 */
function atelier_noir_register_project_cpt() {
	register_post_type(
		'an_project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'atelier-noir-core' ),
				'singular_name' => __( 'Project', 'atelier-noir-core' ),
				'add_new_item'  => __( 'Add Project', 'atelier-noir-core' ),
				'edit_item'     => __( 'Edit Project', 'atelier-noir-core' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'has_archive'  => false,
			'menu_icon'    => 'dashicons-portfolio',
			'supports'     => array( 'title', 'thumbnail' ),
			'rewrite'      => array(
				'slug'       => 'projects',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'atelier_noir_register_project_cpt' );

/**
 * @param int $post_id Post ID.
 * @return array<int, array{type:string, url:string, attachment_id:int}>
 */
function atelier_noir_get_project_sections( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return array();
	}
	$raw = get_post_meta( $post_id, '_an_project_sections', true );
	if ( ! is_string( $raw ) || '' === $raw ) {
		return array();
	}
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}
	$out = array();
	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$type = isset( $row['type'] ) && 'video' === $row['type'] ? 'video' : 'image';
		$att  = absint( $row['attachment_id'] ?? 0 );
		$url  = isset( $row['url'] ) ? esc_url_raw( (string) $row['url'] ) : '';
		if ( $att ) {
			$att_url = wp_get_attachment_url( $att );
			if ( is_string( $att_url ) && '' !== $att_url ) {
				$url = $att_url;
			}
		}
		if ( '' === $url ) {
			continue;
		}
		$out[] = array(
			'type'          => $type,
			'url'           => $url,
			'attachment_id' => $att,
		);
	}
	return $out;
}

/**
 * Sections for front display — media sections only (no featured-image invent).
 *
 * @param int $post_id Post ID.
 * @return array<int, array{type:string, url:string, attachment_id?:int}>
 */
function atelier_noir_get_project_sections_for_display( $post_id ) {
	return atelier_noir_get_project_sections( $post_id );
}

/**
 * If Featured Image is empty, use the first section image as thumbnail (admin only).
 *
 * @param int $post_id Post ID.
 */
function atelier_noir_project_sync_featured_from_first_section( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || has_post_thumbnail( $post_id ) ) {
		return;
	}
	$sections = atelier_noir_get_project_sections( $post_id );
	if ( empty( $sections ) ) {
		return;
	}
	$first = $sections[0];
	if ( empty( $first['attachment_id'] ) || 'image' !== ( $first['type'] ?? 'image' ) ) {
		return;
	}
	$att = (int) $first['attachment_id'];
	if ( $att > 0 && 'attachment' === get_post_type( $att ) ) {
		set_post_thumbnail( $post_id, $att );
	}
}
add_action( 'save_post_an_project', 'atelier_noir_project_sync_featured_from_first_section', 20 );

/**
 * Meta box UI.
 */
function atelier_noir_project_sections_metabox() {
	add_meta_box(
		'an_project_sections',
		__( 'Project sections (full-screen media)', 'atelier-noir-core' ),
		'atelier_noir_project_sections_metabox_render',
		'an_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'atelier_noir_project_sections_metabox' );

/**
 * @param WP_Post $post Post.
 */
function atelier_noir_project_sections_metabox_render( $post ) {
	wp_nonce_field( 'an_project_sections_save', 'an_project_sections_nonce' );
	$sections = atelier_noir_get_project_sections( $post->ID );
	if ( empty( $sections ) ) {
		$sections = array( array( 'type' => 'image', 'url' => '', 'attachment_id' => 0 ) );
	}
	?>
	<p class="description"><?php esc_html_e( 'Add one or more full-screen images or videos. Use the media library button to pick files. Text is optional — media carries the story. Title is admin-only (not shown on the site). Use the Publish box Preview to check the single project.', 'atelier-noir-core' ); ?></p>
	<div id="an-project-sections" data-sections="<?php echo esc_attr( wp_json_encode( $sections ) ); ?>"></div>
	<p><button type="button" class="button" id="an-project-add-section"><?php esc_html_e( 'Add section', 'atelier-noir-core' ); ?></button></p>
	<script>
	(function(){
		var root = document.getElementById('an-project-sections');
		if (!root) return;
		var data = [];
		try { data = JSON.parse(root.getAttribute('data-sections') || '[]'); } catch(e) {}

		function openMediaLib(index, type) {
			var frame = wp.media({
				title: type === 'video' ? 'Select video' : 'Select image',
				button: { text: 'Use this file' },
				multiple: false,
				library: type === 'video' ? { type: 'video' } : { type: 'image' }
			});
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				data[index].url           = att.url;
				data[index].attachment_id = att.id;
				data[index].type          = att.type === 'video' ? 'video' : 'image';
				render();
			});
			frame.open();
		}

		function render(){
			root.innerHTML = '';
			data.forEach(function(row, i){
				var wrap = document.createElement('div');
				wrap.style.cssText = 'margin-bottom:14px;padding:10px;background:#f9f9f9;border:1px solid #ddd;';

				var preview = '';
				if (row.url) {
					if (row.type === 'video') {
						preview = '<video src="' + row.url + '" style="max-height:80px;display:block;margin-bottom:6px" muted></video>';
					} else {
						preview = '<img src="' + row.url + '" style="max-height:80px;display:block;margin-bottom:6px">';
					}
				}

				wrap.innerHTML = '<strong>Section ' + (i+1) + '</strong><br>' + preview +
					'<input type="hidden" name="an_project_section_url[]" value="' + (row.url||'') + '">' +
					'<input type="hidden" name="an_project_section_attachment_id[]" value="' + (row.attachment_id||0) + '">' +
					'<select name="an_project_section_type[]" style="margin-right:6px">' +
						'<option value="image"' + (row.type==='video'?'':' selected') + '>Image</option>' +
						'<option value="video"' + (row.type==='video'?' selected':'') + '>Video</option>' +
					'</select>' +
					' <button type="button" class="button an-pick-media">Choose from media library</button>' +
					' <button type="button" class="button-link-delete an-remove-section" style="margin-left:8px">Remove</button>';

				wrap.querySelector('.an-pick-media').addEventListener('click', function(){
					var type = wrap.querySelector('select').value;
					openMediaLib(i, type);
				});
				wrap.querySelector('select').addEventListener('change', function(){
					data[i].type = this.value;
				});
				wrap.querySelector('.an-remove-section').addEventListener('click', function(){
					data.splice(i,1);
					if (!data.length) data.push({type:'image',url:'',attachment_id:0});
					render();
				});
				root.appendChild(wrap);
			});
		}
		render();
		document.getElementById('an-project-add-section').addEventListener('click', function(){
			data.push({type:'image',url:'',attachment_id:0});
			render();
		});
	})();
	</script>
	<?php
}

/**
 * Save sections meta.
 *
 * @param int $post_id Post ID.
 */
function atelier_noir_project_sections_save( $post_id ) {
	if ( ! isset( $_POST['an_project_sections_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['an_project_sections_nonce'] ) ), 'an_project_sections_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$urls   = isset( $_POST['an_project_section_url'] ) ? (array) wp_unslash( $_POST['an_project_section_url'] ) : array();
	$types  = isset( $_POST['an_project_section_type'] ) ? (array) wp_unslash( $_POST['an_project_section_type'] ) : array();
	$att_ids = isset( $_POST['an_project_section_attachment_id'] ) ? (array) wp_unslash( $_POST['an_project_section_attachment_id'] ) : array();
	$out    = array();
	foreach ( $urls as $i => $url ) {
		$url = esc_url_raw( trim( (string) $url ) );
		if ( '' === $url ) {
			continue;
		}
		$type       = isset( $types[ $i ] ) && 'video' === $types[ $i ] ? 'video' : 'image';
		$att_id_raw = isset( $att_ids[ $i ] ) ? absint( $att_ids[ $i ] ) : 0;

		// Validate attachment: must be a real media file the current user can access.
		if ( $att_id_raw > 0
			&& 'attachment' === get_post_type( $att_id_raw )
			&& current_user_can( 'read_post', $att_id_raw )
		) {
			$att_id  = $att_id_raw;
			$url     = wp_get_attachment_url( $att_id ) ?: $url;
		} else {
			$att_id = 0;
		}

		$out[] = array(
			'type'          => $type,
			'url'           => $url,
			'attachment_id' => $att_id,
		);
	}
	update_post_meta( $post_id, '_an_project_sections', wp_json_encode( $out, JSON_UNESCAPED_UNICODE ) );
}
add_action( 'save_post_an_project', 'atelier_noir_project_sections_save' );
