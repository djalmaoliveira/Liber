<?php

Liber::loadController('BaseController');

/**
 * MainController
 *
 */
class MainController extends BaseController{

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper(array('Url', 'HTML'));
        $this->view()->template('default.html');
    }

    // after deploy setup application
    public function setup() {
        $oSetup = Liber::loadClass('Setup', true);

        if ( !file_exists(Liber::conf('APP_ROOT').Liber::conf('ASSETS_DIR').'app') ) {
            $oSetup->publishAsset();
        }
    }

    public function index(){
        $this->login();
    }


    public function login() {

        if ( Http::post() ) {
            $this->respError('ok');
        } else {
            $this->view()->load( "admin/login.html" );
        }
    }


    public function recover_access( $to_step = 1 ) {
        list($Sec, $Session) = Liber::loadClass(array('Security', 'Session'), true);
        Liber::loadClass('Attempt');
        $Attempt          = new Attempt('recover_access');
        $User             = Liber::loadModel('User', true);
        $data['url']      = Liber::redirect('/'.__FUNCTION__, true);
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
            $this->view()->load( "admin/recover_invalid_message.html" , $data);
            return;
        }

        // fill credentials
        if ( $to_step == 1  ) {
            $Sec->clientWatch();
            $data['token']        = $Sec->token(true);
            $recover_data['step'] = '1';
            $Session->val('recover_data', $recover_data);

            $this->view()->load( "admin/recover_access1.html" , $data);
        } else {
            if ( Http::post() ) {
                if ( !$Sec->validToken( Http::post('token') ) or $Sec->clientChanged() ) {
                    $Attempt->increase();
                    $this->view()->load( "admin/recover_invalid_message.html", array('type' => 'invalid'));
                    return;
                }

                // fill answer question
                if ( $to_step == 2 ) {

                    $recover_data     = $Session->val('recover_data');
                    $user_by_email    = $User->searchBy('email', Http::post('email'))->fetch();
                    $user_by_username = $User->searchBy('user_name', Http::post('user_name'))->fetch();
                    $isSameUser       = ($user_by_email['user_id'] == $user_by_username['user_id'] and is_numeric($user_by_email['user_id']));

                    if ( $recover_data['step'] == '1' and  $isSameUser ) {
                        $recover_data['user_id'] = $user_by_email['user_id'];
                        $recover_data['step']    = '12';
                        $Session->val('recover_data', $recover_data);

                        $data['token']      = Http::post('token');
                        $data['question']   = $user_by_email['recover_question'];
                        $this->view()->load( "admin/recover_access2.html" , $data);
                        return;
                    } else {
                        unset($recover_data['user_id']);
                        $Session->val('recover_data', $recover_data);
                        $Attempt->increase();
                        $this->view()->load( "admin/recover_invalid_message.html", array('type' => 'invalid'));
                        return;
                    }
                }

                // fill new password
                if ( $to_step == 3  ) {
                    $recover_data = $Session->val('recover_data');
                    if ( $recover_data['step'] == '12' and $User->get($recover_data['user_id']) and $User->field('recover_answer') == trim(sha1(Http::post('answer'))) ) {
                        $recover_data['step'] = '123';
                        $Session->val('recover_data', $recover_data);
                        $data['token'] = Http::post('token');
                        $this->view()->load( "admin/recover_access3.html" , $data);
                        return;
                    } else {
                        unset($recover_data['user_id']);
                        $Attempt->increase();
                        $Session->val('recover_data', $recover_data);

                        $User->updateRecoverAttempt( $recover_data['user_id'] );
                        $this->view()->load( "admin/recover_invalid_message.html", array('type' => 'invalid'));
                        return;
                    }
                }

                // change password
                if ( $to_step == 4  ) {
                    $recover_data = $Session->val('recover_data');
                    if ( $recover_data['step'] == '123' and $User->changePassword($recover_data['user_id'], Http::post('password')) ) {
                        $User->resetRecoverAttempt( $recover_data['user_id'] );
                        $Session->val('recover_data', array());
                        $Attempt->reset();
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
        $User             = Liber::loadModel('User', true);
        $data['url']      = Liber::redirect('/'.__FUNCTION__, true);
        $data['descUser'] = $User->toArray('desc');
        $data['show']     = 'form';

        if ( Http::post() ) {
            if ( ($user = $User->searchBy('email', Http::post('email'))->fetch() ) ) {
                $User->sendUserNameRememberMail($user);
            }
            $data['show']     = 'sent_message';
        }

        $this->view()->load('admin/remember_username.html', $data);
    }


    public function not_requested( $type, $id='' ) {
        switch ($type) {
            case 'password_change':
                # code...
            break;

            case 'remember_username':
                # code...
            break;

            default:
                # code...
                break;
        }
    }




}

?>