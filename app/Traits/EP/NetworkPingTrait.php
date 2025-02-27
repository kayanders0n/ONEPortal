<?php

namespace Traits\EP;

use Models\EP\NetworkPingData;

trait NetworkPingTrait
{
    /**
     * Takes and array of Networks and returns ping times
     * Networks = ['ip'=>'127.0.0.1', 'name'=>'Network Name']
     *
     * @param array $networks
     *
     * @return array
     */
    private function getNetworkPingData(array $networks): array
    {
        $results = (new NetworkPingData())->selectNetworkPingData($networks);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildNetworkPingItem($result);
        }

        $data['num_results'] = $results[1];

        return $data;
    }


    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildNetworkPingItem($result): array
    {
        $item = [
            // Network
            'network' => [
                'ip' => (string)$result->IP,
                'name' => htmlentities((string)$result->NAME),
                'status' => htmlentities((string)$result->STATUS),
                'time' => (int)$result->TIME,
                'uom' => 'ms'
            ],

        ];

        return $item;
    }
}
