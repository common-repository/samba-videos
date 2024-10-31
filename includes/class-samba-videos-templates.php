<?php 

class SV_Admin_Templates {

	public function __construct() {
		add_action( 'admin_footer', array( $this, 'add_templates' ) );
	}

	public function add_templates() {
	?>
		<!-- TEMPLATE: Media List -->
		<script type="text/html" id="tmpl-samba-videos-media-list">
			<div class="samba-videos-gallery">
				<div class="media-toolbar">
					<input type="text" class="search"  placeholder="<?php _e('Search', 'samba-videos'); ?>" name="search"></input>
					<button class="button button-default button-filter" type="button" ><?php _e('Filter', 'samba-videos'); ?></button>
				</div>

				<div class="sv-loading"></div>
				<div class="no-content hidden"><?php _e('Have been not found any media.', 'samba-videos'); ?></div>
				<ul id="sv-media-list" class="attachments ui-sortable ui-sortable-disabled" tabindex="-1"></ul>
			</div>
		</script>
		
		<!-- TEMPLATE: Media Item -->
		<script type="text/html" id="tmpl-samba-videos-media-item">
			<div class="attachment-preview type-video landscape">
				<div class="thumbnail">
					<div class="centered">
						<img src="{{{ data.media.thumbs.length ?  data.media.thumbs[0].url : '' }}}" draggable="false" alt="{{{ data.media.title }}}">
					</div>
					<div class="filename">
						<div>{{{ data.media.title }}}</div>
					</div>
				</div>

			</div>
		</script>

		<!-- TEMPLATE: Media Preview -->
		<script type="text/html" id="tmpl-samba-videos-media-preview">
			<div class="side-left">
				<div class="sv-container borderBottom">
					<iframe allowfullscreen webkitallowfullscreen mozallowfullscreen 
					width="640" 
					height="360" 
					src="{{{ data.settings.player_url }}}/{{{ data.settings.sv_playerKey }}}/{{{ data.media.id }}}?cast=true&html5=true" 
					scrolling="no" 
					frameborder="0"></iframe>
				</div>
				<div class="sv-container">
					<h2 class="title">{{{ data.media.title }}}</h2>
					<p class="description">{{{ data.media.description }}}</p>
				</div>
			</div>
			<div class="side-right">
				<h2>Player Settings</h2>
				<hr/>
				<table>
					<tbody>
						<tr>
							<th>Width</th>
							<td><input type="text" name="sv_width" value="{{{ data.settings.sv_width }}}"></td>
						</tr>
						<tr>
							<th>Height</th>
							<td><input type="text" name="sv_height" value="{{{ data.settings.sv_height }}}"></td>
						</tr>
						<tr>
							<th></th>
							<td><label for="sv_autoStart"><input type="checkbox" id="sv_autoStart" name="sv_autoStart" {{{ data.settings.sv_autoStart ? 'checked' : '' }}}>Auto start</label></td>
						</tr>
					</tbody>
				</table>
			</div>
		</script>

	<?php
	}

}
