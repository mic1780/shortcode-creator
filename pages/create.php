<?php

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL, $excludeThese;

//if we have a shortcode we want to test, we need to add it to our shortcodes
if (file_exists( SCODE_PLUGIN_DIR . 'includes/shortcodes/scode_test.php' )) {
	require_once( SCODE_PLUGIN_DIR . 'includes/shortcodes/scode_test.php' );
}//END IF

scode_add_stylesheet('scode_stylesheet', 'style.css');
scode_add_script('scode_create', 'create.js', array('jquery-core'));

$shortcodeRows =	'';
$shortcodes =	array();
scode_read_code_files($shortcodes);

if (count($shortcodes) > 0) {
	foreach ($shortcodes as $key => $codeInfo) {
		$shortcodeRows .=	'' .
								'<tr id="codeRow-' . $key . '">' . $nL .
									'<td class="vt">' .
										$codeInfo['Name'] .
										//'<input type="hidden" name="code['.$key.'][Name]" value="' . $codeInfo['Name'] . '" />' .
									'</td>' . $nL .
									'<td class="vt">' .
										nl2br($codeInfo['Attributes']) .
										//'<input type="hidden" name="code['.$key.'][Attributes]" value="' . $codeInfo['Attributes'] . '" />' .
									'</td>' . $nL .
									'<td class="vt">' .
										nl2br($codeInfo['AttrDefaults']) .
										//'<input type="hidden" name="code['.$key.'][AttrDefaults]" value="' . $codeInfo['AttrDefaults'] . '" />' .
									'</td>' . $nL .
									'<td class="vt">' .
										nl2br($codeInfo['Deps']) .
										//'<input type="hidden" name="code['.$key.'][Deps]" value="' . $codeInfo['Deps'] . '" />' .
									'</td>' . $nL .
									'<td class="vt l">' .
										'<textarea name="code['.$key.'][FunctionCode]" rows="10" cols="35" readonly>' . scode_format_code($codeInfo['FunctionCode'], 'read') . '</textarea>' .
									'</td>' . $nL .
									'<td class="vt l">' .
										'<button type="button" class="allowEdits" value="' . $key . '">Make Changes</button>' .
									'</td>' . $nL .
								'</tr>' . $nL .
								'';
	}//END FOREACH LOOP
} else {
	$shortcodeRows .=	'' .
							'<tr>' . $nL .
								'<td colspan="100%">You have not made any shortcodes yet.</td>' . $nL .
							'</tr>' . $nL .
							'';
}//END IF


?>
<div id="scode-admin">
	<?php require(SCODE_PLUGIN_DIR . 'pages/successHandlers.php'); ?>
	<?php require(SCODE_PLUGIN_DIR . 'pages/errorHandlers.php'); ?>
	<h1>
		<?php echo get_admin_page_title(); ?>
	</h1>
	<hr>
	<h2>
		Current Shortcodes
	</h2>
	<form id="scodeEditForm" action="<?php echo $this->page; ?>&pa=edit" method="post">
		<table class="padCells">
			<thead>
				<tr>
					<th>Name</th>
					<th>Attributes</th>
					<th>Default Values</th>
					<th>Dependencies</th>
					<th>Function Code</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $shortcodeRows; ?>
			</tbody>
			<tfoot id="editTableFooter" class="dn">
				<tr>
					<td colspan="100%" class="vb c">
						<input type="submit" name="submit" value="Save Changes" />
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
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
					<th><a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script#Default_Scripts_Included_and_Registered_by_WordPress" target="_blank">Dependencies</a></th>
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
						<textarea name="newCode[Deps]" placeholder="One per line"></textarea>
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
	<?php ob_start(NULL, 0, PHP_OUTPUT_HANDLER_CLEANABLE); ?>
	<div id="scodeTestArea">
		<?php if (shortcode_exists('scode_test')) { echo do_shortcode('[scode_test]'); } ?>
	</div>
	<?php echo do_shortcode( ob_get_clean() ); ?>
</div>