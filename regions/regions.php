<?php

// All regions of norway
$norway = json_decode(file_get_contents(dirname(__FILE__) . "/norway.json"),true);
// Old Nordland, Troms, Finnmark
$nordlandtromsfinnmark2019 = json_decode(file_get_contents(dirname(__FILE__) . "/nordlandtromsfinnmark-2019.json"),true);
$sognogfjordane = json_decode(file_get_contents(dirname(__FILE__) . "/sognogfjordane.json"),true);

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

print_r($regions);
