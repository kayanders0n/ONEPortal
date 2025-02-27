<?php

namespace Traits\EP;

use Models\EP\JobData;
use Helpers\Date;

trait JobTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobsData(array $filters = [], string $limit = ''): array
    {
        $results = (new JobData())->selectJobsData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobsItem($result);
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
    private function getJobsItem(array $filters): ?array
    {
        return $this->getJobsData($filters, 'ROWS 1');
    }

    /**
     * @param int $job_num
     * @return int
     */
    private function validateJobNum(int $job_num): int
    {
        $result = (new JobData())->getJobsSeqnoFromNum($job_num);

        return $result;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildJobsItem($result): array
    {

        $item = [
            // Job
            'job'   => [
                'item_id'        => (int) $result->JOB_SEQNO,
                'num'            => (int) $result->JOB_NUM,
                'house_hand'     => htmlentities($result->JOB_HOUSEHAND),
                'note'           => nl2br(htmlentities($result->JOB_NOTE)),
                'start_date'     => Date::formatDate($result->JOB_STARTDATE, 'm/d/Y'),
                'completed_date' => Date::formatDate($result->JOB_COMPLETEDON, 'm/d/Y'),
                'created_on'     => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'created_by'     => $result->CREATEBYNAME,
                'modified_on'    => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'modified_by'    => $result->MODIFIEDBYNAME,
            ],
            // Source Company
            'company' => [
                'id'         => (int) $result->SOURCEENTITY_SEQNO,
                'num'        => (int) $result->SOURCEENTITY_NUM,
                'name'       => htmlentities($result->SOURCEENTITY_NAME),
            ],
            // Builder
            'builder' => [
                'id'         => (int) $result->BUILDER_SEQNO,
                'num'        => (int) $result->BUILDER_NUM,
                'name'       => htmlentities($result->BUILDER_NAME),
            ],
            // Jobsite
            'jobsite' => [
                'id'        => (int) $result->JOBSITE_SEQNO,
                'num'       => (int) $result->JOBSITE_NUM,
                'code'      => (string) $result->JOBSITE_IDCODE,
                'name'      => htmlentities($result->JOBSITE_NAME),
                'address1'  => htmlentities(cleanString($result->JOBSITE_ADDRESS1)),
                'address2'  => htmlentities(cleanString($result->JOBSITE_ADDRESS2)),
                'city'      => htmlentities(cleanString($result->JOBSITE_CITY)),
                'state'     => htmlentities(strtoupper($result->JOBSITE_STATE)),
                'zip'       => htmlentities(cleanString($result->JOBSITE_ZIP)),
                'bluestake' => htmlentities($result->JOBSITE_BLUESTAKENUM),
                'coe_date'  => Date::formatDate($result->JOBSITE_COE, 'm/d/Y'),
            ],
            // project
            'project' => [
                'id'       => (int) $result->PROJECT_SEQNO,
                'num'      => (int) $result->PROJECT_NUM,
                'name'     => htmlentities($result->PROJECT_NAME),
            ],
            // plan
            'plan' => [
                'id'       => (int) $result->PLAN_SEQNO,
                'num'      => (int) $result->PLAN_NUM,
                'code'     => htmlentities($result->PLAN_CODE),
                'elevation' => htmlentities($result->ELEVATION_NAME),
            ],
        ];

        $company_id = (int)$result->SOURCEENTITY_SEQNO;

        $estimator = $this->buildJobEsitmatorData($company_id, (int)$result->BUILDER_SEQNO);

        $item = array_merge($item, $estimator); // merge the estimator section

        if ($company_id == CONCRETE_ENTITYID) {
            $concrete = $this->buildJobConcreteData((int)$result->JOB_SEQNO);
            $item = array_merge($item, $concrete); // merge the concrete section
        }

        return $item;
    }

    private function buildJobEsitmatorData(int $company_id, int $builder_id): array
    {
        $results = (new JobData())->getJobsEstimatorData($company_id, $builder_id);

        $item = [
            // Estimator
            'estimator'   => [
                'id'    => (int)$results->EMPLOYEE_SEQNO,
                'num'   => (int)$results->EMPLOYEE_NUM,
                'name'  => htmlentities($results->EMPLOYEE_NAME),
                'email' => htmlentities($results->EMPLOYEE_EMAIL),
            ],
        ];

        return $item;
    }

    private function buildJobConcreteData(int $job_id): array
    {
        $concrete = (new JobData())->getJobsProjectConcreteData($job_id);

        // 10076726 ABC TAKEOFF ITEM
        // 62495980 CABLE TAKEOFF ITEM

        $abcvendor = (new JobData())->getJobsPOVendorData($job_id, 10076726);
        $abctotal = (new JobData())->getJobsTakeoffTotalData($job_id, 10076726);

        $cablevendor = (new JobData())->getJobsPOVendorData($job_id, 62495980);
        $cabletotal = (new JobData())->getJobsTakeoffTotalData($job_id, 62495980);

        $item = [
            'concrete' => [
                // Project Info
                'project'   => [
                    'pump'              => htmlentities($concrete->CONCRETE_PUMPORDERED),
                    'inspection'        => htmlentities($concrete->CONCRETE_INSPECTIONCALLED),
                    'pretreat'          => htmlentities($concrete->CONCRETE_PRETREATORDERED),
                    'laser'             => htmlentities($concrete->CONCRETE_LASERSCREED),
                    'concrete_mix_code' => htmlentities($concrete->CONCRETE_MIXCODE),
                    'concrete_vendor'   => htmlentities($concrete->CONCRETE_VENDOR),
                ],
                // Post Tension Cables
                'cable'  => [
                    'vendor'    => htmlentities($cablevendor->VENDOR_NAME ?? ''),
                    'email'     => htmlentities($cablevendor->VENDOR_EMAIL ?? ''),
                    'units'     => (float) ($cabletotal->TOTAL_UNITS ?? 0),
                    'po_num'    => (int) ($cablevendor->PO_NUM ?? 0),
                ],
                // ABC
                'abc'   => [
                    'vendor'    => htmlentities($abcvendor->VENDOR_NAME ?? ''),
                    'email'     => htmlentities($abcvendor->VENDOR_EMAIL ?? ''),
                    'units'     => (float) ($abctotal->TOTAL_UNITS ?? 0),
                    'po_num'    => (int) ($abcvendor->PO_NUM ?? 0),
                ]
            ]
        ];

        return $item;
    }


}
