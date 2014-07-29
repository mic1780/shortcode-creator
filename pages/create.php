<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;

scode_add_stylesheet('scode_stylesheet', 'style.css');
scode_add_script('scode_general_settings', 'general_settings.js');

//glob the shortcode files and parse the contents.

?>
<div id="scode-admin">
	<!--Success handler include here-->
	<!--Error handler include here-->
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
		<table>
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
		</table>
	</form>
</div>