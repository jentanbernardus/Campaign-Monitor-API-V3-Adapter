<?php
/**
 * Adapter for the campaign monitor v3 api
 *
 * @author Andrew Lowther <randomsite@gmail.com>
 * @package Campaign Monitor
 * @subpackage API Adapter
 */
class CampaignMonitor {
	
	/**
	 * Campaign monitor object variable
	 * 
	 * @var _cm
	 * @access public
	 */
	public $_campaign;
	
	/**
	 * Methods excluded from caching
	 *
	 * @var _exclude_method
	 * @access public
	 */
	public $_exclude_methods = array('create', 'send');
	
	/**
	 * Base path for the campaign monitor api
	 *
	 * @var _base_path
	 * @access private
	 */
	private static $_base_path;
	
	/**
	 * Campaign monitor api key
	 *
	 * @var _apikey
	 * @access private
	 */
	private $_apikey;
	
	/**
	 * Cache location and length
	 * 
	 * @var _cachelocation _cachelength
	 * @access private
	 */
	private $_cachelocation;
	private $_cachelength;
	
	/**
	 * Constructor method
	 *
	 * @param int/string $api_key
	 *	Required for campaign monitor to do anything
	 * @param string $base_path
	 *	Base path for the campaign monitor api, if different 
	 *	from default
	 * @param bool $secure
	 *	If this is true, then the https protocol
	 *	will be used rather than http
	 *
	 * @return object $campaign_monitor
	 */
	public function __construct( $api_key, $base_path = false, $secure = false ) {
		$this->set_base_path($base_path ? $base_path : dirname(__file__) . "/campaignmonitor/v3/");
		$this->set_api_key($api_key);
		$this->_campaign = self::Load('general', 'CS_REST_General', $this->get_api_key(), $secure ? 'https' : 'http');
	}
	
	/**
	 * Set up the caching options
	 * 
	 * @param string $location
	 * 	This MUST contain a trailing slash, or the caching will break
	 * @param int $length
	 */
	public function set_cache_options( $location, $length ) {
		$this->set_cache_location($location);
		$this->set_cache_length($length);
	}
	
	/**
	 * Client object generation method
	 * 
	 * @param $client_id
	 * 	This argument is required and cannot be passed as null
	 * @param bool $secure
	 *	If this is true, then the https protocol
	 *	will be used rather than http
	 *
	 * @return client object
	 */
	public function client( $client_id, $secure = false ) {
		$this->_campaign = self::Load('clients', 'CS_REST_Clients', $client_id, $this->get_api_key(), $secure ? 'https' : 'http');
	}
	
	/**
	 * List object generation method
	 * 
	 * @param string $list_id
	 *	This argument can be passed as null
	 * @param bool $secure
	 *	If this is true, then the https protocol
	 *	will be used rather than http
	 *
	 * @return list object
	 *
	 * @notes
	 *	This method uses the name cm_list as list
	 *	is something used by php itself.
	 */
	public function cm_list( $list_id = null, $secure = false ) {
		return $this->_campaign = self::Load('lists', 'CS_REST_Lists', $list_id, $this->get_api_key(), $secure ? 'https' : 'http');
	}
	
	/**
	 * Campaign object generation method
	 *
	 * @param string $campaign_id
	 *	This argument can be passed as null
	 * @param bool $secure
	 *	If this is true, then the https protocol
	 *	will be used rather than http
	 *
	 * @return campaign object
	 */
	public function campaign( $campaign_id = null, $secure = false ) {
		return $this->_campaign = self::Load('campaigns', 'CS_REST_Campaigns', $campaign_id, $this->get_api_key(), $secure ? 'https' : 'http');
	}
	
	/**
	 * Load arbitrary campaign monitor objects other than the
	 * pre-defined ones in this file.
	 *
	 * @param string $extension
	 *	Name for the campaign monitor api file
	 *	e.g. campaigns
	 * @param string $class_name
	 *	Class to map object to
	 * @param $id
	 *	Most campaign monitor api methods require
	 *	an id to be passed over, set it here
	 * @param $secure
	 *	If this is true, then the https protocol
	 *	will be used rather than http
	 *
	 * @return object
	 */
	public function load_arbitrary( $extension, $class_name, $id, $secure ) {
		return $this->_campaign = self::Load($extension, $class_name, $id, $this->get_api_key(), $secure ? 'https' : 'http');
	}
	
	/**
	 * Set the api key
	 */
	public function set_api_key( $key ) {
		$this->_api_key = $key;
	}
	
	/**
	 * Get the current api key
	 */
	public function get_api_key() {
		return $this->_api_key;
	}
	
	/**
	 * Set the base path
	 */
	public function set_base_path( $path ) {
		self::$_base_path = $path;
	}
	
	/**
	 * Get the current base path
	 */
	public function get_base_path() {
		return self::$_base_path;
	}
	
	/**
	 * Get all campaign monitor object variables
	 *
	 * @param void
	 *
	 * @return array class_vars
	 */
	public function get_objects() {
		return $this->_campaign;
	}
	
	/**
	 * Set the cache location
	 *
	 * @param string $directory
	 * 	MUST contain a trailing slash
	 */
	public function set_cache_location( $directory ) {
		$this->_cachelocation = $directory;
	}
	
	/**
	 * Get the current cache location
	 */
	public function get_cache_location() {
		return $this->_cachelocation;
	}
	
	/**
	 * Set the cache length
	 */
	public function set_cache_length( $length ) {
		$this->_cachelength = $length;
	}
	
	/**
	 * Get the cache length
	 */
	public function get_cache_length() {
		return $this->_cachelength;
	}
	
	/**
	 * Add a method to the cache exclusion
	 */
	public function add_cache_exclusion($method) {
		$this->_exclude_methods[] = $method;
	}
	
	/**
	 * Get the methods excluded from caching
	 */
	public function get_exclusions() {
		return $this->_exclude_methods;
	}
	
	/**
	 * Call method for this class
	 *
	 * @param string $method
	 *	Generated by the magic method
	 * @param mixed $params
	 *	Generated by the magic method
	 *
	 * @return called method
	 */
	public function __call( $method, $params ) {
		if(!in_array($method, $this->get_exclusions()) && $data = $this->get_cache($method)) {
			return $data;
		}
		
		if(method_exists($this, $method)) {
			$objects = call_user_func_array(array($this, $method), &$params);
		} elseif(method_exists($this->_campaign, $method)) {
			$objects = call_user_func_array(array($this->_campaign, $method), &$params);
		} else {
			return false;
		}

		if(!in_array($method, $this->get_exclusions())) {
			// Generate a cache from the call
			$this->generate_cache($method, $objects);
		}
		
		return $objects;
	}
	
	/**
	 * Get cache for campaign monitor calls
	 *
	 * @param string $methodname
	 *	Method name in cleartype so that a filename
	 *	can be created from a hash and checked against
	 *
	 * @return string $cachedata or false
	 */
	public function get_cache( $methodname ) {
		$cache_location = $this->generate_cache_location($methodname);
		
		// Check if the file exists and is within the cache length
		if(file_exists($cache_location) && (filemtime($cache_location) + $this->get_cache_length()) > time()) {
			return unserialize(file_get_contents($cache_location));
		} else {
			return false;
		}
	}
	
	/**
	 * Method to generate a cache file
	 *
	 * @param string $methodname
	 *	Used to create a hash for the name of the cachefile
	 * @param mixed $data
	 *	Simplexml will cause a problem here as we're
	 *	only going to serialize, and simplexml
	 *	will not serialize, luckily, campaign monitor
	 *	defaults to json
	 */
	public function generate_cache( $methodname, $data ) {
		$cache_location = $this->generate_cache_location($methodname);
		
		// Touch the cache file to make sure it exists
		touch($cache_location);
		$cache_pointer = fopen($cache_location, 'r+');
		if(flock($cache_pointer, LOCK_EX)) {
			ftruncate($cache_pointer, 0);
			flock($cache_pointer, LOCK_UN);
			fwrite($cache_pointer, serialize($data));
			fclose($cache_pointer);
		}
	}
	
	/**
	 * Generate a cache file name and full cache location
	 */
	public function generate_cache_location( $methodname ) {
		// Create the file name from a hash or the method
		$cachefile = md5($methodname) . '.cache';
		// Get the cache location
		$cache_location = $this->get_cache_location();
		
		// Merge the two
		return $cache_location . $cachefile;
	}
	
	/**
	 * Method to load classes from the v3 api
	 *
	 * @access private
	 *
	 * @param string $extension
	 *	Do not include the extension, for example, if requesting
	 *	the campaign methods simply pass 'campaigns' rather than
	 *	'crest_campaigns.php'
	 * @param string $class
	 *	Class to map the return value to
	 * @param mixed transient $argv
	 *	This is to pass any extra parameters required by the class to the loader.
	 *
	 * @return new $class
	 */
	private static function Load( $extension, $class ) {
		// Get all passed arguments, 
		$argv = func_get_args();
		// Shift off the $extension and $class arguments
		array_shift($argv);
		array_shift($argv);
		
		// Load the requested file, creating a base path first
		$base_path = self::get_base_path();
		// Create the path to the file from the extension
		$api_file = sprintf($base_path . 'csrest_%s.php', $extension);
		
		// Check the file exists before including it
		if(file_exists($api_file)) {
			require_once $api_file;
			
			// If there are no arguments, call the class
			if(!count($argv)) {
				$class = new $class();
				return $class;
			} else {
				// Otherwise create a reflection class and pass the arguments through.
				$class = new ReflectionClass($class);
				return $class->newInstanceArgs($argv);
			}
		} else {
			throw new Exception("File csrest_{$extension}.php does not exist in {$api_file}");
		}
	}
	
}

?>