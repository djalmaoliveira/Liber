<?php

Liber::loadController('BaseController');

/**
 * UserController
 *
 */
class UserController extends BaseController {

    var $url_base;
    var $panel_template;

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper(array('Url', 'HTML'));
        $this->view()->template('default.html');
        $this->url_base = url_to_('/user', true).'/';
        $this->panel_template = Liber::conf('APP_PATH').'template/panel.html';

    }

    public function index() {

    }

    public function logout() {
        $User = Liber::loadModel('User', 'User' ,  true);
        $User->logout();
        Liber::redirect($this->url_base.'login');
    }

    public function login() {
        list($Attempt) = Liber::loadClass(array('Attempt'));
        Liber::loadHelper('DT');
        Liber::loadClass(array('Session'));

        $Sec              = new Security;
        $Attempt          = new Attempt('login-attempt');
        $User             = Liber::loadModel('User', 'User' , true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;

        if ( Http::post() ) {
            $PreLogin = new Session('pre-login');


                // first post
                if ( Http::post('username') ) {

                    $PreLogin->val('username', Http::post('username'));
                    $this->respOk('ok', array('token' => $Sec->token(true) ));
                    return;

                // second post
                } elseif ( Http::post('hpassword') ) {
                    $this->checkToken();

                    $hasError = false;
                    $Attempt->watch(3);
                    $Sec->startDelay(2); // avoid timing attack

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

            $User->logout();
            $data['wait_time']      = dt_timesince_(time()-$Attempt->waitTime());
            $data['isLoginBlocked'] = $Attempt->isBlocked();

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
            $this->view()->load( "recover_message.html" , $data);
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
                $this->checkToken();
                if ( $Sec->clientChanged() ) {
                    $Attempt->increase();
                    $data['type'] = 'invalid';
                    $this->view()->load( "recover_message.html", $data);
                    return;
                }

                // fill answer question
                if ( $to_step == 2 ) {
                    $Sec->startDelay(1);
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

                    } else {
                        unset($recover_data['user_id']);
                        $Session->val('recover_data', $recover_data);
                        $Attempt->increase();
                        $data['type'] = 'invalid';
                        $this->view()->load( "recover_message.html", $data);

                    }

                    $Sec->stopDelay();
                    return;
                }

                // fill new password
                if ( $to_step == 3  ) {
                    $recover_data = $Session->val('recover_data');
                    $this->log( "Recover step $to_step for user_id=".$recover_data['user_id'] );
                    if ( $recover_data['step'] == '12' and $User->isAnswer($recover_data['user_id'], Http::post('answer') ) ) {
                        $recover_data['step'] = '123';
                        $Session->val('recover_data', $recover_data);
                        $data['token'] = Http::post('token');
                        $data['url']   = $data['url'].'/4';
                        $this->view()->load( "recover_access3.html" , $data);

                    } else {
                        unset($recover_data['user_id']);
                        $Session->val('recover_data', $recover_data);
                        $Attempt->increase();
                        $data['type'] = 'invalid';
                        $this->view()->load( "recover_message.html", $data);

                    }

                    return;
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

                    return;
                }

            } else {
                Liber::redirect($data['url']);
            }
        }

    }


    public function reset_password( $reset_token='' ) {
        $Sec              = Liber::loadClass('Security', true);
        $User             = Liber::loadModel('User', 'User', true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;
        $data['descUser'] = $User->toArray('desc');


        if ( Http::post() ) {
            $this->checkToken();

            // send request mail
            $Sec->startDelay(1);
            if ( Http::post('email') ) {
                $this->log( "Request mail => ".Http::post('email') );
                if ( ($user = $User->searchBy('email', Http::post('email'))->fetch()) ) {
                    $this->sendPasswordResetMail($user);
                    $Sec->token(true);
                }

                $data['show'] = 'sent_reset';
                $this->view()->load('reset_password.html', $data);
            }
            $Sec->stopDelay();

            // reset password
            if ( Http::post('hpassword') ) {
                Liber::loadClass('Session');
                $Session = new Session('reset-password');
                $this->log( "Reset password user_id => ".$Session->val('user_id') );
                if ( $User->changePassword($Session->val('user_id'), Http::post('hpassword')) ) {
                    $User->get($Session->val('user_id'));
                    $this->sendPasswordChangedMail( $User->toArray() );
                    $Session->clear();

                    $this->respOk('ok');
                } else {
                    $this->respError('Problem changing password, please try again.');
                }
            }

        } else {

            // show password form
            if ( $reset_token ) {

                $Sec->startDelay(1);
                if ( $User->isValidResetPasswordToken($reset_token) ) {
                    Liber::loadClass('Session');
                    $Session = new Session('reset-password');
                    $user = $User->searchBy('reset_pass_token', $reset_token)->fetch();
                    $Session->val('user_id', $user['user_id']);

                    $data['token'] = $Sec->token(true);
                    $this->view()->load('recover_access3.html', $data);

                    $this->log( "Show password form for user_id => ".$user['user_id'] );
                } else {
                    $data['type'] = 'reset';
                    $this->view()->load( "recover_message.html", $data);
                }
                $Sec->stopDelay();

            // show request form
            } else {
                $data['token'] = $Sec->token(true);
                $data['show']  = 'form';
                $this->view()->load('reset_password.html', $data);
            }

        }


    }



    public function remember_username() {
        $Sec              = Liber::loadClass('Security', true);
        $User             = Liber::loadModel('User', 'User', true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;
        $data['descUser'] = $User->toArray('desc');
        $data['show']     = 'form';


        if ( Http::post() ) {
            $this->checkToken();
            if ( ($user = $User->searchBy('email', Http::post('email'))->fetch() ) ) {
                $this->log( Http::post('email') );
                $this->sendUserNameRememberMail($user);
            }
            $data['show'] = 'sent_message';
        }

        $data['token'] = $Sec->token(true);
        $this->view()->load('remember_username.html', $data);
    }



    public function not_requested( $type, $id='' ) {
        $User             = Liber::loadModel('User', 'User', true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;

        switch ($type) {
            case 'password_reset':

                $user = $User->searchBy('reset_pass_token', $id)->fetch();
                $User->resetPasswordToken( $user, true );
                $this->log( 'user_id:'.$user['user_id']. ",  token:$id" );
            break;

            case 'password_change':
                $this->log( "Track:$id" );
            break;

            case 'remember_username':
                $this->log( "Track:$id" );
            break;
        }

        $data['type'] = 'not_requested';
        $this->view()->load( "recover_message.html", $data);
    }


    // manage profile frontend
    //
    public function profile( $user_id=null ) {
        $this->isUserLogged();
        $Sec                       = new Security;
        $User                      = Liber::loadModel('User', 'User', true);
        $data['url']               = $this->url_base.__FUNCTION__;
        $data['url_base']          = $this->url_base;
        $data['descUser']          = $User->toArray('desc');
        $data['recover_questions'] = array('What framework ?', 'Other question ?');
        $errors                    = array();



        // request processing
        if ( Http::post() ) {
            $this->checkToken();

            if ( $User->authenticate($this->user['user_name'], Http::post('hcpassword')) ) {

                // user exists or is a new
                if ( Http::post('user_id')  ) {
                    $User->get( Http::post('user_id') );
                }

                $User->field('email', Http::post('email'));
                $User->field('user_name', Http::post('username') );

                if ( $User->save() ) {
                    // new password
                    if ( Http::post('hnpassword') ) {
                        $User->changePassword( $User->field('user_id'), Http::post('hnpassword') );
                        $this->sendPasswordChangedMail( $User->toArray() );
                    }

                    // recover question
                    if ( Http::post('hrecover_answer') ) {
                        $User->changeQuestion( $User->field('user_id'), Http::post('recover_question'), Http::post('hrecover_answer') );
                    }

                    unset($_POST['recover_question'], $_POST['hrecover_answer'], $_POST['hnpassword'], $_POST['hcpassword']);
                    $this->log( "saved. ".print_r(array_filter($_POST), true) );
                    $this->respOk('Profile saved.', array('user_id' => $User->field('user_id'), 'token' => $Sec->token(true) ));
                    return;
                }

                $errors = $User->errors();

            }

            $this->log( print_r($errors, true) );
            $this->respError('Problem saving data.', $errors);

        // show form
        } else {


            if ( $user_id == 'logged' ) { $user_id = $this->user['user_id']; }
            $User->get( $user_id );
            $data['token'] = $Sec->token(true);
            $data['user']  = $User->toArray();
            unset($data['user']['password_hash'], $data['user']['recover_answer_hash']);

            $this->view()->loadWithTemplate($this->panel_template, 'profile.html', $data);
        }
    }




    function browser() {
        $this->isUserLogged();

        Liber::loadHelper('Html', 'APP');
        $Sec                = new Security;
        $User               = Liber::loadModel('User', 'User', true);
        $data['url']        = $this->url_base.__FUNCTION__;
        $data['url_base']   = $this->url_base;
        $data['url_remove'] = $this->url_base.'remove';
        $data['descUser']   = $User->toArray('desc');
        $data['token']      = $Sec->token(true);

        $query = Http::get('search')?Http::get('search'):page_options_('query');
        $data['users'] = $User->paginate($query , array(
                                                    'fields' => array('user_name', 'email')
                                                  )
                                        );


        $this->view()->loadWithTemplate($this->panel_template, 'browser.html', $data);

    }


    /**
     * Remove user.
     * @return
     */
    function remove() {
        $this->isUserLogged();
        $User             = Liber::loadModel('User', 'User', true);
        $data['url']      = $this->url_base.__FUNCTION__;
        $data['url_base'] = $this->url_base;

        // request processing
        $this->log( 'user_id:'.Http::post('id') );
        if ( Http::post() ) {
            $this->checkToken();
            if ( $User->delete( Http::post('id') ) ) {
                $Sec = new Security;
                $this->respOk("User removed.", array('token' => $Sec->token(true) ) );
                return;
            }
        }

        $this->respError('Problem removing user.', $User->errors());
    }






    /**
     * Send a mail to $user about reset your password.
     * @param  array $user User data
     * @return boolean
     */
    private function sendPasswordResetMail( $user ) {
        $User = Liber::loadModel('User', 'User', true);
        $Mail = Liber::loadClass('Mailer', true);

        if ( ($data['reset_token'] = $User->resetPasswordToken($user)) ) {

            $data['url_base'] = $this->url_base;
            $data['url']      = $this->url_base.'reset_password/';
            $data['user']     = $user;

            $body = $this->view()->loadWithTemplate('mail.html', 'password_reset_mail_tpl.html', $data, true);

            unset( $user['password_hash'], $user['recover_dt'] );
            $Mail->subject("Password reset");
            $Mail->body($body);
            $Mail->to($user['email']);
            $Mail->html(true);
            return $Mail->send();
        }
    }


    /**
     * Send a mail to $user about your password changed.
     * @param  array $user User data
     * @return boolean
     */
    private function sendPasswordChangedMail( $user ) {
        $Mail = Liber::loadClass('Mailer', true);

        $data['url_base'] = $this->url_base;
        $data['user']     = $user;
        $data['token']    = microtime(true);
        $this->log( "Password change, Track:{$data['token']}, IP[{$_SERVER['REMOTE_ADDR']}], AGENT[{$_SERVER['HTTP_USER_AGENT']}]" );

        $body = $this->view()->loadWithTemplate('mail.html', 'password_changed_mail_tpl.html', $data, true);

        unset( $user['password_hash'],  $user['recover_dt'] );
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
        $Mail = Liber::loadClass('Mailer', true);

        $data['url_base'] = $this->url_base;
        $data['user']     = $user;
        $data['token']    = microtime(true);
        $this->log( "Remember user name, Track:{$data['token']}, IP[{$_SERVER['REMOTE_ADDR']}], AGENT[{$_SERVER['HTTP_USER_AGENT']}]" );

        $body = $this->view()->loadWithTemplate('mail.html', 'remember_username_mail_tpl.html', $data, true);

        unset( $user['password_hash'], $user['recover_dt'] );
        $Mail->subject("Remember username");
        $Mail->body($body);
        $Mail->to($user['email']);
        $Mail->html(true);
        return $Mail->send();
    }


}

?>