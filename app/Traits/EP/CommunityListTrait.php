<?php

namespace Traits\EP;

use Models\EP\CommunityListData;

trait CommunityListTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getCommunityListData(array $filters = [], string $limit = ''): array
    {
        $results = (new CommunityListData())->selectCommunityListData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildCommunityListItem($result);
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
    private function getCommunityListItem(array $filters): ?array
    {
        return $this->getCommunityListData($filters, 'ROWS 1');
    }

    /**
     * @param int $plu
     * @param int $con
     * @param int $fra
     * @return string
     */
    private function buildFlags(int $plu, int $con, int $fra): string {
      $flags = '';
      if ($plu==1) { $flags .= '~P'; }
      if ($con==1) { $flags .= '~C'; }
      if ($fra==1) { $flags .= '~F'; }
      return $flags;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildCommunityListItem($result): array
    {
        $item = [
            // community
            'community'   => [
                'item_id'     => (int) $result->SEQNO,
                'num'         => (int) $result->TABLEID,
                'name'        => htmlentities($result->DESCRIPT),
                'flag_plu'    => (int) $result->ISPLUMBING,
                'flag_con'    => (int) $result->ISCONCRETE,
                'flag_fra'    => (int) $result->ISFRAMING,
                'flags'       => (string) Self::buildFlags((int)$result->ISPLUMBING, (int)$result->ISCONCRETE, (int)$result->ISFRAMING),
            ],
        ];

        return $item;
    }
}
