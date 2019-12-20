<?php
/*
   Used to generate the list of valid municipalities for LitteraturnettAuthorFields
 */

class LitteraturnettRegions {
	protected static $instance = null;
	// Make field definitions accessible by name instead of field ID
	protected static $indexedfields = array();

	// This class should only get instantiated with this method. IOK 2019-10-14
	public static function instance()  {
		if (!static::$instance) static::$instance = new LitteraturnettAuthorFields();
		return static::$instance;
	}

	public static function allRegions () {
		// All regions of norway
		$norway = json_decode(file_get_contents(dirname(__FILE__) . "/regions/norway.json"),true);
		// Old Nordland, Troms, Finnmark
		$nordlandtromsfinnmark2019 = json_decode(file_get_contents(dirname(__FILE__) . "/regions/nordlandtromsfinnmark-2019.json"),true);
		$sognogfjordane = json_decode(file_get_contents(dirname(__FILE__) . "/regions/sognogfjordane.json"),true);

		$osloandviken  = array_merge($norway['Oslo'],$norway['Viken']);
		$nordlandtromsfinnmark = array();
		$nordlandtromsfinnmark['Nordland'] = $norway['Nordland'];
		$nordlandtromsfinnmark['Troms og Finnmark'] = $norway['Troms og Finnmark'];

		// All Fylker are regions
		$fylker =array_keys($norway);
		asort($fylker);

		$regions = array();
		$regions['Norge'] = $norway;
		$regions['Oslo og Viken'] = $osloandviken;
		$regions['Nordland, Troms og Finnmark'] = $nordlandtromsfinnmark;
		$regions['Nordland, Troms og Finnmark 2019'] = $nordlandtromsfinnmark2019;
		$regions['Sogn og Fjordane'] = $sognogfjordane;

		foreach($fylker as $fylke) {
			$regions[$fylke] = $norway[$fylke];
		}

		return $regions;
	}

        public static function get( $region) {
           return allRegions()[$region];
        }


}

