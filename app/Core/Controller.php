<?php

namespace Core;

use Helpers\Session;
use Helpers\Url;
use Traits\EntityUserTrait;
use Traits\UserTrait;
use Helpers\Request;

abstract class Controller
{
    use EntityUserTrait;
    use UserTrait;

    protected $data;
    protected $path;
    protected $view;
    protected $user = [];
    protected $user_id = 0;

    public function __construct()
    {
        $this->path = new Path();
        $this->view = new View();
    }

    public function registry(bool $require_auth = true, string $slug = ''): array
    {
        // Path
        $path = $this->path->segments();

        // Querystring
        $params = Url::parseQueryString();

        // Limit Results
        $limit = '';
        if (!empty($params['limit'])) {
            if ($params['limit'] == 'none') {
                $limit = '';
            } else {
                $limit = 'ROWS ' . $params['limit'];
            }
        }

        // Check authed user
        if (($path[2] ?? null) == 'login' && empty($path[3])) {

            if (Session::exists('auth')) {
                Url::redirect('/');
            }

            if (isset($_COOKIE[SESSION_PREFIX . 'auth'])) {
                Session::set('auth', $_COOKIE[SESSION_PREFIX . 'auth']);
                Url::redirect('/');
            }

        } else if (($path[2] ?? null) != 'login' && $require_auth) {

            if (!Session::exists('auth')) {
                if (isset($_COOKIE[SESSION_PREFIX . 'auth'])) {
                    Session::set('auth', $_COOKIE[SESSION_PREFIX . 'auth']);
                }
            }

            if (Session::exists('auth')) {
                $user_auth     = unserialize(Session::get('auth'));
                $this->user_id = (int) $user_auth['user_id'];
            }

            $is_authed = $this->isAuthed($this->user_id);

            if (!$is_authed) {
                Url::redirect('/auth/logout');
                exit();
            } else {
                if (!empty($this->user_id)) {
                    if (config('app.portal') == 'ep') {
                        $this->user = $this->getUser(['user_id' => $this->user_id]);
                    } else {
                        $this->user = $this->getEntityUser(['entity_id' => $this->user_id]);
                    }
                }
            }
        }

        // Registry Data
        $this->data = [
            'path'      => $path,
            'params'    => $params,
            'limit'     => $limit ?: '',
            'user'      => $this->user,
            'nav_items' => $this->getNavItems(config('app.portal')),
            'nav_icons' => $this->getNavIcons(config('app.portal'))
        ];

        if (!empty($this->user_id)) {
            $this->data['user']['dashboards'] = $this->getUserDashboards($this->user_id, $this->user['user_type'], $this->user['user_role']);
            $this->data['user']['default_dashboard'] = $this->getUserDefaultDashboard($this->user_id, $this->user['user_type'], $this->user['user_role']);
        }

        return $this->data;
    }

    /**
     * @return bool
     */
    public function isAuthed(int $user_id = 0): bool
    {
        $authed = true;

        if (empty($user_id)) {
            Session::destroy();
            $authed = false;
        }

        return $authed;
    }

    public function hasSecurityToken($id, $token, string $user_type = 'employee', string $user_role = '')
    {
        $result   = false;
        $security = json_decode(file_get_contents(config('sec_file')));

        if ($user_type == 'entity') {

            foreach ($security->entities as $entity) {
                if ($entity->id == $id || (($user_role != '') && ($entity->id == $user_role))) {
                    if (in_array($token, $entity->tokens)) {
                        $result = true;
                    }
                    break;
                }
            }
        } else if ($user_type == 'employee') {

            foreach ($security->users as $user) {
                if ($user->id == $id || (($user_role != '') && ($user->id == $user_role))) {
                    if (in_array($token, $user->tokens)) {
                        $result = true;
                    }
                    break;
                }
            }
        }

        return $result;
    }

    public function hasAnySecurityToken($id, $tokens, string $user_type = 'employee', string $user_role = '')
    {
        $result   = false;
        $security = json_decode(file_get_contents(config('sec_file')));

        if ($user_type == 'entity') {
            foreach ($security->entities as $entity) {
                if ($entity->id == $id || (($user_role != '') && ($entity->id == $user_role))) {
                    foreach ($tokens as $token) {
                        if (in_array($token, $entity->tokens)) {
                            $result = true;
                            break;
                        }
                    }
                    break;
                }
            }
        } else if ($user_type == 'employee') {
            foreach ($security->users as $user) {
                if ($user->id == $id || (($user_role != '') && ($user->id == $user_role))) {
                    foreach ($tokens as $token) {
                        if (in_array($token, $user->tokens)) {
                            $result = true;
                            break;
                        }
                    }
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param int $user_id
     * @param string $user_type
     * @param string $user_role
     *
     * @return string|null
     */
    public function getUserDefaultCompany(int $user_id, string $user_type = 'employee', string $user_role = '')
    {
        $security = json_decode(file_get_contents(config('sec_file')));

        $default_company = null;

        if ($user_type == 'entity') {

            // group entities like "all" need to be last so it will look for the number first
            foreach ($security->entities as $entity) {

                if ($entity->id == $user_id || (($user_role != '') && ($entity->id == $user_role))) {
                    $default_company = $entity->default_company ?? '';
                    break;
                }
            }

        } else if ($user_type == 'employee') {

            // group users like "super" need to be last so it will look for the number first
            foreach ($security->users as $user) {
                if ($user->id == $user_id || (($user_role != '') && ($user->id == $user_role))) {
                    $default_company = $user->default_company ?? '';
                    break;
                }
            }
        }

        if (empty($default_company)) {
            if (($user_id == $this->user_id) && ($user_type == 'employee')) {
                $default_company = $this->user['company_id'];
            }
        }

        if (isset($_SESSION['default_company_id'])) {
            $default_company = $_SESSION['default_company_id'];
        }

        return $default_company;
    }

    /**
     * @param int $user_id
     * @param string $user_type
     * @param string $user_role
     *
     * @return array|null
     */
    public function getUserDashboards(int $user_id, string $user_type = 'employee', string $user_role = ''): ?array
    {
        $security = json_decode(file_get_contents(config('sec_file')));

        if ($user_type == 'entity') {

            // group entities like "all" need to be last so it will look for the number first
            foreach ($security->entities as $entity) {
                if ($entity->id == $user_id || $entity->id == "all") {
                    return $entity->dashboard;
                }
            }

        } else if ($user_type == 'employee') {

            $default_dashboards = array('home');
            // group users like "super" need to be last so it will look for the number first
            foreach ($security->users as $user) {
                if ($user->id == $user_id || (($user_role != '') && ($user->id == $user_role))) {
                    return array_merge($default_dashboards, $user->dashboard);
                }
            }
        }

        return null;
    }

    /**
     * @param int $user_id
     * @param string $user_type
     * @param string $user_role
     *
     * @return string|null
     */
    public function getUserDefaultDashboard(int $user_id, string $user_type = 'employee', string $user_role = ''): ?string
    {
        $security = json_decode(file_get_contents(config('sec_file')));

        if ($user_type == 'entity') {

            // group entities like "all" need to be last so it will look for the number first
            foreach ($security->entities as $entity) {
                if ($entity->id == $user_id || $entity->id == "all") {
                    return $entity->default_dashboard ?? null;
                }
            }

        } else if ($user_type == 'employee') {

            // group users like "super" need to be last so it will look for the number first
            foreach ($security->users as $user) {
                if ($user->id == $user_id || (($user_role != '') && ($user->id == $user_role))) {
                    return $user->default_dashboard ?? null;
                }
            }
        }

        return null;
    }

    public function getNavItems(string $portal)
    {
        $this->main_nav = ['Dashboard' => '/'];

        $nav_items = [];

        if ($this->user_id) {

            switch ($portal) {
                case 'ep':

                    if (!$this->hasSecurityToken($this->user_id, 'payroll_admin', $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Schedule'] = 'schedule';
                    }
                    $nav_items['Jobs'] = 'jobs';
                    if (!$this->hasSecurityToken($this->user_id, 'payroll_admin', $this->user['user_type'], $this->user['user_role'])) {
                        if ($this->hasAnySecurityToken($this->user_id, ['admin', 'super'], $this->user['user_type'], $this->user['user_role'])) {
                            $nav_items['Tickets'] = 'tickets';
                        }
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'super', 'budgets'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Job Budgets'] = 'job-budgets';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'estimator'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Estimator'] = 'estimator';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'catalog'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Catalog'] = 'catalog';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'warehouse'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Warehouse'] = 'warehouse';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'management'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Key Indicators'] = 'key-indicators';
                    }
                    $nav_items['AP Approval'] = 'ap-approval';
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'service'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Service'] = 'service';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'hyphen'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Hyphen'] = 'hyphen';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'management', 'payroll_admin', 'super', 'manager'], $this->user['user_type'],
                        $this->user['user_role'])) {
                        $nav_items['Employee'] = 'employee';
                    }
                    if ($this->hasAnySecurityToken($this->user_id, ['admin', 'management', 'super', 'manager'], $this->user['user_type'], $this->user['user_role'])) {
                        $nav_items['Visual Record'] = 'twwvr';
                    }

                    break;
                case 'bp':
                    $nav_items['Job QA'] = 'job-qa';
                    break;
                case 'vp':
                    break;
                default:
                    break;
            }
        }

        return $nav_items;
    }

    public function getNavIcons(string $portal)
    {
        switch ($portal) {
            case 'ep':
                $nav_icons = [
                    'dashboard'      => '<i class="fas fa-tachometer-alt fa-fw"></i>',
                    'schedule'       => '<i class="fas fa-calendar-alt fa-fw"></i>',
                    'jobs'           => '<i class="fas fa-truck fa-fw"></i>',
                    'tickets'        => '<i class="fas fa-check-double fa-fw"></i>',
                    'job-budgets'    => '<i class="fas fa-hand-holding-usd fa-fw"></i>',
                    'estimator'      => '<i class="fas fa-user-cog fa-fw"></i>',
                    'catalog'        => '<i class="fas fa-book-open fa-fw"></i>',
                    'warehouse'      => '<i class="fas fa-warehouse fa-fw"></i>',
                    'key-indicators' => '<i class="fas fa-key fa-fw"></i>',
                    'ap-approval'    => '<i class="fas fa-thumbs-up fa-fw"></i>',
                    'service'        => '<i class="fas fa-wrench fa-fw"></i>',
                    'hyphen'         => '<i class="fas fa-building fa-fw"></i>',
                    'employee'       => '<i class="fas fa-user fa-fw"></i>',
                    'twwvr'          => '<i class="fas fa-photo-video fa-fw"></i>',
                ];
                break;
            case 'bp':
                $nav_icons = [
                    'dashboard' => '<i class="fas fa-tachometer-alt fa-fw"></i>',
                    'job-qa'    => '<i class="fas fa-video fa-fw"></i>',
                ];
                break;
            case 'vp':
                $nav_icons = [];
                break;
            default:
                $nav_icons = [
                    'dashboard' => '<i class="fas fa-tachometer-alt fa-fw"></i>'
                ];
                break;
        }

        return $nav_icons;
    }
}
