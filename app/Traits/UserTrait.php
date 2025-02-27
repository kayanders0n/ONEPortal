<?php

namespace Traits;

use Models\Users;

trait UserTrait
{
    /**
     * @param array $filters
     * @param string $limit
     *
     * @return array|null
     */
    private function getUsers(array $filters = [], string $limit = ''): ?array
    {
        $results = (new Users())->selectUsers($filters, $limit);

        foreach ($results[0] as $i => $result) {
            $data['results'][$i] = $this->buildUser($result);
        }

        $data['num_results'] = $results[1];

        // Strict single result
        if ($limit === 'ROWS 1') {
            return $data['results'][0] ?? [];
        }

        return $data;
    }

    /**
     * @param int $user_id
     *
     * @return array|null
     */
    private function getUser(array $filters): ?array
    {
        return $this->getUsers($filters, 'ROWS 1');
    }

    /**
     * @param string $user_name
     * @param string $password
     *
     * @return array|null
     */
    private function getUserByLogin(string $user_name, string $password): ?array
    {
        return $this->getUsers([
            'user_name' => $user_name,
            'password'  => $password,
        ], 'ROWS 1');
    }

    /**
     * @param $result (std object)
     * @param string $scope
     *
     * @return array
     */
    private function buildUser($result): array
    {
        // Check EMPLOYEE_PW_CODE_DATE passed
        if (!empty($result->EMPLOYEE_NUM) && !empty($result->EMPLOYEE_PW_CODE_DATE)) {

            $current_time = strtotime(date('Y-m-d H:i:s'));
            $pw_code_time = strtotime($result->EMPLOYEE_PW_CODE_DATE);

            if ($current_time > ($pw_code_time + 3600)) {
                (new Users())->deleteUserPasscode(['user_id' => $result->EMPLOYEE_NUM]);
            }
        }

        // Map user role
        switch ($result->EMPLOYEE_GLSUFFIX) {
            case '00100':
                $user_role = 'super';
                break;
            case '00150':
                $user_role = 'production';
                break;
            case '00155':  // warranty & service
            case '00175':
                $user_role = 'service';
                break;
            case '00270':
                $user_role = 'estimating';
                break;
            case '00300':
                $user_role = 'prefab';
                break;
            case '00350':
                $user_role = 'warehouse';
                break;
            case '00400':
                $user_role = 'fleet';
                break;
            case '00700':
                $user_role = 'hr';
                break;
            case '00800':
                $user_role = 'accounting';
                break;
            case '00850':
                $user_role = 'it';
                break;
            case '00910':
                $user_role = 'management';
                break;
            default:
                $user_role = '';
        }

        $data = [
            'seq_id'          => $result->SEQNO,
            'user_id'         => $result->EMPLOYEE_NUM,
            'user_name'       => $result->EMPLOYEE_LOGINNAME,
            'description'     => $result->EMPLOYEE_NAME,
            'first_name'      => $result->EMPLOYEE_FIRSTNAME,
            'last_name'       => $result->EMPLOYEE_LASTNAME,
            'email'           => $result->EMPLOYEE_EMAIL,
            'password'        => $result->EMPLOYEE_PW_HASH,
            'passcode'        => $result->EMPLOYEE_PW_CODE,
            'passcode_date'   => $result->EMPLOYEE_PW_CODE_DATE,
            'password_legacy' => $result->EMPLOYEE_PW,
            'type_id'         => $result->EMPLOYEE_TYPEID,
            'company_id'      => $result->EMPLOYEE_SOURCEENTITYID,
            'user_type'       => 'employee',
            'user_role'       => $user_role,
            'site'            => (int) $result->EMPLOYEE_GLPREFIX,
            'department'      => (int) $result->EMPLOYEE_GLSUFFIX
        ];

        return $data;
    }
}
