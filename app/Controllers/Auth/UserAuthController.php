<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\View;
use Helpers\Request;
use Helpers\Response;
use Helpers\Session;
use Helpers\Url;
use Models\Users;
use Traits\EntityUserTrait;
use Traits\UserTrait;

class UserAuthController extends Controller
{
    use EntityUserTrait;
    use UserTrait;

    public function showLogin()
    {
        $data = $this->registry(false);

        // Legacy users
        if (!empty($data['params']['alu'])) {
            Url::redirect('/auth/login/legacy');
        }

        // Page Object
        $data['page'] = [
            'slug'  => 'login',
            'title' => 'Account Login'
        ];

        View::render('auth/header', $data);
        View::render('auth/login', $data);
        View::render('auth/footer', $data);
    }

    public function doUserLogin()
    {
        if (Request::isAjax()) {

            $request = $_REQUEST;

            if ($request['action'] === 'user-login') {

                $request['user_type'] = trim($request['user_type']) ?: '';
                $request['user_name'] = trim($request['user_name']) ?: '';
                $request['password']  = trim($request['user_password']) ?: '';

                if (empty($request['user_type']) || empty($request['user_name']) || empty($request['password'])) {
                    Response::addStatus(400);
                    $data = ['message' => Response::$status[400]];
                } else {
                    $data = $this->authenticateUser($request);
                }

                Response::sendHeaders();
                Response::json($data);
            }
        }
    }

    public function doUserLogout()
    {
        Session::destroy();
        setcookie(SESSION_PREFIX . 'auth', '', -1, '/', $_SERVER['HTTP_HOST']);
        Url::redirect('/auth/login');
    }

    /**
     * @param int $user_id
     * @param string $user_type
     * @param string $user_role
     *
     * @return string|null
     */
    public function getUserEnvironment(int $user_id, string $user_type = 'employee', string $user_role = '')
    {
        $security = json_decode(file_get_contents(config('sec_file')));

        if ($user_type == 'entity') {

            // group entities like "all" need to be last so it will look for the number first
            foreach ($security->entities as $entity) {
                if ($entity->id == $user_id || (($user_role != '') && ($entity->id == $user_role))) {
                    return $entity->environment;
                }
            }

        } else if ($user_type == 'employee') {

            // group users like "super" need to be last so it will look for the number first
            foreach ($security->users as $user) {
                if ($user->id == $user_id || (($user_role != '') && ($user->id == $user_role))) {
                    return $user->environment;
                }
            }
        }

        return null;
    }

    private function authenticateUser($request)
    {
        // Check user exists
        if ($request['user_type'] == 'employee') {
            $user = $this->getUser([
                'user_name' => $request['user_name'],
                'completed' => false
            ]);
        } else {
            $user = $this->getEntityUser([
                'entity_id' => $request['user_name'],
                'completed' => false
            ]);
        }

        if (empty($user) || (!password_verify(md5(trim($request['password'])), $user['password']) && empty($request['password_match']))) {
            Response::addStatus(200);
            $data = ['message' => 'Invalid user name or password. Try again.'];
        } else {

            $environment = $this->getUserEnvironment($user['user_id'], $user['user_type'], $user['user_role']);

            if (empty($environment)) {
                Response::addStatus(200);
                $data = ['message' => 'Environment not set'];
            } else {

                // Build session array
                $user_auth = [
                    'authed'    => 1,
                    'seq_id'    => $user['seq_id'] ?? null,
                    'user_id'   => $user['user_id'] ?? null,
                    'user_env'  => $environment ?? null,
                    'user_ipv4' => $request['ipv4'] ?? null
                ];

                Session::set('auth', serialize($user_auth));

                $expire = 0; // just this session
                if ($request['remember_me'] ?? false) {
                    $expire = time() + (60 * 60 * 24 * 365); // 1 year
                }
                if ((!isset($_COOKIE[SESSION_PREFIX . 'auth'])) || ($request['remember_me'] ?? false)) {
                    setcookie(SESSION_PREFIX . 'auth', serialize($user_auth), $expire, '/', $_SERVER['HTTP_HOST']);
                }

                Response::addStatus(200);
                $data = [
                    'message'      => Response::$status[200],
                    'user_session' => Session::display()
                ];
            }
        }

        return $data;
    }

    public function doLegacyUserLogin()
    {
        $data = $this->registry();

        // Check for alu(auth legacy user) param
        if (empty($data['params']['alu'])) {
            $this->doUserLogout();
        }

        $alu_string = urlDecrypt($data['params']['alu']);

        parse_str($alu_string, $alu_params);

        // Check individual params
        $request = [
            'user_name'       => $alu_params['user_name'],
            'password_legacy' => $alu_params['password_legacy'],
            'redirect_url'    => $alu_params['redirect_url']
        ];

        // Does user exist
        $user = $this->getUser(['user_name' => $request['user_name']]);

        if (!empty($user['user_id'])) {

            $request['password_match'] = false;
            if ($request['password_legacy'] == $user['password_legacy']) {
                $request['password_match'] = true;
            }

            $request = $request + $user;

            if (!empty($user['password'])) {
                $updated = 1;
            } else {
                // Update user password
                $request['password'] = $user['password_legacy']; // push the legacy password into the new TWW as the starting default password for the user
                $updated = (new Users())->updateUserPassword($request);
            }

           if (!empty($updated)) {

                // Authenticate user
                $data = $this->authenticateUser($request);

                if (!empty($data['user_session'])) {

                    // Redirect user
                    Url::redirect($alu_params['redirect_url']);
                }
            }
        }
    }
}
