<?php

namespace Traits\EP;

use Models\EP\JobLaborData;
use Helpers\Numeric;

trait JobLaborTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobLaborData(array $filters = [], string $limit = ''): array
    {

        $results = (new JobLaborData())->selectJobLaborData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobLaborItem($result);
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

        $company_id = (int) $filters['company_id'] ?: 0;

        if ($company_id == PLUMBING_ENTITYID) {
            // only plumbing does flex at this time
            $this->fillJobLaborFlexData($filters, $limit, $data);
            $this->fillJobLaborNonFlexData($filters, $limit, $data);
        }

        $this->fillJobLaborActualData($filters, $limit, $data);

        return $data;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildJobLaborItem($result): array
    {

        $uom = trim($result->BUDGET_UOM);

        $item = [
            // data
            'labor' => [
                'index' => htmlentities(trim($result->PHASE_INDEX)),
                'name'  => htmlentities(strtoupper(trim($result->PHASE_NAME))),
                'uom'   => htmlentities($uom),
                'budget' => Numeric::formatFloat(floatval($result->SUM_TOTAL), 2, ($uom == 'USD' ? true : false)),
                'actual' => Numeric::formatFloat(0, 2, true),
                'delta'  => Numeric::formatFloat(0, 2, true),
                'flex'   => Numeric::formatFloat(0, 2, true),
            ],
        ];

        return $item;
    }

    /**
     * @param array $filters
     * @param string $limit
     * @param array $data
     * @return bool
     */
    private function fillJobLaborFlexData(array $filters = [], string $limit = '', array &$data): bool
    {
        $filters['flex_only'] = 1; // filter for flex

        $results = (new JobLaborData())->selectJobLaborData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $name = trim($result->PHASE_NAME);
            $flex = (float)$result->SUM_TOTAL;

            foreach ($data['results'] as $key=>$item) {

                if ($item['labor']['name'] == $name) {

                    // have to use the actual object to modify the data that will be passed by reference
                    $flex += (float)$data['results'][$key]['labor']['flex']['amount'];
                    $data['results'][$key]['labor']['flex'] = Numeric::formatFloat($flex, 2, true);
                    break; // update one and only match

                }
            }
        }

        return true;

    }


    /**
     * @param array $filters
     * @param string $limit
     * @param array $data
     * @return bool
     */
    private function fillJobLaborNonFlexData(array $filters = [], string $limit = '', array &$data): bool
    {
        $filters['non_flex'] = 1; // filter for not flex items

        $results = (new JobLaborData())->selectJobLaborOtherData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $name = trim($result->PHASE_NAME);

            foreach ($data['results'] as $key=>$item) {

                if ($item['labor']['name'] == $name) {

                    // have to use the actual object to modify the data that will be passed by reference
                    $data['results'][$key]['labor']['other'][] = [
                        'code'    => htmlentities(trim($result->MATERIAL_IDCODE)),
                        'name'    => htmlentities(strtoupper(cleanString($result->MATERIAL_NAME))),
                        'budget'  => Numeric::formatFloat((float)$result->SUM_TOTAL, 2, true),
                    ];
                    break; // update one and only match

                }
            }
        }

        return true;

    }

    /**
     * @param array $filters
     * @param string $limit
     * @param array $data
     * @return bool
     */
    private function fillJobLaborActualData(array $filters = [], string $limit = '', array &$data): bool
    {

        $results = (new JobLaborData())->selectJobLaborActualData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $name = trim($result->PHASE_NAME);
            $actual = round(floatval($result->ACTUAL_AMT), 2);

            $found = false;

            foreach ($data['results'] as $key=>$item) {

                if ($item['labor']['name'] == $name) {

                    $found = true;

                    // have to use the actual object to modify the data that will be passed by reference
                    $actual += $data['results'][$key]['labor']['actual']['amount'];
                    $data['results'][$key]['labor']['actual'] = Numeric::formatFloat($actual, 2, true);
                    break; // update one and only match

                }
            }

            if (!$found) {
                $data['results'][]['labor'] =
                    [
                        'index' => '999.000',
                        'name'  => htmlentities($name),
                        'uom'   => 'USD',
                        'budget' => Numeric::formatFloat(0, 2, true),
                        'actual' => Numeric::formatFloat($actual, 2, true),
                        'delta'  => Numeric::formatFloat(-$actual, 2, true),
                        'flex'   => Numeric::formatFloat(0, 2, true),
                    ];
            }
        }

        // get deltas and totals

        $total_budget = (float)0; $total_actual = (float)0; $total_delta = (float)0;

        foreach ($data['results'] as $key=>$item) {

            $delta = $data['results'][$key]['labor']['budget']['amount'] -
                     $data['results'][$key]['labor']['actual']['amount'];

            $data['results'][$key]['labor']['delta'] = Numeric::formatFloat($delta, 2, true);

            $total_budget += $data['results'][$key]['labor']['budget']['amount'];
            $total_actual += $data['results'][$key]['labor']['actual']['amount'];
            $total_delta  += $data['results'][$key]['labor']['delta']['amount'];

            //
        }

        $data['results'][]['labor'] =
            [
                'index' => 'TOTAL',
                'name'  => 'TOTAL',
                'uom'   => 'USD',
                'budget' => Numeric::formatFloat($total_budget, 2, true),
                'actual' => Numeric::formatFloat($total_actual, 2, true),
                'delta'  => Numeric::formatFloat($total_delta, 2, true),
                'flex'   => Numeric::formatFloat(0, 2, true),
            ];

        return true;

    }
}