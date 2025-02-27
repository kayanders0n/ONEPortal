<?php

namespace Traits\EP;

use Models\EP\EstimatorBidsData;
use Helpers\Date;

trait EstimatorBidsTrait
{

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getEstimatorBidsData(array $filters = [], string $limit = ''): array
    {

        $results = (new EstimatorBidsData())->selectEstimatorBidsData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEstimatorBidsItem($result);
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
    private function getEstimatorBidsItem(array $filters): ?array
    {
        return $this->getEstimatorBidsData($filters, 'ROWS 1');
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEstimatorBidsItem($result): array
    {

        $bid_flag = '';
        if ((((float)$result->MARGIN_PLU == 0) && ((string)$result->STATUS_PLU == '')) ||
            (((float)$result->MARGIN_CON == 0) && ((string)$result->STATUS_CON == '')) ||
            (((float)$result->MARGIN_FRA == 0) && ((string)$result->STATUS_FRA == '')) ||
            (((float)$result->MARGIN_DAT == 0) && ((string)$result->STATUS_DAT == ''))) {
            $bid_flag = '<i class="fas fa-exclamation-circle" style="float: right; color: #cc0000; font-size: 1.0em; margin-right: 2px;" title="Missing Bids!"></i>';
        }

        $item = [
            'item' => [
                'item_id'        => (int)$result->SEQNO,
                'bid_num'        => (int)$result->TABLEID,
                'customer_name'  => htmlentities($result->CUSTOMER_NAME),
                'project_name'   => htmlentities($result->PROJECT_NAME),
                'project_series' => htmlentities($result->PROJECT_NAME_SERIES),
                'project_city'   => htmlentities($result->PROJECT_CITY),
                'project_area'   => htmlentities($result->PROJECT_LOCATION),
                'lot_count'      => (int)$result->LOT_COUNT,
                'bid_date_due'   => Date::formatDate($result->BID_DATE_DUE, 'm/d/Y'),
                'bid_date_sent'  => Date::formatDate($result->BID_DATE_SENT, 'm/d/Y'),
                'bid_date_award' => Date::formatDate($result->AWARD_DATE, 'm/d/Y'),
                'plumbing' => [
                    'margin' => number_format((float)$result->MARGIN_PLU, 2),
                    'status' => strtoupper($result->STATUS_PLU ?? ''),
                    'bid_info' => $this->formatBidInfo(PLUMBING_ENTITYID, (float)$result->MARGIN_PLU, (string)$result->STATUS_PLU),
                ],
                'concrete' => [
                    'margin' => number_format((float)$result->MARGIN_CON, 2),
                    'status' => $result->STATUS_CON ?? '',
                    'bid_info' => $this->formatBidInfo(CONCRETE_ENTITYID, (float)$result->MARGIN_CON, (string)$result->STATUS_CON),
                ],
                'framing' => [
                    'margin' => number_format((float)$result->MARGIN_FRA, 2),
                    'status' => strtoupper($result->STATUS_FRA ?? ''),
                    'bid_info' => $this->formatBidInfo(FRAMING_ENTITYID, (float)$result->MARGIN_FRA, (string)$result->STATUS_FRA),
                ],
                'door_trim' => [
                    'margin' => number_format((float)$result->MARGIN_DAT, 2),
                    'status' => strtoupper($result->STATUS_DAT ?? ''),
                    'bid_info' => $this->formatBidInfo(DOORTRIM_ENTITYID, (float)$result->MARGIN_DAT, (string)$result->STATUS_DAT),
                ],
                'bid_info'    => $this->formatBidInfo(PLUMBING_ENTITYID, (float)$result->MARGIN_PLU, (string)$result->STATUS_PLU, true) .
                                 $this->formatBidInfo(CONCRETE_ENTITYID, (float)$result->MARGIN_CON, (string)$result->STATUS_CON, true) .
                                 $this->formatBidInfo(FRAMING_ENTITYID, (float)$result->MARGIN_FRA, (string)$result->STATUS_FRA, true) .
                                 $this->formatBidInfo(DOORTRIM_ENTITYID, (float)$result->MARGIN_DAT, (string)$result->STATUS_DAT, true),
                'bid_flag' => $bid_flag,
                'bid_note'    => nl2br(htmlentities($result->NOTE)),
                'created_on'  => Date::formatDate($result->CREATEDON, 'm/d/Y h:i:s A'),
                'modified_on' => Date::formatDate($result->MODIFIEDON, 'm/d/Y h:i:s A'),
                'created_by'  => htmlentities(trim($result->CREATEDBYNAME)),
                'modified_by' => htmlentities(trim($result->MODIFIEDBYNAME)),
            ]
        ];

        return $item;
    }

    /***
     * @param int $company_id
     * @param float $margin
     * @param string $status
     * @return string
     */
    private function formatBidInfo(int $company_id, float $margin, string $status, bool $compressed = false): string
    {
        $result = '';

        $status_str = '';

        switch (trim($status)) {
            case 'DECLINED':
                $status_str = '<span style="color: pink; float: right;">' . ($compressed?'DC':'DECLINED') .'</span>';
                break;
            case 'AWARDED':
                $status_str = '<span style="color: black; float: right;">' . ($compressed?'AW':'AWARDED') .'</span>';
                break;
            case 'N/A':
                $status_str = '<span style="color: black; float: right;">' . ($compressed?'NA':'N/A') .'</span>';
                break;
            default:
                if ($compressed) { $status_str = str_pad((int)$margin, 2, "0", STR_PAD_LEFT); }
        }

        $margin_str = '';
        if ($margin > 0)  {
            $margin_str = '<span style="float: right; margin-right: 20px;">' . number_format($margin, 2) . '%'. '</span>';
            if ($compressed) { $margin_str = ''; }
        } else if (trim($status) == '') {
            $status_str = '<span style="color: red; float: right;">!! MISSING BID !!</span>';
            if ($compressed) {
                $status_str = '<span style="color: red; float: right;">MB</span>';
            }
        }

        $company = 'Unknown';
        $color = '#FFFFFF';

        switch ($company_id) {
            case PLUMBING_ENTITYID: $color = '#00FFFF;'; $company = ($compressed?'PLU':'Plumbing'); break;
            case CONCRETE_ENTITYID: $color = '#00FF00;'; $company = ($compressed?'CON':'Concrete'); break;
            case FRAMING_ENTITYID: $color = '#FFFF00;'; $company = ($compressed?'FRA':'Framing'); break;
            case DOORTRIM_ENTITYID: $color = '#C39B77;'; $company = ($compressed?'DAT':'Door and Trim'); break;
        }

        $result = '<div style="background-color: ' . $color .' padding: 3px;' . ($compressed?'float: left; margin-right: 2px;':'') .'">';
        $result .= $company . ': ' . $status_str . $margin_str . '</div>';
        if (!$compressed) {
            $result .= '<div style="clear: both; padding: 1px;"></div>';
        }

        return $result;
    }

}
