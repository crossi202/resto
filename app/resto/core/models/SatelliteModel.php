<?php
/*
 * Copyright 2018 Jérôme Gasperi
 *
 * Licensed under the Apache License, version 2.0 (the "License");
 * You may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
/**
 * resto model for satellite imagery
 */
class SatelliteModel extends LandCoverModel
{

    /**
     * Constructor
     */
    public function __construct()
    {
        
        parent::__construct();
        
        /*
         * Satellite model follows STAC EO Extension Specification
         */
        $this->stacExtensions[] = 'eo';
        
        /*
         * Extend STAC mapping
         */
        $this->stacMapping = array_merge($this->stacMapping, array(
            'instrument' => 'eo:instrument',
            'platform' => 'eo:platform',
            'resolution' => 'eo:gsd',
            'viewAzimuth' => 'eo:azimuth',
            'viewZenith' => 'eo:off_nadir'
        ));

        /*
         * Extend search filters
         */
        $this->searchFilters = array_merge($this->searchFilters, array(
        
            'eo:productType' => array(
                'key' => 'normalized_hashtags',
                'osKey' => 'productType',
                'prefix' => 'productType',
                'operation' => 'keywords',
                'title' => 'A string identifying the entry type (e.g. ER02_SAR_IM__0P, MER_RR__1P, SM_SLC__1S, GES_DISC_AIRH3STD_V005)',
                'options' => 'auto'
            ),
            
            'eo:processingLevel' => array(
                'key' => 'normalized_hashtags',
                'osKey' => 'processingLevel',
                'prefix' => 'processingLevel',
                'operation' => 'keywords',
                'title' => 'A string identifying the processing level applied to the entry',
                'options' => 'auto'
            ),
            
            'eo:platform' => array(
                'key' => 'normalized_hashtags',
                'osKey' => 'platform',
                'prefix' => 'platform',
                'operation' => 'keywords',
                'title' => 'A string with the platform short name (e.g. Sentinel-1)',
                'options' => 'auto'
            ),
            
            'eo:instrument' => array(
                'key' => 'normalized_hashtags',
                'osKey' => 'instrument',
                'prefix' => 'instrument',
                'operation' => 'keywords',
                'title' => 'A string identifying the instrument (e.g. MERIS, AATSR, ASAR, HRVIR. SAR)',
                'options' => 'auto'
            ),
            
            'eo:sensorType' => array(
                'key' => 'normalized_hashtags',
                'osKey' => 'sensorType',
                'prefix' => 'sensorType',
                'operation' => 'keywords',
                'title' => 'A string identifying the sensor type. Suggested values are: OPTICAL, RADAR, ALTIMETRIC, ATMOSPHERIC, LIMB',
                'options' => 'auto'
            )
            
            /* 
             *  
             *
            'eo:resolution' => array(
                'key' => 'resolution',
                'osKey' => 'resolution',
                'operation' => 'interval',
                'title' => 'Spatial resolution expressed in meters',
                'pattern' => '^(\[|\]|[0-9])?[0-9]+$|^[0-9]+?(\[|\])$|^(\[|\])[0-9]+,[0-9]+(\[|\])$',
                'quantity' => array(
                    'value' => 'resolution',
                    'unit' => 'm'
                )
            ),
    
            /*
             *  
             *
            'eo:orbitNumber' => array(
                'key' => 'orbitNumber',
                'osKey' => 'orbitNumber',
                'operation' => 'interval',
                'minInclusive' => 1,
                'quantity' => array(
                    'value' => 'orbit'
                )
            ),*/
            
        ));

        /*
         * Extend facet categories
         */
        $this->facetCategories = array_merge($this->facetCategories, array(
            array(
                'productType'
            ),
            array(
                'processingLevel'
            ),
            array(
                'sensorType',
                'platform',
                'instrument'
            )
        ));
        
        /*
         * [IMPORTANT] The table resto.feature_satellite must exist
         * with columns 'id' and at least the columns list below
         */
        $this->tables[] = array(
            'name' => 'feature_satellite',
            'columns' => array(
                'resolution'
            )
        );
   
    }
}
