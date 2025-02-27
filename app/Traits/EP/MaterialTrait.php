<?php

namespace Traits\EP;

use Models\EP\MaterialData;
use Helpers\Numeric;

trait MaterialTrait
{

    private bool $price_data = false;
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getMaterialData(array $filters = [], string $limit = ''): array
    {
        $this->price_data = (bool) ($filters['price_data'] ?? null);

        $results = (new MaterialData())->selectMaterialData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildMaterialItem($result);
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
    private function getMaterialItem(array $filters): ?array
    {
        return $this->getMaterialData($filters, 'ROWS 1');
    }


    /**
     * @param int $material_num
     * @return array
     */
    private function validateMaterialNum(int $material_num): array
    {
        $result = (new MaterialData())->getMaterialFromNum($material_num);

        return $result;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildMaterialItem($result): array
    {
        $item = [
            // data
            'material'   => [
                'item_id' => (int) $result->MATERIAL_SEQNO,
                'num'     => (int) $result->MATERIAL_TABLEID,
                'code'    => htmlentities($result->MATERIAL_IDCODE),
                'name'    => htmlentities(cleanString($result->MATERIAL_NAME)),
                'upc'     => htmlentities($result->MATERIAL_UPC),
                'to_uom'  => htmlentities($result->TAKEOFF_UOM),
            ],
            // category
            'category' => [
                'id'   => (int) $result->CATEGORY_SEQNO,
                'name' => htmlentities($result->CATEGORY_NAME),
            ],
        ];

        $upc_list = $this->buildMaterialItemUPCData((int)$result->MATERIAL_SEQNO);
        $item = array_merge($item, $upc_list); // merge the prices section

        if ($this->price_data) {
            $prices = $this->buildMaterialItemPricesData((int)$result->MATERIAL_SEQNO);
            $item = array_merge($item, $prices); // merge the prices section
        }

        return $item;
    }


    /**
     * @param int $item_id
     * @return array
     */
    private function buildMaterialItemUPCData(int $item_id): array
    {

        if ($results = (new MaterialData())->getMaterialItemUPCData($item_id)) {

            foreach ($results as $i => $result) {
                $data['upc_list'][$i] = $this->buildMaterialUPCItem($result);
            }

        } else {
            return [];
        }

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildMaterialUPCItem($result): array
    {
        $item = [
            // data
            'upc'   => [
                'item_id'   => (int) $result->UPC_SEQNO,
                'upc'       => htmlentities($result->UPC),
            ],
        ];

        return $item;
    }


    /**
     * @param int $item_id
     * @return array
     */
    private function buildMaterialItemPricesData(int $item_id): array
    {

        if ($results = (new MaterialData())->getMaterialItemPriceData($item_id)) {

            $total_price = 0;
            $last_price = 0;
            foreach ($results as $i => $result) {
                $last_price = (float) $result->SUPPLIER_PRICE;
                $total_price = $total_price + $last_price;
                $data['prices'][$i] = $this->buildMaterialPriceItem($result);
            }

            if ($total_price > 0) {
                if (round(($total_price / count($results)), 4) == round($last_price, 4)) {
                    $data['catalog']['std_price'] = Numeric::formatFloat((float)$last_price, 4, true);
                } else {
                    $data['catalog']['std_price'] = Numeric::formatFloat((float)0, 4, true);
                }
            } else {
                $data['catalog']['std_price'] = Numeric::formatFloat((float)0, 4, true);
            }


        } else {

            $data['catalog']['std_price'] = Numeric::formatFloat((float)0, 4, true);

        }

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildMaterialPriceItem($result): array
    {
        $item = [
            // data
            'price'   => [
                'item_id'            => (int) $result->PRICE_SEQNO,
                'supplier_name'      => htmlentities($result->SUPPLIER_NAME),
                'supplier_price'     => Numeric::formatFloat((float) $result->SUPPLIER_PRICE, 4, true),
                'supplier_reference' => htmlentities($result->SUPPLIER_REFERENCE),
                'site_name'          => htmlentities($result->SITE_NAME),
            ],
        ];

        return $item;
    }

    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array
     */
    private function getMaterialCategoryData(array $filters = [], string $limit = ''): array
    {
        $results = (new MaterialData())->selectMaterialCategoryData($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildMaterialCategoryItem($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            return $data['results'][0];
        }

        return $data;
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildMaterialCategoryItem($result): array
    {
        $item = [
            // data
            'category'   => [
                'item_id'     => (int) $result->ITEM_ID,
                'name' => htmlentities($result->CATEGORY_NAME),

            ],
        ];

        return $item;
    }
}
