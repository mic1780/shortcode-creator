<?php
//Copyright (C) 2014  Michael Cummins

if (! defined('SCODE_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

//Credit to tutsplus for most of this code
//http://code.tutsplus.com/tutorials/distributing-your-plugins-in-github-with-automatic-updates--wp-34817
class scodePluginUpdater {
	
	private $slug; // plugin slug
	private $pluginData; // plugin data
	private $username; // GitHub username
	private $repo; // GitHub repo name
	private $pluginFile; // __FILE__ of our plugin
	private $pluginActive;
	private $githubAPIResult; // holds data from GitHub
	private $accessToken; // GitHub private repo token
	
	function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' ) {
		add_filter( "pre_set_site_transient_update_plugins", array( $this, "setTransitent" ) );
		add_filter( "plugins_api", array( $this, "setPluginInfo" ), 11, 3 );
		add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );
		add_filter( "upgrader_pre_install", array( $this, "preInstall" ), 9, 2 );
		
		$this->pluginFile = $pluginFile;
		$this->username = $gitHubUsername;
		$this->repo = $gitHubProjectName;
		$this->accessToken = $accessToken;
	}//END FUNCTION
	
	// Get information regarding our plugin from WordPress
	private function initPluginData() {
		$this->slug = plugin_basename( $this->pluginFile );
		if (file_exists($this->pluginFile))
			$this->pluginData = get_plugin_data( $this->pluginFile );
	}//END PRIVATE FUNCTION
	
	// Get information regarding our plugin from GitHub
	private function getRepoReleaseInfo() {
		// Only do this once
		if ( ! empty( $this->githubAPIResult ) ) {
			return;
		}//END IF
		
		// Query the GitHub API
		$url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
		
		// We need the access token for private repos
		if ( ! empty( $this->accessToken ) ) {
			$url = add_query_arg( array( "access_token" => $this->accessToken ), $url );
		}//END IF
		
		// Get the results
		$this->githubAPIResult = wp_remote_retrieve_body( wp_remote_get( $url ) );
		if ( ! empty( $this->githubAPIResult ) ) {
			$this->githubAPIResult = @json_decode( $this->githubAPIResult );
		}//END IF
		
		// Use only the latest release
		if ( is_array( $this->githubAPIResult ) ) {
			$this->githubAPIResult = $this->githubAPIResult[0];
		}//END IF
	}//END PRIVATE FUNCTION
	
	// Push in plugin version information to get the update notification
	public function setTransitent( $transient ) {
		// If we have checked the plugin data before, don't re-check
		if ( empty( $transient->checked ) || ! file_exists($this->pluginFile) ) {
			return $transient;
		}//END IF
		
		// Get plugin & GitHub release information
		$this->initPluginData();
		$this->getRepoReleaseInfo();
		
		if ( isset( $_GET['force-check'] ) && '1' === $_GET['force-check'] ) {
			$transient->checked[$this->slug] = SCODE_VERSION;
		}//END IF
		
		// Check the versions if we need to do an update
		$doUpdate = version_compare( $this->githubAPIResult->tag_name, $transient->checked[$this->slug] );
		
		// Update the transient to include our updated plugin data
		if ( $doUpdate == 1 ) {
			$package = $this->githubAPIResult->zipball_url;
			
			// Include the access token for private GitHub repos
			if ( !empty( $this->accessToken ) ) {
				$package = add_query_arg( array( "access_token" => $this->accessToken ), $package );
			}//END IF
			
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $this->githubAPIResult->tag_name;
			$obj->url = $this->pluginData["PluginURI"];
			$obj->package = $package;
			$transient->response[$this->slug] = $obj;
		}//END IF
		
		return $transient;
	}//END PUBLIC FUNCTION
	
	// Push in plugin version information to display in the details lightbox
	public function setPluginInfo( $false, $action, $response ) {
		// Get plugin & GitHub release information
		$this->initPluginData();
		$this->getRepoReleaseInfo();
		
		// If nothing is found, do nothing
		if ( empty( $response->slug ) || $response->slug != $this->slug ) {
			return false;
		}//END IF
		
		// Add our plugin information
		$response->last_updated = $this->githubAPIResult->published_at;
		$response->slug = $this->slug;
		$response->plugin_name  = $this->pluginData["Name"];
		$response->name  = $this->pluginData["Name"];
		$response->version = $this->githubAPIResult->tag_name;
		$response->author = $this->pluginData["AuthorName"];
		$response->homepage = $this->pluginData["PluginURI"];
		
		// This is our release download zip file
		$downloadLink = $this->githubAPIResult->zipball_url;
		
		// Include the access token for private GitHub repos
		if ( !empty( $this->accessToken ) ) {
			$downloadLink = add_query_arg(array( "access_token" => $this->accessToken ), $downloadLink);
		}//END IF
		$response->download_link = $downloadLink;
		
		// We're going to parse the GitHub markdown release notes, include the parser
		require_once( dirname( __FILE__ ) . "/Parsedown.php" );
		
		// Create tabs in the lightbox
		$response->sections = array(
			'description' => $this->pluginData["Description"],
			'changelog' => class_exists( "Parsedown" )
				? Parsedown::instance()->parse( $this->githubAPIResult->body )
				: $this->githubAPIResult->body
		);
		
		// Gets the required version of WP if available
		$matches = null;
		preg_match( "/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );
		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$response->requires = $matches[1];
				}
			}
		}
		
		// Gets the tested version of WP if available
		$matches = null;
		preg_match( "/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );
		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$response->tested = $matches[1];
				}
			}
		}
		
		return $response;
	}//END PUBLIC FUNCTION
	
	//since wordpress sucks at keeping custom files, we will do it ourselves!
	public function preInstall( $true, $hook_extra ) {
		
		// Get plugin information
		$this->initPluginData();
		$this->pluginActive =	is_plugin_active( $this->slug );
		
		if ( isset($_GET['plugin']) && $_GET['plugin'] != $this->slug )
			return true;
		
		//we need to keep our custom shortcodes intact
		$allFiles =	array();
		$fileList = glob(SCODE_PLUGIN_DIR . 'includes/shortcodes/*.php');
		if (count($fileList) > 0) {
			foreach ($fileList as $filename) {
				if (end( explode('/', $filename) ) != 'index.php')
					$allFiles[] =	$filename;
			}//END FOREACH LOOP
		}//END IF
		
		if (count($allFiles) > 0) {
			global $wp_filesystem;
			$contentdir =	trailingslashit( $wp_filesystem->wp_content_dir() );
			$wp_filesystem->mkdir( $contentdir . 'scode-temp' );
			foreach ($allFiles as $filepath) {
				$fileName = end( explode('/', $filepath) );
				$wp_filesystem->copy( $filepath, $contentdir . 'scode-temp/' . $fileName );
			}//END FOREACH LOOP
		}//END IF
		
		//$wp_filesystem->move( SCODE_PLUGIN_DIR . 'includes/shortcodes', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'scode_temp');
		
		return true;
	}//END PUBLIC FUNCTION
	
	// Perform additional actions to successfully install our plugin
	public function postInstall( $true, $hook_extra, $result ) {
		// Get plugin information
		$this->initPluginData();
		
		if ( isset($_GET['plugin']) && $_GET['plugin'] != $this->slug )
			return $result;
		
		// Since we are hosted in GitHub, our plugin folder would have a dirname of
		// reponame-tagname change it to our original one:
		global $wp_filesystem;
		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
		$wp_filesystem->move( $result['destination'], $pluginFolder );
		$result['destination'] = $pluginFolder;
		
		//move back our custom files
		$contentdir =	trailingslashit( $wp_filesystem->wp_content_dir() );
		if ( file_exists($contentdir . 'scode-temp') ) {
			$files =	glob($contentdir . 'scode-temp/*.php');
			if (count($files) > 0) {
				foreach ($files as $filepath) {
					$fileName =	end( explode('/', $filepath) );
					$wp_filesystem->move( $filepath, SCODE_PLUGIN_DIR . 'includes/shortcodes/' . $fileName );
				}//END FOREACH LOOP
			}//END IF
			$wp_filesystem->rmdir( $contentdir . 'scode-temp' );
		}//END IF
		//$wp_filesystem->move(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'scode_temp', SCODE_PLUGIN_DIR . 'includes/shortcodes');
		
		// Re-activate plugin if needed
		if ( $this->pluginActive ) {
			//activate plugin without activation hook
			$activate = activate_plugin( $this->slug, NULL, false, true );
		}//END IF
		
		return $result;
	}//END PUBLIC FUNCTION
	
}//END CLASS
?>