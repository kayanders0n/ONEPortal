<?php

namespace Traits\EP;

use Models\EP\FleetData;
use Helpers\Date;

trait FleetTrait
{

    private $company_id = 0;

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getFleetData(array $filters = [], string $limit = ''): array
    {
        $this->company_id = (int)($filters['company_id'] ?? null);

        $results = (new FleetData())->selectFleetData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildFleetItem($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            if (empty($results[0])) {
                return array(); // no data
            } else {
                return $data['results'][0];
            }
        }

        return $data;
    }

    /**
     * @param array $filters
     *
     * @return array|null
     */
    private function getFleetItem(array $filters): ?array
    {
        return $this->getFleetData($filters, 'ROWS 1');
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildFleetItem($result): array
    {
        // ----- VEHICLE PARKED -----

        $parked_value = (int)$result->ASSET_PARKED;
        $parked = '';

        if ($parked_value == 1) {$parked = 'PARKED';}


        // ----- NAME ------
        // should also include ADOT, being serviced, emissions...

        $engine = htmlentities(trim($result->ENGINE_SIZE));
        $engine_size = '';
        if ($engine != '') {$engine_size = ' (' . $engine . ')';}


        // ----- CONFIGURATION -----

        $weight = (int)$result->ASSET_VEHICLEWEIGHT; //listed as "double precision"?
        $vehicleweight = '';
        if ($weight > 100) {$vehicleweight = ' <span style="float: right; color: green; font-size: 0.9em;">' . $weight . ' Lbs</span> ';}

        
        // ----- LICENSE/EXPIRES-----
        // sould include vin title on hover
        // $licensestyle should set background to red and font to white if < 10 days, or background to orange/brown if < 30 days


        // ----- LAST ODM. -----


        // ----- ODM. DATE -----


        // ----- LAST OIL CHANGE -----
        // on hover should say "Change every ___ miles." and "Last changed on (date)" if applicable
        // need calculation for (##)
        // background red if
        // background grey if no data (and says no data)


        // ----- DRIVER -----
        // red font if "No Driver"
        // figure out what the (#####) is


        // ----- LOCATION -----
        // background green if...??


        // ----- COMPANY -----
        // make it say PLU/FRA/CON/D&T



        // ----- GPS -----
        // make it say YES/NO


        // ----- ITEMS -----

        $item = [
            'material' => [
                'item_id' => (int)$result->MATERIAL_SEQNO,
                'idcode' => htmlentities(trim($result->MATERIAL_IDCODE)),
                'name' => htmlentities(trim($result->MATERIAL_NAME)),
                'oilchangemiles' => (int)$result->MATERIAL_OILCHANGEMILES,
            ],

            'asset' => [
                'id' => (int)$result->ASSET_SEQNO,
                'idcode' => htmlentities(trim($result->ASSET_IDCODE)),
                'vin' => htmlentities(trim($result->ASSET_VIN)),
                'license' => htmlentities(trim($result->ASSET_LICENSE)),
                'configuration' => htmlentities(trim($result->ASSET_CONFIGURATION)),
                'vehicleweight' => $vehicleweight,
                'servicedate' => Date::formatDate($result->ASSET_SERVICEDATE, 'm/d/Y h:i:s A'),
                'regexpiration' => Date::formatDate($result->ASSET_REGEXPIRATION, 'm/d/Y h:i:s A'),
                'isemissionsneeded' => (int)$result->ASSET_ISEMISSIONSNEEDED,
                'isadot' => (int)$result->ASSET_ISADOT,
                'status' => htmlentities(trim($result->ASSET_STATUS)),
                'parked' => $parked,
                'last_odometer' => (int)$result->LAST_ODOMETER,
                'last_fuel_date' => Date::formatDate($result->LAST_FUEL_DATE, 'm/d/Y h:i:s A'),
                'last_oilchange' => (int)$result->LAST_OILCHANGE,
                'last_oilchange_date' => Date::formatDate($result->LAST_OILCHANGE_DATE, 'm/d/Y h:i:s A'),
                'has_gps' => (int)$result->HAS_GPS,
                'engine_size' => $engine_size,
            ],

            'driver' => [
                'id' => (int)$result->DRIVER_SEQNO,
                'num' => (int)$result->DRIVER_TABLEID,
                'name' => htmlentities(trim($result->DRIVER_NAME)),
            ],

            'location' => [
                'id' => (int)$result->ASSET_LOCATION_SEQNO,
                'name' => htmlentities(trim($result->ASSET_LOCATION)),
            ],

            'company' => [
                'id' => (int)$result->ASSET_ENTITYID,
                'num' => (int)$result->ASSET_COMPANY_TABLEID,
                'name' => htmlentities(trim($result->ASSET_COMPANY)),
            ]
        ];

        return $item;
    }
}