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
 * resto model
 */
abstract class RestoModel
{

    /*
     * Model options
     */
    public $options = array();

    /*
     * STAC extensions - override in child models
     */
    public $stacExtensions = array();

    /*
     * STAC mapping
     */
    public $stacMapping = array();

    /*
     * Facet hierarchy
     */
    public $facetCategories = array(
        array(
            'collection'
        ),
        array(
            'continent',
            'country',
            'region',
            'state'
        ),
        array(
            'year',
            'month',
            'day'
        )
    );

    /**
     * OpenSearch search filters
     *
     *  'key' :
     *      resto.feature column name
     *  'osKey' :
     *      OpenSearch property name in template urls
     *  'prefix' :
     *      (for "keywords" operation only) Prefix systematically added to input value (i.e. prefix:value)
     *  'operation' :
     *      Type of operation applied to the filter ("in", "keywords", "intersects", "distance", "=", "<=", ">=")
     *
     *
     *  Below properties follow the "Parameter extension" (http://www.opensearch.org/Specifications/OpenSearch/Extensions/Parameter/1.0/Draft_2)
     *
     *  'minimum' :
     *      Minimum number of times this parameter must be included in the search request (default 0)
     *  'maximum' :
     *      Maximum number of times this parameter must be included in the search request (default 1)
     *  'pattern' :
     *      Regular expression against which the parameter's value
     *      Pattern follows Javascript (http://www.ecma-international.org/publications/standards/Ecma-262.htm)
     *  'title' :
     *      Tooltip
     *  'minExclusive'
     *      Minimum value for the element that cannot be reached
     *  'maxExclusive'
     *      Maximum value for the element that cannot be reached
     *  'hidden'
     *      Do not display this search parameter in OpenSearch Description Document
     *  'options'
     *      List of possible values. Two ways
     *      1. Array of predefined value/label
     *          array(
     *              array(
     *                  'value'
     *                  'label'
     *              ),
     *              ...
     *          )
     *      2. 'auto'
     *         In this case will be computed from facets table
     */
    public $searchFilters = array(

        'searchTerms' => array(
            'key' => 'normalized_hashtags',
            'type' => 'array',
            'osKey' => 'q',
            'operation' => 'keywords',
            'title' => 'Free text search'
        ),
        
        'count' => array(
            'osKey' => 'limit',
            'minInclusive' => 1,
            'maxInclusive' => 500,
            'title' => 'The maximum number of results returned per page (default 10)'
        ),
        
        'startIndex' => array(
            'osKey' => 'startIndex',
            'minInclusive' => 1
        ),
        
        'startPage' => array(
            'osKey' => 'page',
            'minInclusive' => 1
        ),
        
        'language' => array(
            'osKey' => 'lang',
            'title' => 'Two letters language code according to ISO 639-1',
            'pattern' => '^[a-z]{2}$'
        ),
        
        'geo:uid' => array(
            'key' => 'id',
            'osKey' => 'ids',
            'operation' => 'in',
            'title' => 'Array of item ids to return. All other filter parameters that further restrict the number of search results (except next and limit) are ignored',
            'pattern' => '^[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}$'
        ),
        
        'geo:geometry' => array(
            'key' => 'geom',
            'osKey' => 'intersects',
            'operation' => 'intersects',
            'title' => 'Region of Interest defined in GeoJSON or in Well Known Text standard (WKT) with coordinates in decimal degrees (EPSG:4326)'
        ),

        'geo:box' => array(
            'key' => 'geom',
            'osKey' => 'bbox',
            'operation' => 'intersects',
            'title' => 'Region of Interest defined by \'west, south, east, north\' coordinates of longitude, latitude, in decimal degrees (EPSG:4326)',
            'pattern' => '^[-]?[0-9]*\.?[0-9]+,[-]?[0-9]*\.?[0-9]+,[-]?[0-9]*\.?[0-9]+,[-]?[0-9]*\.?[0-9]+$'
        ),
        
        'geo:name' => array(
            'key' => 'geom',
            'osKey' => 'name',
            'operation' => 'distance',
            'title' => 'Location string e.g. Paris, France or toponym identifier (i.e. geouid:xxxx)'
        ),
        
        'geo:lon' => array(
            'key' => 'geom',
            'osKey' => 'lon',
            'operation' => 'distance',
            'title' => 'Longitude expressed in decimal degrees (EPSG:4326) - should be used with geo:lat',
            'minInclusive' => -180,
            'maxInclusive' => 180
        ),
        
        'geo:lat' => array(
            'key' => 'geom',
            'osKey' => 'lat',
            'operation' => 'distance',
            'title' => 'Latitude expressed in decimal degrees (EPSG:4326) - should be used with geo:lon',
            'minInclusive' => -90,
            'maxInclusive' => 90
        ),
        
        'geo:radius' => array(
            'key' => 'geom',
            'osKey' => 'radius',
            'operation' => 'distance',
            'title' => 'Expressed in meters - should be used with geo:lon and geo:lat',
            'minInclusive' => 1
        ),
        
        'time:start' => array(
            'key' => 'startDate',
            'osKey' => 'start',
            'operation' => '>=',
            'title' => 'Beginning of the time slice of the search query. Format should follow RFC-3339',
            'pattern' => '^[0-9]{4}-[0-9]{2}-[0-9]{2}(T[0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]+)?(|Z|[\+\-][0-9]{2}:[0-9]{2}))?$'
        ),
        
        'time:end' => array(
            'key' => 'startDate',
            'osKey' => 'end',
            'operation' => '<=',
            'title' => 'End of the time slice of the search query. Format should follow RFC-3339',
            'pattern' => '^[0-9]{4}-[0-9]{2}-[0-9]{2}(T[0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]+)?(|Z|[\+\-][0-9]{2}:[0-9]{2}))?$'
        ),
        
        'dc:date' => array(
            'key' => 'created',
            'osKey' => 'created',
            'title' => 'Metadata product creation within database',
            'operation' => '>=',
            'pattern' => '^[0-9]{4}-[0-9]{2}-[0-9]{2}(T[0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]+)?(|Z|[\+\-][0-9]{2}:[0-9]{2}))?$'
        ),

        // [STAC/WFS3] datetime is a mix of time:start/time:end
        'resto:datetime' => array(
            'key' => 'startDate',
            'osKey' => 'datetime',
            'title' => 'Single date+time, or a range ("/" separator) of the search query. Format should follow RFC-3339. Equivalent to OpenSearch {time:start}/{time:end}',
            'pattern' => '^[a-zA-Z0-9\-\/\.\:]+$'
        ),

        'resto:collection' => array(
            'key' => 'collection',
            'osKey' => 'collections',
            'title' => 'Comma separated list of collections name',
            'pattern' => '^[A-Za-z][a-zA-Z0-9,]+$',
            'operation' => 'in',
            'hidden' => true,
            'options' => 'auto'
        ),

        'resto:model' => array(
            'key' => 'model',
            'osKey' => 'model',
            'title' => 'Model name',
            'pattern' => '^[A-Za-z][a-zA-Z0-9]+$',
            'operation' => '='
        ),
        
        /*
         * Opposite to STAC "next" query parameter does not exist but could be named "prev"
         */
        'resto:gt' => array(
            'osKey' => 'prev',
            'title' => 'Cursor pagination - return result with sort key greater than sort value',
            'pattern' => "^[0-9]+$",
            'operation' => '>'
        ),
        
        /*
         * The default sort order is DESCENDING - so the STAC "next" query parameter is equivalent
         * to the "resto:lt" (lower than) filter 
         */
        'resto:lt' => array(
            'osKey' => 'next',
            'title' => 'Cursor pagination - return result with sort key lower than sort value',
            'pattern' => "^[0-9]+$",
            'operation' => '<'
        ),
        
        'resto:pid' => array(
            'key' => 'productIdentifier',
            'osKey' => 'pid',
            'operation' => '=',
            'title' => 'Equal on productIdentifier'
        ),
        
        'resto:sort' => array(
            'osKey' => 'sort',
            'pattern' => '^[a-zA-Z\-]*$',
            'title' => 'Sort results by property (startDate or created - Default is startDate). Sorting order is DESCENDING (ASCENDING if property is prefixed by minus sign)'
        ),
        
        'resto:owner' => array(
            'key' => 'owner',
            'osKey' => 'owner',
            'title' => 'Owner of features',
            'operation' => '='
        ),
        
        'resto:status' => array(
            'key' => 'status',
            'osKey' => 'status',
            'title' => 'Feature status',
            'operation' => '=',
            'pattern' => '^[0-9]+$'
        )

    );

    /*
     * Array of table names to store "model specific" properties for feature
     * Usually only numeric properties are stored (for search) since
     * string property are stored within metadata property of resto.feature table
     * and indexed with normalized_hashtags property of the same table
     */
    public $tables = array();
    
    /**
     * Constructor
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = $options;

        if ( isset($this->options['addons']['Social']) ) {
            $this->searchFilters = array_merge($this->searchFilters, array(
                'resto:likes' => array(
                    'key' => 'likes',
                    'osKey' => 'likes',
                    'operation' => 'interval',
                    'title' => 'Number of likes for feature',
                    'pattern' => '^(\[|\]|[0-9])?[0-9]+$|^[0-9]+?(\[|\])$|^(\[|\])[0-9]+,[0-9]+(\[|\])$'
                ),
                
                'resto:liked' => array(
                    'osKey' => 'liked',
                    'title' => 'Return only liked feature from calling user'
                )
            ));       
        }

    }

    /**
     * Return the model name (i.e. the name of the Model class)
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * Return model inheritance hierarchy stripping out RestoModel
     */
    public function getLineage()
    {
        return array_slice(
            array_merge(
                array_values(array_reverse(class_parents($this))),
                array($this->getName())
            ), 1
        );
    }

    /**
     * Store feature within {collection}.features table following the class model
     *
     * @param RestoCollection $collection
     * @param array $data : array (MUST BE GeoJSON in abstract Model)
     * @param array $params
     *
     */
    public function storeFeature($collection, $data, $params)
    {
        
        /*
         * Input feature cannot have both an id and a productIdentifier
         */
        if (isset($data['id']) && isset($data['properties']['productIdentifier']) && $data['id'] !== $data['properties']['productIdentifier']) {
            return RestoLogUtil::httpError(400, 'Invalid input feature - found both "id" and "properties.productIdentifier"');
        }

        $productIdentifier = $data['id'] ?? $data['properties']['productIdentifier'] ?? null;
        $data['properties']['productIdentifier'] = $productIdentifier;
        $featureId = isset($productIdentifier) ? RestoUtil::toUUID($productIdentifier) : RestoUtil::toUUID(md5(microtime().rand()));

        /*
         * First check if feature is already in database
         * [Note] Feature productIdentifier is UNIQUE
         *  
         * (do this before getKeywords to avoid iTag process)
         */
        if (isset($productIdentifier) && (new FeaturesFunctions($collection->context->dbDriver))->featureExists($featureId)) {
            RestoLogUtil::httpError(409, 'Feature ' . $featureId . ' (with productIdentifier=' . $productIdentifier . ') already in database');
        }

        return (new FeaturesFunctions($collection->context->dbDriver))->storeFeature(
            $featureId,
            $collection,
            $this->prepareFeatureArray($collection, $data, $params)
        );

    }

    /**
     * Update feature within {collection}.features table following the class model
     *
     * @param RestoFeature $feature
     * @param RestoCollection $collection
     * @param array $data : array (MUST BE GeoJSON in abstract Model)
     *
     */
    public function updateFeature($feature, $collection, $data)
    {
        return (new FeaturesFunctions($collection->context->dbDriver))->updateFeature(
            $feature,
            $collection,
            $this->prepareFeatureArray($collection, $data)
        );
    }

    /**
     * Set properties['resource'] object if applicable
     *
     *    {
     *       href: 'http://localhost/items/id/download',     // url to the resource (i.e. download link)
     *       type: 'application/zip',                           // mimeType of the resource
     *       size: 12345678,                                    // size in bytes of the resource
     *       checksum: 'MD5:1223ab45ef',                        // checksum of the resource (prefixed by the checksum type)
     *       path: '/data/images/xxxxx.zip',                    // local path to the resource
     *       metadata:{
     *            href: 'http://my.image.com/xxxxxx.xml,        // url to the original metadata for the resource
     *            type: 'application/xml'                       // mimeType of the metadata file
     *       },
     *       browse:{
     *            href: 'http://localhost/items/id/browse',  // url to browse (wms) service
     *            realhref: 'http://my.wms.server/wms',         // real wms href
     *            title: 'My wms',                              // Title
     *            type: 'WMS',                                  // Should be WMS
     *            layers: ''                                    // List of layers comma separated
     *       }
     *    }
     *
     * @param array $properties : feature properties
     * @param string $href : resto download url i.e. http://locahost/items/id/download
     *
     */
    public function generateLinksArray($properties, $href)
    {
        if (isset($properties['links'])) {
            return $properties['links'];
        }
        return null;
    }

    /**
     * The return value from this function will replace
     * feature properties['quicklook'] string
     *
     * @param array $properties : feature properties
     */
    public function generateQuicklookUrl($properties)
    {
        if (isset($properties['quicklook'])) {
            return $properties['quicklook'];
        }
        return null;
    }

    /**
     * The return value from this function will replace
     * feature properties['thumbnail'] string
     *
     * @param array $properties : feature properties
     */
    public function generateThumbnailUrl($properties)
    {
        if (isset($properties['thumbnail'])) {
            return $properties['thumbnail'];
        }
        return null;
    }

    /**
     * Get auto facet fields from model
     */
    public function getAutoFacetFields()
    {
        $facetFields = array();
        foreach (array_values($this->searchFilters) as $filter) {
            if (isset($filter['options']) && $filter['options'] === 'auto') {
                // [IMPORTANT] prefix has preseance over key
                $facetFields[] = $filter['prefix'] ?? $filter['key'];
            }
        }
        return $facetFields;
    }

    /**
     * Get resto filters from input query parameters
     *  - change parameter keys to model parameter key
     *  - remove unset parameters
     *  - remove all HTML tags from input to avoid XSS injection
     *  - check that filter value is valid regarding the model definition
     *
     * @param array $query
     */
    public function getFiltersFromQuery($query)
    {
        $params = array();
        foreach ($query as $key => $value) {
            $filterKey = $this->getFilterName($key);
            if (isset($filterKey)) {
                // Special case geo:geometry also accept GeoJSON => convert it to WKT
                $params[$filterKey] = preg_replace('/<.*?>/', '', $filterKey === 'geo:geometry' ? $this->forceWKT($value) : $value);
                $this->validateFilter($filterKey, $params[$filterKey]);
            }
        }

        // [STAC] If "ids" filter is set, then discard every other filters except next and limit
        return isset($params['geo:uid']) ? array(
            'geo:uid' => $params['geo:uid'],
            'resto:lt' => $params['resto:lt'] ?? null,
            'limit' => $params['limit'] ?? null
        ) : $params;
    }

    /**
     * Return OpenSearch filter name from OpenSearch key
     * @param string $osKey
     */
    public function getFilterName($osKey)
    {
        foreach (array_keys($this->searchFilters) as $filterKey) {
            if ($osKey === $this->searchFilters[$filterKey]['osKey']) {
                return $filterKey;
            }
        }
        return null;
    }

    /**
     * Check if value is valid for a given filter regarding the model
     *
     * @param string $filterKey
     * @param string $value
     */
    public function validateFilter($filterKey, $value)
    {

        /*
         * Check pattern for string
         */
        if (isset($this->searchFilters[$filterKey]['pattern'])) {

            /*
             * If operation = "in" then value is a comma separated list - check pattern for each element of the list
             */
            if ($this->searchFilters[$filterKey]['operation'] && $this->searchFilters[$filterKey]['operation'] === 'in') {
                $elements = array_map('trim', explode(',', $value));
                for ($i = count($elements); $i--;) {
                    if (preg_match('\'' . $this->searchFilters[$filterKey]['pattern'] . '\'', $elements[$i]) !== 1) {
                        return RestoLogUtil::httpError(400, 'Comma separated list of "' . $this->searchFilters[$filterKey]['osKey'] . '" must follow the pattern ' . $this->searchFilters[$filterKey]['pattern']);
                    }
                }
            }
            else if (preg_match('\'' . $this->searchFilters[$filterKey]['pattern'] . '\'', $value) !== 1) {
                RestoLogUtil::httpError(400, 'Value for "' . $this->searchFilters[$filterKey]['osKey'] . '" must follow the pattern ' . $this->searchFilters[$filterKey]['pattern']);
            }
        }
        /*
         * Check pattern for number
         */
        elseif (isset($this->searchFilters[$filterKey]['minInclusive']) || isset($this->searchFilters[$filterKey]['maxInclusive'])) {
            if (!is_numeric($value)) {
                RestoLogUtil::httpError(400, 'Value for "' . $this->searchFilters[$filterKey]['osKey'] . '" must be numeric');
            }
            if (isset($this->searchFilters[$filterKey]['minInclusive']) && $value < $this->searchFilters[$filterKey]['minInclusive']) {
                RestoLogUtil::httpError(400, 'Value for "' . $this->searchFilters[$filterKey]['osKey'] . '" must be greater than ' . ($this->searchFilters[$filterKey]['minInclusive'] - 1));
            }
            if (isset($this->searchFilters[$filterKey]['maxInclusive']) && $value > $this->searchFilters[$filterKey]['maxInclusive']) {
                RestoLogUtil::httpError(400, 'Value for "' . $this->searchFilters[$filterKey]['osKey'] . '" must be lower than ' . ($this->searchFilters[$filterKey]['maxInclusive'] + 1));
            }
        }

        return true;
        
    }

    /**
     * Prepare featureArray for store/update
     *
     * @param RestoCollection $collection
     * @param array $data : array (MUST BE GeoJSON in abstract Model)
     * @param array $params : optional options for ingestion
     *
     */
    private function prepareFeatureArray($collection, $data, $params = array())
    {

        /*
         * Assume input file or stream is a JSON Feature
         */
        $checkGeoJSON = RestoGeometryUtil::checkGeoJSONFeature($data);
        if (! $checkGeoJSON['isValid']) {
            RestoLogUtil::httpError(400, $checkGeoJSON['error']);
        }

        /*
         * If model->inputMapping is set then remap input GeoJSON Feature file properties
         * to match resto model
         */
        $properties = $this->mapInputProperties($data);
        
        /*
         * Add collection to $properties to initialize facet counts on collection
         * [WARNING] if properties['collection'] is already set, it is discarded and replaced by the current collection
         */
        $properties['collection']  = $collection->name;

        /*
         * Check geometry topology integrity
         */
        $topologyAnalysis = (new GeneralFunctions($collection->context->dbDriver))->getTopologyAnalysis($data['geometry'], $params);
        if (!$topologyAnalysis['isValid']) {
            RestoLogUtil::httpError(400, $topologyAnalysis['error']);
        }
        
        /*
         * Tag add-on
         *
         * iTag is triggered by default unless :
         *
         *   - the collection is one of Tag add-on "excludedCollections" array option
         *   - query parameter "_useItag" is set to false
         *
         * [WARNING] if collection is excluded BUT _useItag is set to true then iTag is used
         */
        $keywords = array();
        if (isset($collection->context->addons['Tag'])) {
            $useItag = true;
            $tagger = new Tag($collection->context, $collection->user);
            if (isset($collection->context->addons['Tag']['options']['iTag']['excludedCollections'])) {
                for ($i = count($collection->context->addons['Tag']['options']['iTag']['excludedCollections']); $i--;) {
                    if ($collection->name === $collection->context->addons['Tag']['options']['iTag']['excludedCollections'][$i]) {
                        $useItag = false;
                        break;
                    }
                }
            }

            /*
             * Get
             */
            $keywords = $tagger->getKeywords($properties, $data['geometry'], $collection->model->facetCategories, array(
                'useItag' => isset($collection->context->query['_useItag']) ? filter_var($collection->context->query['_useItag'], FILTER_VALIDATE_BOOLEAN) : $useItag,
                'computeLandCover' => in_array('LandCoverModel', $collection->model->getLineage())
            ));
            
        }

        /*
         * Return prepared data
         */
        return array(
            'topologyAnalysis' => $topologyAnalysis,
            'properties' => array_merge($properties, array('keywords' => $keywords)),
            'assets' => $data['assets'] ?? null,
            'links' => $data['links'] ?? null
        );
    }

    /**
     * Remap properties array accordingly to $inputMapping array
     *
     *  $inputMapping array structure:
     *
     *          array(
     *              'propertyNameInInputFile' => 'restoPropertyName' or array('restoPropertyName1', 'restoPropertyName2)
     *          )
     *
     * @param Array $geojson
     */
    private function mapInputProperties($geojson)
    {

        // Output properties
        $properties = array();

        if (!isset($geojson['properties'])) {
            $geojson['properties'] = array();
        }

        if (property_exists($this, 'inputMapping')) {
            foreach ($this->inputMapping as $key => $arr) {

                /*
                 * key can be a path i.e. key1.key2.key3
                 */
                $childs = explode(Resto::MAPPING_PATH_SEPARATOR, $key);
                if (isset($geojson[$childs[0]])) {
                    // [IMPORTANT] Pass reference not copy
                    $property = &$geojson[$childs[0]];
                } else {
                    $property =  null;
                }
                $propertyKey = null;
                $isValid = true;

                if (isset($property)) {
                    for ($i = 1, $ii = count($childs); $i < $ii; $i++) {
                        if (! isset($property[$childs[$i]])) {
                            $isValid = false;
                            break;
                        }
                        $propertyKey = $childs[$i];
                        if ($i < $ii - 1) {
                            // [IMPORTANT] Pass reference not copy
                            $property = &$property[$childs[$i]];
                        }
                    }
                    
                    if ($isValid) {
                        if (!is_array($arr)) {
                            $arr = array($arr);
                        }
                        for ($i = count($arr); $i--;) {
                            $geojson['properties'][$arr[$i]] = $property[$propertyKey];
                        }
                        unset($property[$propertyKey]);
                    }
                }
            }
        }

        // Eventually unset all empty properties and array
        foreach (array_keys($geojson['properties']) as $key) {
            if (!isset($geojson['properties'][$key]) || (is_array($geojson['properties'][$key]) && count($geojson['properties'][$key]) === 0)) {
                continue;
            }
            $properties[$key] = $geojson['properties'][$key];
        }
        
        return $properties;
    }

    /**
     * Convert input GeoJSON string geometry into WKT or leave it untouched if already a WKT
     * 
     * @param string $geostring
     */
    private function forceWKT($geostring)
    {

        if (isset($geostring) && isset($geostring[0]) && $geostring[0] === '{') {
            $geostring = RestoGeometryUtil::geoJSONGeometryToWKT(json_decode($geostring, true));
        }
        
        if ( !isset($geostring) || !RestoGeometryUtil::isValidWKT($geostring) ) {
            return RestoLogUtil::httpError(400, 'Invalid input geometry for intersects - should be a valid GeoJSON or Well Known Text standard (WKT)');
        }
        
        return $geostring;

    }

}
