<?php

namespace Traits\EP;

use Models\EP\JobOptionsData;
use Helpers\Date;

trait JobOptionsTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getJobOptionsData(array $filters = [], string $limit = ''): array
    {
        $results = (new JobOptionsData())->selectJobOptionsData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildJobOptionsItem($result);
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
     * @param $result (std object)
     *
     * @return array
     */
    private function buildJobOptionsItem($result): array
    {
        $item = [
            // option
            'option'   => [
                'id'            => (int)$result->OPTION_SEQNO,
                'code'          => htmlentities($result->OPTION_CODE),
                'name'          => htmlentities(cleanString($result->OPTION_NAME)),
                'units'         => (float)$result->OPTION_UNITS,
                'location'      => htmlentities($result->OPTION_LOCATION),
                'note'          => htmlentities(cleanstring($result->OPTION_NOTE)),
                'activity_date' => Date::formatDate($result->OPTION_DATE, 'm/d/Y'),
                'style_color'   => ($result->OPTION_CODE == '~CO~') ? 'color: red;' : '',
            ],
        ];

        return $item;
    }
}
