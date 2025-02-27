<?php

namespace Models\EP;

use Core\Model;

class EstimatorBidsData extends Model
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    public function selectEstimatorBidsData(array $filters = [], string $limit = ''): array
    {
        $item_id    = (int) ($filters['item_id'] ?? null);
        $date_type  = (string) ($filters['date_type'] ?? null);
        $date_start = (string) ($filters['date_start'] ?? null);
        $date_end   = (string) ($filters['date_end'] ?? null);
        $search_type = (string) ($filters['search_type'] ?? null);

        $param_sql = [
            ':BID_ID' => $item_id,
            ':DATE_START' => $date_start,
            ':DATE_END' => $date_end
        ];

        $select_sql = 'SELECT 
                       SEQNO, 
                       TABLEID, 
                       CUSTOMER_NAME, 
                       PROJECT_NAME, 
                       PROJECT_NAME_SERIES, 
                       PROJECT_CITY, 
                       PROJECT_LOCATION, 
                       LOT_COUNT,
                       BID_DATE_DUE, 
                       BID_DATE_SENT, 
                       MARGIN_PLU, 
                       MARGIN_CON, 
                       MARGIN_FRA, 
                       MARGIN_DAT, 
                       STATUS_PLU, 
                       STATUS_CON, 
                       STATUS_FRA,
                       STATUS_DAT, 
                       AWARD_DATE, 
                       COMPLETED, 
                       COMPLETEDON, 
                       REFERNCE, 
                       COMMENT, 
                       NOTE, 
                       OVERRIDE, 
                       CREATEDON, 
                       CREATEDBYNAME,
                       MODIFIEDON, 
                       MODIFIEDBYNAME ' . PHP_EOL;

        $from_sql = 'FROM PROJECT_BIDS ' . PHP_EOL;

        $where_sql =  'WHERE 1 = 1 ';
        $where_sql .= $item_id ? 'AND PROJECT_BIDS.SEQNO = :BID_ID ': '';
        $where_sql .= 'AND PROJECT_BIDS.COMPLETED = 0 ' . PHP_EOL;

        $order_sql  = 'ORDER BY PROJECT_BIDS.BID_DATE_DUE '; // default sort

        if ($date_type == 'due') {
            $where_sql .= ($date_start && $date_end) ? 'AND PROJECT_BIDS.BID_DATE_DUE BETWEEN :DATE_START AND :DATE_END ' : '';
            $order_sql  = 'ORDER BY PROJECT_BIDS.BID_DATE_DUE ';
        } else if ($date_type ==  'sent') {
            $where_sql .= ($date_start && $date_end) ? 'AND PROJECT_BIDS.BID_DATE_SENT BETWEEN :DATE_START AND :DATE_END ' : '';
            $order_sql  = 'ORDER BY PROJECT_BIDS.BID_DATE_SENT ';
        } else if ($date_type == 'award') {
            $where_sql .= ($date_start && $date_end) ? 'AND PROJECT_BIDS.AWARD_DATE BETWEEN :DATE_START AND :DATE_END ' : '';
            $order_sql  = 'ORDER BY PROJECT_BIDS.AWARD_DATE ';
        }

        if ($search_type == 'missing') {
            $where_sql .= 'AND (' . PHP_EOL;
            $where_sql .= "(COALESCE(PROJECT_BIDS.STATUS_PLU, '') = '' AND COALESCE(PROJECT_BIDS.MARGIN_PLU, 0) = 0) OR " . PHP_EOL;
            $where_sql .= "(COALESCE(PROJECT_BIDS.STATUS_CON, '') = '' AND COALESCE(PROJECT_BIDS.MARGIN_CON, 0) = 0) OR " . PHP_EOL;
            $where_sql .= "(COALESCE(PROJECT_BIDS.STATUS_FRA, '') = '' AND COALESCE(PROJECT_BIDS.MARGIN_FRA, 0) = 0) OR " . PHP_EOL;
            $where_sql .= "(COALESCE(PROJECT_BIDS.STATUS_DAT, '') = '' AND COALESCE(PROJECT_BIDS.MARGIN_DAT, 0) = 0) " . PHP_EOL;
            $where_sql .= ') ' . PHP_EOL;
        } else if ($search_type == 'awarded') {
            $where_sql .= 'AND (' . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_PLU = 'AWARDED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_CON = 'AWARDED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_FRA = 'AWARDED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_DAT = 'AWARDED') " . PHP_EOL;
            $where_sql .= ') ' . PHP_EOL;
        } else if ($search_type == 'declined') {
            $where_sql .= 'AND (' . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_PLU = 'DECLINED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_CON = 'DECLINED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_FRA = 'DECLINED') OR " . PHP_EOL;
            $where_sql .= "(PROJECT_BIDS.STATUS_DAT = 'DECLINED') " . PHP_EOL;
            $where_sql .= ') ' . PHP_EOL;
        } else if ($search_type == 'sent') {
            $where_sql .= "AND PROJECT_BIDS.BID_DATE_SENT IS NOT NULL " . PHP_EOL;
        } else if ($search_type == 'notsent') {
            $where_sql .= "AND PROJECT_BIDS.BID_DATE_SENT IS NULL " . PHP_EOL;
        }

        $limit_sql = $limit ?: '';

        $query = $this->db->select($select_sql . $from_sql . $where_sql . $order_sql . $limit_sql, $param_sql);

        return [$query, count($query)];
    }

    /**
     * @param $request
     *
     * @return int
     */

    public function updateEstimatorBidsData($request): int
    {

        $data = [
            'CUSTOMER_NAME'       => html_entity_decode($request['customer_name'], ENT_QUOTES),
            'PROJECT_NAME'        => html_entity_decode($request['project_name'], ENT_QUOTES),
            'PROJECT_NAME_SERIES' => html_entity_decode($request['project_series'], ENT_QUOTES),
            'PROJECT_CITY'        => html_entity_decode($request['project_city'], ENT_QUOTES),
            'PROJECT_LOCATION'    => strtoupper($request['project_area']),
            'LOT_COUNT'           => (int)$request['lot_count'],
            'BID_DATE_DUE'        => $request['bid_date_due'],
            'BID_DATE_SENT'       => $request['bid_date_sent'],
            'AWARD_DATE'          => $request['bid_date_award'],
            'MODIFIEDBYNAME'     => $request['user_name'],
            'MODIFIEDON'         => date('m/d/Y H:i:s')
        ];

        $item_id = (int) ($request['item_id'] ?? null);

        if ($item_id == 0) { // new record

            $insert = ['CREATEDBYNAME' => $request['user_name'], 'CREATEDON' => date('m/d/Y H:i:s')];

            $data = array_merge($insert, $data); // add the CREATED info

            $result = $this->db->insert('PROJECT_BIDS', $data, 'SEQNO');

            $item_id = (int)$result;

            $where = ['SEQNO' => $item_id];
        } else {
            $where = ['SEQNO' => $item_id];
            $result = $this->db->update('PROJECT_BIDS', $data, $where);
        }

        if ($result) {
          unset($update); // clear the variable
          $company_id = (int) ($request['company_id'] ?? null);
          switch ($company_id) {
              case PLUMBING_ENTITYID: $update = ['MARGIN_PLU' => (float)$request['profit_margin'], 'STATUS_PLU' => strtoupper($request['bid_status'])]; break;
              case CONCRETE_ENTITYID: $update = ['MARGIN_CON' => (float)$request['profit_margin'], 'STATUS_CON' => strtoupper($request['bid_status'])]; break;
              case FRAMING_ENTITYID: $update = ['MARGIN_FRA' => (float)$request['profit_margin'], 'STATUS_FRA' => strtoupper($request['bid_status'])]; break;
              case DOORTRIM_ENTITYID: $update = ['MARGIN_DAT' => (float)$request['profit_margin'], 'STATUS_DAT' => strtoupper($request['bid_status'])]; break;
          }

          if ($company_id && (!empty($update))) {
              $this->db->update('PROJECT_BIDS', $update, $where);
          }
        }

        return $result;
    }
}
