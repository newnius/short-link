<?php

class CRObject implements JsonSerializable
{
	private $map;

	/*
	 * @param $map: key-value array
	 */
	public function __construct(array $map = array())
	{
		$this->map = $map;
	}

	public function set($key, $value)
	{
		$this->map[$key] = $value;
		return true;
	}

	public function get($key, $default = null)
	{
		if (isset($this->map[$key]) && !is_null($this->map[$key])) {
			return $this->map[$key];
		}
		return $default;
	}

	public function getInt($key, $default = null)
	{
		if (isset($this->map[$key]) && !is_null($this->map[$key]) && is_numeric($this->map[$key])) {
			return intval($this->map[$key]);
		}
		return $default;
	}

	public function getBool($key, $default = false)
	{
		if (isset($this->map[$key]) && !is_null($this->map[$key])) {
			return $this->map[$key] === true;
		}
		return $default === true;
	}

	public function toArray()
	{
		return $this->map;
	}

	/*
	 * set $this[$key] if isset($obj[$key])&&!isset($this[$key])
	 * (new CRObject(['k1' => 'v1'])).union(new CRObject(['k1' => 'v1.1', 'k2' => 'v2']))
	 *   = new CRObject(['k1' => 'v1', 'k2' => 'v2'])
	 */
	public function union(CRObject $obj)
	{
		$keys = array_keys($obj->toArray());
		foreach ($keys as $key) {
			$this->set($key, $this->get($key, $obj->get($key)));
		}
		return $this;
	}

	public function jsonSerialize()
	{
		return $this->toArray();
    }

}
