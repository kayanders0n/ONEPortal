<?php

namespace Traits\EP;

use Models\EP\LotListData;
use Helpers\Date;

trait LotListTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getLotListData(array $filters = [], string $limit = ''): array
    {
        $results = (new LotListData())->selectLotListData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildLotListItem($result);
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
     * @param int $sourceentityid
     * @return string
     */
    private function buildCompanyCode(int $sourceentityid): string {
        $result = 'UNK';
        switch ($sourceentityid) {
            case PLUMBING_ENTITYID: $result = 'PLU'; break;
            case CONCRETE_ENTITYID: $result = 'CON'; break;
            case FRAMING_ENTITYID: $result = 'FRA';
        }
        return $result;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildLotListItem($result): array
    {
        $item = [
            // community
            'lot'   => [
                'item_id'     => (int) $result->SEQNO,
                'num'         => (int) $result->TABLEID,
                'code'        => (string) $result->IDCODE,
                'name'        => htmlentities($result->DESCRIPT),
                'company'     => Self::buildCompanyCode((int)$result->SOURCEENTITYID),
                'start_date'  => Date::formatDate($result->STARTDATE, 'm/d/Y'),
            ],
        ];

        return $item;
    }
}
