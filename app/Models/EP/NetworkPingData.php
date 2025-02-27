<?php

namespace Models\EP;

use Core\Model;

class NetworkPingData extends Model
{
    /**
     * @param array $networks
     *
     * @return array
     */
    public function selectNetworkPingData(array $networks = []): array
    {
        foreach ($networks as $network) {
            $item = $this->getPingTime($network);
            $data[] = (object) $item;
        }

        return [$data, count($networks)];
    }

    private function getPingTime(array $network): array
    {
        $result = ['STATUS' => 'failed', 'TIME' => 9999, 'IP' => $network['ip'], 'NAME' => $network['name']];
        if ($network['ip']) {
            $ip = $network['ip'];
            if (getenv("OS") == "Windows_NT") {
                $exec = trim(exec("ping -n 3 " . $ip));
                $ping = explode(",", $exec);
                if (substr($exec, 0, 9) == 'Minimum =') {
                    $time = trim(str_replace('Average = ', '', $ping[2]));
                    $result = ['STATUS' => 'success', 'TIME' => $time, 'IP' => $network['ip'], 'NAME' => $network['name']];
                }
            } else {
                $exec = exec("ping -c 3 " . $ip);
                $ping = explode("/", end(explode("=", $exec)));
                if (substr($exec, 0, 11) == 'rtt min/avg') {
                    $time = ceil($ping[1]) . 'ms';
                    $result = ['STATUS' => 'success', 'TIME' => $time, 'IP' => $network['ip'], 'NAME' => $network['name']];
                }

            }
        }
        return $result;
    }
}
