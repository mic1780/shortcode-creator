<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

//if we have a shortcode we want to test, we need to add it to our shortcodes
if (file_exists( SCODE_PLUGIN_DIR . 'includes/shortcodes/scode_test.php' )) {
	require_once( SCODE_PLUGIN_DIR . 'includes/shortcodes/scode_test.php' );
}//END IF

global $nL;

scode_add_stylesheet('scode_stylesheet', 'style.css');
scode_add_script('scode_create', 'create.js', array('jquery-core'));

//glob the shortcode files and parse the contents.

?>
<div id="scode-admin">
	<?php require( SCODE_PLUGIN_DIR . 'pages/successHandlers.php' ); ?>
	<?php require( SCODE_PLUGIN_DIR . 'pages/errorHandlers.php' ); ?>
	<h1>
		<?php echo get_admin_page_title(); ?>
	</h1>
	<hr>
	<h2>
		Current Shortcodes
	</h2>
	<hr>
	<h2>
		New Shortcode
	</h2>
	<form action="<?php echo $this->page; ?>&pa=create" method="post">
		<table class="padCells">
			<thead>
				<tr>
					<th>Name</th>
					<th>Attributes</th>
					<th>Default Values</th>
					<th>Function code</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="text" name="newCode[Name]" placeholder="Name" />
					</td>
					<td>
						<textarea name="newCode[Attributes]" placeholder="One per line"></textarea>
					</td>
					<td>
						<textarea name="newCode[AttrDefaults]" placeholder="One per line"></textarea>
					</td>
					<td>
						<textarea name="newCode[FunctionCode]" placeholder=""></textarea>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="100%" class="vt c">
						<input type="submit" name="submit" value="Create" />
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
	<hr>
	<h2>Test A Shortcode</h2>
	<div id="scodeTestArea">
		<?php if (shortcode_exists('scode_test')) { echo do_shortcode('[scode_test]'); } ?>
	</div>
</div>