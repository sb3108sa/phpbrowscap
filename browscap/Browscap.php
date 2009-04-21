<?php

/**
 * Browscap.ini parsing class with caching and update capabilities
 *
 * PHP version 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * This is the core of Browscap, for fast online usage.
 *
 * @package    Browscap
 * @author     Jonathan Stoppani <st.jonathan@gmail.com>
 * @copyright  Copyright (c) 2006-2008 Jonathan Stoppani
 * @version    0.7
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://garetjax.info/projects/browscap/
 */
class Browscap
{
	/**
	 * Current version of the class.
	 */
	const VERSION					 = '0.7';

	/**
	 * Options for regex patterns.
	 *
	 * REGEX_DELIMITER:  Delimiter of all the regex patterns in the whole class.
	 * REGEX_MODIFIERS:  Regex modifiers.
	 */
	const REGEX_DELIMITER   = '@';
	const REGEX_MODIFIERS   = 'i';
	/**
	 * Flag to enable only lowercase indexes in the result.
	 * The cache has to be rebuilt in order to apply this option.
	 *
	 * @var bool
	 */
	public $lowercase       = false;


	/**
	 * The file that specify caching strategy
	 */
	public $cacheLevelFile = '/BrowscapCacheStrategy.php';

	/**
	 * Where to store the cached PHP arrays.
	 * We can have multi-level of caches.
	 * The lower the level, the smaller the size.
	 * The last level should include every browser.
	 *
	 * @var array of string
	 */
	public $cacheFilenames   = array();

	/**
	 * Path to the cache directory
	 *
	 * @var string
	 */
	public $cacheDir        = null;

	/**
	 * Flag to be set to true after loading the cache
	 * The level of cache that is loaded
	 *
	 * @var integer
	 */
	private $_cacheLoaded   = -1;

	/**
	 * Where to store the value of the included PHP cache file
	 *
	 * @var array
	 */
	protected $_userAgents    = array();
	protected $_browsers      = array();
	protected $_patterns      = array();
	protected $_properties    = array();

	/**
	 * Constructor class, checks for the existence of (and loads) the cache and
	 * if needed updated the definitions
	 *
	 * @param string $cache_dir
	 */
	public function __construct($cache_dir)
	{
		// has to be set to reach E_STRICT compatibility, does not affect system/app settings
		date_default_timezone_set(date_default_timezone_get());

		if (!isset($cache_dir)) {
			throw new Browscap_Exception(
				'You have to provide a path to read/store the browscap cache file'
			);
		}

		$cache_dir = realpath($cache_dir);

		// Is the cache dir really the directory or is it directly the file?
		if (substr($cache_dir, -4) === '.php') {
			$this->topCacheFilename = null;
			$this->cacheFilenames = array(basename($cache_dir));
			$this->cacheDir = dirname($cache_dir);
		} else {
			$this->cacheDir = $cache_dir;
			$full_path = $cache_dir . $this->cacheLevelFile;
			require $full_path;
			$this->cacheFilenames = array_keys($cache_level);
		}

		$this->cacheDir .= DIRECTORY_SEPARATOR;
	}


	/**
	 * Gets the information about the browser by User Agent
	 *
	 * @param string $user_agent   the user agent string
	 * @param bool   $return_array whether return an array or an object
	 * @throws Browscap_Exception
	 * @return stdObject the object containing the browsers details. Array if
	 *                   $return_array is set to true.
	 */
	public function getBrowser($user_agent = null, $return_array = false)
	{
		// Load the cache at the first request
		if ($this->_cacheLoaded < 0) {
			if (method_exists($this, 'checkCache')) {
					$this->checkCache();
			}
			$this->_loadCache(0);
		}

		// Automatically detect the useragent
		if (!isset($user_agent)) {
			if (isset($_SERVER['HTTP_USER_AGENT'])) {
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
			} else {
				$user_agent = '';
			}
		}

		$browser = array();
		do {
			foreach ($this->_patterns as $key => $pattern) {
				if (preg_match($pattern . 'i', $user_agent)) {
					$browser = $this->constructBrowser($user_agent, $key, $pattern);
					break;
				}
			}
		} while (empty($browser) && $this->_loadCache());

		// Add the keys for each property
		$array = array();
		foreach ($browser as $key => $value) {
			$array[$this->_properties[$key]] = $value;
		}

		return $return_array ? $array : (object) $array;
	}

	/**
	 * Reconstructs the browser array from $key and $pattern
	 *
	 * @param string original user_agent
	 * @param string pattern key
	 * @param string pattern
	 * @return array browser array
	 */
	protected function constructBrowser($user_agent, $key, $pattern) {
		$browser = array(
			$user_agent, // Original useragent
			trim(strtolower($pattern), self::REGEX_DELIMITER),
			$this->_userAgents[$key]
		);

		$browser = $value = $browser + $this->_browsers[$key];
		while (array_key_exists(3, $value)) {
			$value      =   $this->_browsers[$value[3]];
			$browser    +=  $value;
		}

		if (!empty($browser[3])) {
			$browser[3] = $this->_userAgents[$browser[3]];
		}

		return $browser;
	}

	/**
	 * Loads the cache into object's properties
	 *
	 * @param int the level of cache to be loaded
	 *            default: the next level of cache
	 * @return bool true if the cache is loaded; false if not.
	 */
	private function _loadCache($level = null)
	{
		if ($level === null) {
			// default: next level
			$level = $this->_cacheLoaded + 1;
		} else {
			if ($this->_cacheLoaded >= $level) {
				// already loaded
				return true;
			}
		}

		if ($level >= count($this->cacheFilenames)) {
			//not available
			return false;
		}

		$cache_file = $this->cacheDir . $this->cacheFilenames[$level];
		require $cache_file;

		$this->_browsers   = $browsers;
		$this->_userAgents  = $userAgents;
		$this->_patterns  = $patterns;
		$this->_properties  = $properties;

		$this->_cacheLoaded = $level;
		return true;
	}


	/**
	 * The instance for online production usage.
	 * For each request, call:
	 *    Browscap::getInstance()->getBrowser()
	 * to get the Browscap information because each
	 * HTTP request will have unique browser information.
	 */
	private static $instance = null;
	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new Browscap($_SERVER['PHP_ROOT'].'/lib/');
		}
		return self::$instance;
	}

}

/**
 * Browscap.ini parsing class exception
 *
 * @package    Browscap
 * @author     Jonathan Stoppani <st.jonathan@gmail.com>
 * @copyright  Copyright (c) 2006-2008 Jonathan Stoppani
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://garetjax.info/projects/browscap/
 */
class Browscap_Exception extends Exception
{}
