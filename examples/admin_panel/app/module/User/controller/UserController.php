<?php

Liber::loadController('BaseController');

/**
 * UserController
 *
 */
class UserController extends BaseController {

    var $url_base;

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper(array('Url', 'HTML'));
        $this->view()->template('default.html');
        $this->url_base = url_to_('/user', true).'/';
    }

    public function index() {
        $this->isUserLogged();

    }

    public function logout() {
        $User = Liber::loadModel('User', 'User' ,  true);
        $User->logout();
        Liber::redirect($this->url_base.'login');
    }

    public function login() {
        list($Sec, $Attempt) = Liber::loadClass(array('Security', 'Attempt'));
        Liber::loadHelper('DT');
        Liber::loadClass(array('Session'));

        $Sec              = new Security;
        $Attempt          = new Attempt('login-attempt');
        $User             = Liber::loadModel('User', 'User' , true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;

        if ( Http::post() ) {
            $PreLogin = new Session('pre-login');

            // verify token
            if ( $Sec->token() == Http::post('t') ) {

                // first post
                if ( Http::post('username') ) {

                    $PreLogin->val('username', Http::post('username'));
                    $this->respOk('ok');
                    return;

                // second post
                } elseif ( Http::post('hpassword') ) {

                    $hasError = false;
                    $Attempt->watch(3);
                    $Sec->startDelay(3); // avoid timing attack

                    if ( !$Attempt->isBlocked() ) {

                        // search for username
                        if ( !$hasError and !($user = $User->searchBy('user_name', $PreLogin->val('username'))->fetch()) ) {
                            $this->log('Username not found ['.$PreLogin->val('username').']. ');
                            $hasError = true;
                        }

                        // username/password is valid
                        if ( !$hasError and  !$User->authenticate( $PreLogin->val('username'), Http::post('hpassword') ) ) {
                            $this->log('Authentication fail for ['.$PreLogin->val('username').'].');
                            $hasError = true;
                        }

                        // Login username
                        if ( !$hasError and !$User->login($user) ) {
                            $this->log('Problem creating session login for ['.$PreLogin->val('username').'].');
                            $hasError = true;
                        }

                        if ( $hasError ) {
                            $Attempt->increase();
                            $resp = array();
                            if ( $Attempt->isBlocked() ) {
                                $resp = array('redirect' => url_to_($data['url'], true));
                            }
                            $this->log( print_r($User->errors(), true) );
                            $this->respError('error', $resp);

                        } else {
                            $PreLogin->clear();
                            $this->log( "Login for ".$user['user_name'] );
                            $this->respOk('ok', array('redirect' => url_to_('/', true)));
                        }
                    }

                    $Sec->stopDelay();
                }

            } else {
                $this->log( "token missing." );
            }

        } else {

            $User->logout();
            $data['wait_time']      = dt_timesince_(time()-$Attempt->waitTime());
            $data['isLoginBlocked'] = $Attempt->isBlocked();
            $data['token']          = $Sec->token(true);
            $this->view()->load( "login.html", $data );
        }
    }



    public function recover_access( $to_step = 1 ) {
        list($Sec, $Session) = Liber::loadClass(array('Security', 'Session'), true);
        Liber::loadClass('Attempt');
        $Attempt          = new Attempt('recover_access');
        $User             = Liber::loadModel('User', 'User', true);
        $data['url_base'] = $this->url_base;
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['descUser'] = $User->toArray('desc');
        $recover_data     = $Session->val('recover_data');

        if ( !$recover_data ) {
            $Session->val('recover_data', array());
        }
        if ( !$Attempt->isWatching() ) {
            $Attempt->watch(5);
        }

        // attempts limit
        if ( $Attempt->isBlocked() ) {
            Liber::loadHelper('DT');
            $data['wait_time'] = dt_timesince_( time()-$Attempt->waitTime() );
            $data['type']      = 'wait';
            $this->view()->load( "recover_invalid_message.html" , $data);
            return;
        }

        // fill credentials
        if ( $to_step == 1  ) {
            $Sec->clientWatch();
            $data['token']        = $Sec->token(true);
            $recover_data['step'] = '1';
            $Session->val('recover_data', $recover_data);
            $this->view()->load( "recover_access1.html" , $data);
        } else {
            if ( Http::post() ) {
                if ( !$Sec->validToken( Http::post('token') ) or $Sec->clientChanged() ) {
                    $Attempt->increase();
                    $data['type'] = 'invalid';
                    $this->view()->load( "recover_invalid_message.html", $data);
                    return;
                }

                // fill answer question
                if ( $to_step == 2 ) {

                    $recover_data     = $Session->val('recover_data');
                    $user_by_email    = $User->searchBy('email', Http::post('email'))->fetch();
                    $user_by_username = $User->searchBy('user_name', Http::post('user_name'))->fetch();
                    $isSameUser       = ($user_by_email['user_id'] == $user_by_username['user_id'] and is_numeric($user_by_email['user_id']));
                    $this->log( "Recover step $to_step for ".Http::post('user_name').'/'.Http::post('email') );

                    if ( $recover_data['step'] == '1' and  $isSameUser ) {
                        $recover_data['user_id'] = $user_by_email['user_id'];
                        $recover_data['step']    = '12';
                        $Session->val('recover_data', $recover_data);

                        $data['token']      = Http::post('token');
                        $data['question']   = $user_by_email['recover_question'];

                        $this->view()->load( "recover_access2.html" , $data);
                        return;
                    } else {
                        unset($recover_data['user_id']);
                        $Session->val('recover_data', $recover_data);
                        $Attempt->increase();
                        $data['type'] = 'invalid';
                        $this->view()->load( "recover_invalid_message.html", $data);
                        return;
                    }
                }

                // fill new password
                if ( $to_step == 3  ) {
                    $recover_data = $Session->val('recover_data');
                    $this->log( "Recover step $to_step for user_id=".$recover_data['user_id'] );
                    if ( $recover_data['step'] == '12' and $User->get($recover_data['user_id']) and $User->field('recover_answer') == trim(sha1(Http::post('answer'))) ) {
                        $recover_data['step'] = '123';
                        $Session->val('recover_data', $recover_data);
                        $data['token'] = Http::post('token');

                        $this->view()->load( "recover_access3.html" , $data);
                        return;
                    } else {
                        unset($recover_data['user_id']);
                        $Session->val('recover_data', $recover_data);
                        $Attempt->increase();
                        $data['type'] = 'invalid';
                        $this->view()->load( "recover_invalid_message.html", $data);
                        return;
                    }
                }

                // change password
                if ( $to_step == 4  ) {
                    $recover_data = $Session->val('recover_data');
                    $this->log( "Recover step $to_step for user_id=".$recover_data['user_id'] );
                    if ( $recover_data['step'] == '123' and $User->changePassword($recover_data['user_id'], Http::post('hpassword')) ) {
                        $Session->val('recover_data', array());
                        $Attempt->reset();
                        $User->get($recover_data['user_id']);
                        $this->sendPasswordChangedMail( $User->toArray() );
                        $this->respOk('ok');
                    } else {
                        $this->respError('Problem changing password, please try again.');
                    }
                }

            } else {
                Liber::redirect($data['url']);
            }
        }

    }




    public function remember_username() {
        $User             = Liber::loadModel('User', 'User', true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;
        $data['descUser'] = $User->toArray('desc');
        $data['show']     = 'form';


        if ( Http::post() ) {
            if ( ($user = $User->searchBy('email', Http::post('email'))->fetch() ) ) {
                $this->log( $user['user_name'] );
                $this->sendUserNameRememberMail($user);
            }
            $data['show']     = 'sent_message';
        }

        $this->view()->load('remember_username.html', $data);
    }



    public function not_requested( $type, $id='' ) {
        switch ($type) {
            case 'password_change':
                echo $type;
            break;

            case 'remember_username':
                echo $type;
            break;

            default:
                # code...
            break;
        }
    }


    /**
     * Send a mail to $user about your password changed.
     * @param  array $user User data
     * @return boolean
     */
    private function sendPasswordChangedMail( $user ) {
        $Mail    = Liber::loadClass('Mailer', true);

        $data['url_base'] = $this->url_base;
        $data['user']     = $user;
        $body             = $this->view()->element('password_changed_mail_tpl.html', $data, true);

        unset( $user['password_hash'], $user['password_salt'], $user['recover_dt'] );
        $Mail->subject("Password changed");
        $Mail->body($body);
        $Mail->to($user['email']);
        $Mail->html(true);
        return $Mail->send();
    }

    /**
     * Send a mail to $user about to remember your username.
     * @param  array $user User data
     * @return boolean
     */
    private function sendUserNameRememberMail( $user ) {
        $Mail    = Liber::loadClass('Mailer', true);

        $data['url_base'] = $this->url_base;
        $data['user']     = $user;
        $body             = $this->view()->element('remember_username_mail_tpl.html', $data, true);

        unset( $user['password_hash'], $user['password_salt'], $user['recover_dt'] );
        $Mail->subject("Remember username");
        $Mail->body($body);
        $Mail->to($user['email']);
        $Mail->html(true);
        return $Mail->send();
    }


}

?>