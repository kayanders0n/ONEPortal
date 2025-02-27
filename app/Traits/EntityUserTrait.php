<?php

namespace Traits;

use Models\EntityUsers;

trait EntityUserTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array|null
     */
    private function getEntityUsers(array $filters = [], string $limit = ''): ?array
    {
        $results = (new EntityUsers())->selectEntityUsers($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildEntityUser($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            return $data['results'][0];
        }

        return $data;
    }

    /**
     * @param array $filters
     *
     * @return array|null
     */
    private function getEntityUser(array $filters): ?array
    {
        return $this->getEntityUsers($filters, 'ROWS 1');
    }

    /**
     * @param string $entity_id
     * @param string $password
     *
     * @return array|null
     */
    private function getEntityUserByLogin(string $entity_id, string $password): ?array
    {
        return $this->getEntityUsers([
            'entity_id' => $entity_id,
            'password'  => $password,
        ], 'ROWS 1');
    }

    /**
     * @param $result (std object)
     *
     * @return array
     */
    private function buildEntityUser($result): array
    {
        $data = [
            'seq_id'      => $result->SEQNO,
            'user_id'     => $result->ENTITY_NUM,
            'description' => $result->ENTITY_NAME,
            'email'       => $result->ENTITY_EMAIL,
            'password'    => $result->ENTITY_PW_HASH,
            'user_type'   => 'entity',
            'user_role'   => 'all'
        ];

        return $data;
    }
}
