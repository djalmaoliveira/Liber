<?php

Liber::loadClass('TableModel');

class User extends TableModel {

    function __construct () {
        parent::__construct( Liber::db() );
        $this->table   = 'user';
        $this->idField = 'user_id';

        $this->aFields = Array (
            'user_id'          => Array('', 'User ID', 0),
            'user_name'        => Array('', 'User name', Validation::NOTNULL),
            'password_hash'    => Array('', 'Hashed password', Validation::NOTNULL),
            'password_salt'    => Array('', 'Salted password', Validation::NOTNULL),
            'email'            => Array('', 'E-mail address', Validation::EMAIL),
            'status'           => Array('', 'Status', Validation::NOTNULL),
            'recover_token'    => Array('', 'Recover token', 0),
            'recover_dt'       => Array('', 'Recover Date/Time', 0),
            'recover_question' => Array('', 'Security question', 0),
            'recover_answer'   => Array('', 'Question answer', 0),
            'blocked'          => Array('', 'Account blocked', 0),
            'blocked_dt'       => Array('', 'Account blocked Date/Time', 0),
            'modified'         => Array('', 'Last modified', Validation::NOTNULL),
            'created'          => Array('', 'Created', Validation::NOTNULL)
        );
    }



    /**
     * Return current logged user.
     * @return array
     */
    function loggedUser() {
        $Session = Liber::loadClass('Session',true);
        return $Session->val('logged_user');
    }


    /**
     * Indicates if a login session exists.
     * @return boolean
     */
    function hasLogin() {
        $Session = Liber::loadClass('Session',true);
        return is_array( $Session->val('logged_user') );
    }

    /**
     * Create a valid session to logged user.
     * @param  array $user
     * @return boolean
     */
    function login( $user ) {
        if ( is_array($user) ) {
            $Session    = Liber::loadClass('Session', true);
            unset($user['password_hash']);
            $Session->val('logged_user', $user);
            return true;
        }
        return false;
    }


    /**
     * Logout user.
     * @return void
     */
    function logout() {
        $Session = Liber::loadClass('Session',true);
        $Session->val('logged_user', '');
    }


    /**
     * Indicates if $user_name match with $password specified.
     * @param  String $user_name
     * @param  String $password
     * @return bool
     */
    function authenticate($user_name, $password) {

        $users = $this->searchBy('user_name', $user_name)->fetchAll();
        if ( $users and count($users) == 1  ) {
            $user = current($users);
            if ( $user['status'] != 'A' ) {
                $this->errors('Inactive user.');
                return false;
            }

            $password_hash = $this->passwordHash( $password, $user['password_salt'] ) ;
            if ( $user['password_hash'] == $password_hash ) {
                $this->login( $user );
                return true;
            }
        }

        $this->logout();
        return false;
    }


    /**
     * Calculate a hashed $password using $password_salt specified.
     * @param  string $password
     * @param  string $password_salt
     * @return string
     */
    function passwordHash( $password, $password_salt ) {
        return crypt($password, "$2y$17$".$password_salt);
    }


    /**
     * Return a random salt.
     * @return string
     */
    function  passwordSalt() {
        $salt = substr(base64_encode(openssl_random_pseudo_bytes(17)),0,22);
        $salt = str_replace("+",".",$salt);
        return $salt;
    }


    /**
     * Change a password from $user_id specified.
     * @param  integer $user_id
     * @param  string $password
     * @return boolean
     */
    function changePassword( $user_id, $password ) {
        $User = new User();
        $changed = false;
        if ( $User->get( $user_id ) ) {
            $password_salt = $this->passwordSalt();
            $password_hash = $this->passwordHash( $password, $password_salt );
            $User->field('password_hash', $password_hash);
            $User->field('password_salt', $password_salt);
            $changed = $User->save();
            if ( $changed ) {
                $this->sendPasswordChangedMail( $User->toArray() );
            }
        }

        return $changed;
    }

    /**
     * Send a mail to $user about your password changed.
     * @param  array $user User data
     * @return boolean
     */
    function sendPasswordChangedMail( $user ) {
        $User    = new User;
        $MailTpl = Liber::loadClass('MailTemplate','APP', true);
        $Mail    = Liber::loadClass('Mailer', true);

        // send mail
        unset( $user['password_hash'], $user['password_salt'], $user['recover_dt'] );
        $body = $MailTpl->password_changed( $user );
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
    function sendUserNameRememberMail( $user ) {
        $User    = new User;
        $MailTpl = Liber::loadClass('MailTemplate','APP', true);
        $Mail    = Liber::loadClass('Mailer', true);

        // send mail
        unset( $user['password_hash'], $user['password_salt'], $user['recover_dt'] );
        $body = $MailTpl->remember_username( $user );
        $Mail->subject("Remember username");
        $Mail->body($body);
        $Mail->to($user['email']);
        $Mail->html(true);
        return $Mail->send();
    }



    /**
     * Indicates if a $token of $user_name specified is valid.
     *
     * @param  string  $user_name
     * @param  string  $token
     * @return boolean
     */
    function isValidRecoverToken($user_name, $token) {

        // token should exist
        if ( !($user = $this->searchBy(array('user_name' => $user_name, 'recover_token' => $token))->fetch()) )  {
            return false;
        }

        // should not be expired, token expire in 30 minutes.
        $token_time = strtotime($user['recover_dt']);
        if ( (time()) > ($token_time+(30*60)) ) {
            return false;
        }

        return true;
    }




}

?>