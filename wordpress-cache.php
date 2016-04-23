<?php

class WordPress_Cache implements \Arctic\Cache\Cache
{
	protected $_group;

	/**
	 * Initiate the cache.
	 * @param array|null $config
	 * @return bool
	 */
	public function initiate(array $config=null) {
		// default configuration
		$default_config = array(
			'group' => 'arctic_api'
		);

		// merge default configuration
		if ($config) $config = array_merge($default_config, $config);
		else $config = $default_config;

		// get group
		$this->_group = $config['group'];

		return true;
	}

	/**
	 * Create new entry in the cache only if the key does not exist in the cache.
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl 0 for no timeout, otherwise time in seconds.
	 * @return bool
	 */
	public function insert($key, $value, $ttl=0) {
		return wp_cache_add($key, $value, $this->_group, $ttl);
	}

	/**
	 * Update an existing entry in the cache, but only if it is exists.
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl 0 for no timeout, otherwise time in seconds.
	 * @return bool
	 */
	public function update($key, $value, $ttl=0) {
		return wp_cache_replace($key, $value, $this->_group, $ttl);
	}

	/**
	 * Insert or update an entry in the cache.
	 * @param string $key
	 * @param mixed $value
	 * @param int $ttl 0 for no timeout, otherwise time in seconds.
	 */
	public function set($key, $value, $ttl=0) {
		wp_cache_set($key, $value, $this->_group, $ttl);
	}

	/**
	 * Fetch a value from the cache.
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$found = null;
		$ret = wp_cache_get($key, $this->_group, false, $found);
		if (false === $found) return null;
		return $ret;
	}

	/**
	 * Remove a key from the cache.
	 * @param string $key
	 */
	public function remove($key) {
		wp_cache_delete($key, $this->_group);
	}

	/**
	 * Whether or not this cache is a viable default type.
	 * @param array|null $config
	 * @return bool
	 */
	public static function isViableDefaultCacheType(array $config = null) {
		// not defined
		if (!defined('WP_CONTENT_DIR')) return false;

		// has file?
		return file_exists(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'object-cache.php');
	}
}
