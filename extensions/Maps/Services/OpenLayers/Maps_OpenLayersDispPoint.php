<?php

/**
 * File holding the MapsOpenLayersDispPoint class.
 *
 * @file Maps_OpenLayersDispPoint.php
 * @ingroup MapsOpenLayers
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Class for handling the display_point(s) parser functions with OpenLayers.
 *
 * @author Jeroen De Dauw
 */
class MapsOpenLayersDispPoint extends MapsBasePointMap {
	
	protected $markerStringFormat = 'getOLMarkerData(lon, lat, "title", "label", "icon")';

	protected function getDefaultZoom() {
		global $egMapsOpenLayersZoom;
		return $egMapsOpenLayersZoom;
	}
	
	/**
	 * @see MapsBaseMap::addSpecificMapHTML()
	 */
	public function addSpecificMapHTML() {
		global $egMapsOpenLayersPrefix, $egOpenLayersOnThisPage, $wgLang;
		
		$layerItems = $this->mService->createLayersStringAndLoadDependencies( $this->layers );
		
		$egOpenLayersOnThisPage++;
		$mapName = $egMapsOpenLayersPrefix . '_' . $egOpenLayersOnThisPage;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc; overflow: hidden;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$langCode = $wgLang->getCode();
		
		$this->parser->getOutput()->addHeadItem(
			Html::inlineScript( <<<EOT
addOnloadHook(
	function() {
		initOpenLayer(
			'$mapName',
			$this->centreLon,
			$this->centreLat,
			$this->zoom,
			[$layerItems],
			[$this->controls],
			[$this->markerString],
			'$langCode'
		);
	}
);
EOT
		) );
	}

}