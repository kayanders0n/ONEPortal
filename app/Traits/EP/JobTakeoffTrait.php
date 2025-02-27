<?php

namespace Traits\EP;

use Models\EP\JobTakeoffData;
use Helpers\Numeric;

trait JobTakeoffTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobTakeoffData(array $filters = [], string $limit = ''): array
    {

        $company_id = (int)$filters['company_id'];

        $results = (new JobTakeoffData())->selectJobTakeoffData($filters, $limit);

        $data = ['results' => array(), 'num_results' => $results[1]];

        foreach ($results[0] as $i => $result) {
            $this->buildJobTakeoffItem($result, $data);
        }

        if ($company_id == CONCRETE_ENTITYID) { $this->concreteTotals($data); }

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
     * @param $result
     * @param $data
     * @return bool
     */
    private function buildJobTakeoffItem($result, array &$data): bool
    {
        $phase_name       = $this->getPhaseName((string)$result->PHASE_NAME);
        $location_name    = (string)strtoupper(trim($result->TAKEOFF_LOCATION));
        $material_uom     = (string)strtoupper(trim($result->UOM_NAME));
        $material_idcode  = (string)strtoupper(trim($result->MATERIAL_IDCODE));

        $location_name = substr($location_name, 4); //strip off the code prefix of the location

        if (!in_array($material_idcode, array('NONSTOCK'))) {
            if (in_array($material_uom, array('LS', 'USD'))) {
                return false;
            }
        }

        $item = [
            'id'            => (int)$result->MATERIAL_SEQNO,
            'code'          => htmlentities($material_idcode),
            'name'          => htmlentities(cleanString($result->MATERIAL_NAME)),
            'uom'           => htmlentities(cleanString($result->UOM_NAME)),
            'units'         => Numeric::formatFloat((float)$result->TAKEOFF_UNITS, 2, false),
            'additional'    => htmlentities(cleanString($result->TAKEOFF_ADDDESCRIPT)),
            'doc_count'     => (int)$result->DOC_COUNT,
            'whs_status'    => htmlentities($result->WAREHOUSE_STATUS),
        ];

        $found_phase = false;
        foreach ($data['results'] as $pkey=>$pitem) {
            if ($data['results'][$pkey]['phase']['name'] == $phase_name) {
                $found_phase = true; $found_location = -1;
                foreach ($data['results'][$pkey]['phase']['locations'] as $lkey=>$litem) {
                    if ($litem['location']['name'] == $location_name) {
                        $found_location = $lkey;
                        break;
                    }
                }
                if ($found_location == -1) {
                    $data['results'][$pkey]['phase']['locations'][] = ['location' => ['name' => $location_name, 'name_java' => javaSafe($location_name), 'items' => [['item' => $item]]]];
                } else {
                    $found_item = -1;
                    foreach ($data['results'][$pkey]['phase']['locations'][$found_location]['location']['items'] as $ikey=>$iitem) {
                        if (($iitem['item']['id'] == $item['id']) && ($iitem['item']['additional'] == $item['additional'])) {
                            $found_item = $ikey;
                            break;
                        }
                    }

                    if ($found_item == -1) {
                        $data['results'][$pkey]['phase']['locations'][$found_location]['location']['items'][] = ['item' => $item];
                    } else {
                        $units = $data['results'][$pkey]['phase']['locations'][$found_location]['location']['items'][$found_item]['item']['units'];
                        $units = Numeric::formatFloat($units['amount'] + $item['units']['amount'], 2, false);
                        $data['results'][$pkey]['phase']['locations'][$found_location]['location']['items'][$found_item]['item']['units'] = $units;
                    }

                }
            }
        }

        if (!$found_phase) {
            $data['results'][]['phase'] = ['name' => $phase_name, 'name_java' => javaSafe($phase_name), 'locations' => [['location' => ['name' => $location_name, 'name_java' => javaSafe($location_name), 'items' => [['item' => $item]]]]]];
        }

        return true;
    }


    /**
     * @param string $phase_name
     * @return string
     */
    function getPhaseName(string $phase_name): string {
        $return = strtoupper(trim($phase_name));
        // look for Phase: and strip off everything up to that point
        $search = 'PHASE:';
        $i = strpos($return, $search);
        if ($i > -1) {
            $return = substr($return, $i + strlen($search));
            $return = trim(str_replace(range(0,9), '',$return));
            $return = str_replace('- ', ' ', $return);
            $return = str_replace('.', '', $return);
        }
        return trim($return);
    }

    /**
     * @param string $takeoff_code
     * @param float $takeoff_units
     * @return float
     */
    function concreteYards(string $takeoff_code, float $takeoff_units): float {
        $result = 0;
        if (in_array($takeoff_code, array('C100'))) {
            $result = $takeoff_units;
        }
        return (float)$result;
    }

    function concreteTotals(array &$data): bool {
        foreach ($data['results'] as $key=>$item) {
            foreach ($item['phase']['locations'] as $lkey=>$litem) {
                $data['results'][$key]['phase']['locations'][$lkey]['location']['concrete_yards'] = Numeric::formatFloat(0, 2, false);
                foreach($litem['location']['items'] as $ikey=>$iitem) {
                    $units = $this->concreteYards($iitem['item']['code'], $iitem['item']['units']['amount']);
                    if ($units != 0) {
                        $concrete_yardage = $data['results'][$key]['phase']['locations'][$lkey]['location']['concrete_yards'];
                        $concrete_yardage = Numeric::formatFloat($concrete_yardage['amount'] + $units, 2, false);
                        $data['results'][$key]['phase']['locations'][$lkey]['location']['concrete_yards'] = $concrete_yardage;
                    }
                };

            }
        }
        return true;
    }
}