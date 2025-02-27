<?php

namespace Controllers\Auth;

use Core\Controller;
use Core\View;
use Helpers\Mail;
use Helpers\Request;
use Helpers\Response;
use Models\Users;
use Traits\EntityUserTrait;
use Traits\UserTrait;

class UserPasswordController extends Controller
{
    use EntityUserTrait;
    use UserTrait;

    public function forgotPassword()
    {
        $data = $this->registry(false);

        if (Request::isGet()) {

            $data['page'] = [
                'slug'  => 'forgot-password',
                'title' => 'Account Forgot Password'
            ];

            View::render('auth/header', $data);
            View::render('auth/forgot', $data);
            View::render('auth/footer', $data);

        } else if (Request::isPost()) {

            $request = Request::filterRequest($_POST, true);

            $token = $this->generateRequestToken($request);

            if (empty($token)) {
                Response::addStatus(200);
                $data = ['message' => 'Unauthorized'];
            } else {

                // Check user exists
                $user = $this->getUser([
                    'user_name' => $request['user_name'],
                    'completed' => false
                ]);

                if (empty($user)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Invalid user name'];
                } else {

                    $passcode = uniqid(rand(0, 8));

                    $updated = (new Users())->updateUserPasscode([
                        'user_id'  => $user['user_id'] ?? null,
                        'passcode' => $passcode
                    ]);

                    if (empty($updated)) {
                        Response::addStatus(200);
                        $data = ['message' => 'Something happened. Please contact support.'];
                    } else {

                        $to      = $user['email'];
                        $from    = 'no-reply@whittoncompanies.com';
                        $subject = 'Your TheWhittonWay Validation Code';
                        $message = '<p>Dear ' . ucfirst($user['first_name']) . ',</p>';
                        $message .= '<p>We have received a request to reset login credentials for this<br>
                                        email address. If you made this request, your validation code is<br>
                                        below. The following code will expire in 1 hour. If you did not<br>
                                        make this request, you may ignore this email.</p>';
                        $message .= '<p>Code: ' . $passcode . '</p>';
                        $message .= '<p>Your request will not be processed unless you validate your email<br>
                                        using the above code within 1 hour.</p>';
                        $message .= '<p>Regards,<br> Whitton Companies</p>';

                        logger(__METHOD__)->info('Sending New Passcode', [
                            'user_id' => $user['user_id'] ?? null,
                            'mail' => [
                                'to'      => $to,
                                'from'    => $from,
                                'subject' => $subject,
                                'message' => $message
                            ]
                        ]);

                        if (Mail::send($to, $from, $subject, $message)) {

                            logger(__METHOD__)->info('New Passcode Sent', [
                                'user_id' => $user['user_id'] ?? null
                            ]);
                        }

                        Response::addStatus(200);
                        $data = [
                            'message' => Response::$status[200],
                            'token'   => $token ?? null,
                            'user_id' => $user['user_id'] ?? null
                        ];
                    }
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

    public function validatePasscode()
    {
        $data = $this->registry(false);

        if (Request::isGet()) {

            $data['page'] = [
                'slug'  => 'validate-passcode',
                'title' => 'Account Validate Passcode'
            ];

            View::render('auth/header', $data);
            View::render('auth/passcode', $data);
            View::render('auth/footer', $data);

        } else if (Request::isPost()) {

            $request = Request::filterRequest($_POST, true);

            if (!$this->validateRequestToken($request)) {
                Response::addStatus(200);
                $data = ['message' => 'Unauthorized'];
            } else {

                // Validate passcode
                $user = $this->getUser([
                    'user_id'  => $request['user_id'] ?? null,
                    'passcode' => $request['passcode'] ?? null
                ]);

                if (empty($user)) {
                    Response::addStatus(200);
                    $data = ['message' => 'Invalid Code. Please try again.'];
                } else {

                    logger(__METHOD__)->info('Passcode Validated', [
                        'user_id'  => $request['user_id'] ?? null,
                        'passcode' => $request['passcode'] ?? null
                    ]);

                    Response::addStatus(200);
                    $data = [
                        'message' => Response::$status[200],
                        'token'   => $request['token'] ?? null,
                        'user_id' => $request['user_id'] ?? null
                    ];
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }

    public function resetPassword()
    {
        $data = $this->registry(false);

        if (Request::isGet()) {

            $data['page'] = [
                'slug'  => 'reset-password',
                'title' => 'Account Reset Password'
            ];

            View::render('auth/header', $data);
            View::render('auth/reset', $data);
            View::render('auth/footer', $data);

        } else if (Request::isPost()) {

            $request = Request::filterRequest($_POST, true);

            if (!$this->validateRequestToken($request)) {
                Response::addStatus(200);
                $data = ['message' => 'Unauthorized'];
            } else {

                $new_password     = $request['new_password'] ?? null;
                $confirm_password = $request['confirm_password'] ?? null;

                if ($new_password != $confirm_password) {
                    Response::addStatus(200);
                    $data = ['message' => 'Passwords do not match. Please try again.'];
                } else {

                    $updated = (new Users())->updateUserPassword([
                        'user_id'  => $request['user_id'] ?? null,
                        'password' => $new_password,
                    ]);

                    if (empty($updated)) {
                        Response::addStatus(200);
                        $data = ['message' => 'Something happened. Please contact support.'];
                    } else {

                        logger(__METHOD__)->info('Password Reset', [
                            'user_id' => $request['user_id'] ?? null
                        ]);

                        Response::addStatus(200);
                        $data = ['message' => Response::$status[200]];
                    }
                }
            }

            Response::sendHeaders();
            Response::json($data);
        }
    }


    /**
     * @param array $request
     *
     * @return null|string
     */
    private function generateRequestToken(array $request): ?string
    {
        if (!empty($request['user_ipv4']) && !empty($request['user_type'])) {
            return password_hash(md5($request['user_ipv4'] . $request['user_type']), PASSWORD_BCRYPT, ['cost' => 12]);
        }

        return null;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    private function validateRequestToken(array $request): bool
    {
        if (!empty($request['user_ipv4']) && !empty($request['user_type'])) {

            return password_verify(md5($request['user_ipv4'] . $request['user_type']), $request['token']);
        }

        return false;
    }
}
