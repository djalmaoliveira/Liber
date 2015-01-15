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
            'password_hash'    => Array('', 'Password', Validation::NOTNULL),
            'email'            => Array('', 'E-mail address', Validation::EMAIL),
            'status'           => Array('', 'Status', Validation::NOTNULL),
            'reset_pass_token' => Array('', 'Reset password token', 0),
            'reset_pass_dt'    => Array('', 'Reset password Date/Time', 0),
            'recover_question' => Array('', 'Security question', 0),
            'recover_answer_hash'   => Array('', 'Question answer', 0),
            'blocked'          => Array('', 'Account blocked', 0),
            'blocked_dt'       => Array('', 'Account blocked Date/Time', 0),
            'modified'         => Array('', 'Last modified', Validation::NOTNULL),
            'created'          => Array('', 'Created', Validation::NOTNULL)
        );
    }


    function beforeSave() {
        if ( !$this->field($this->idField) )  {
            $this->field('created', date('Y-m-d H:i:s'));
        }
        $this->field('modified', date('Y-m-d H:i:s'));
    }

    protected function _compat_password() {
        require Liber::conf('APP_PATH').'../vendor/ircmaxell/password-compat/lib/password.php';
    }


    /**
     * Return current logged user.
     * @return array
     */
    function loggedUser() {
        Liber::loadClass('Session');
        $Session = new Session('user-login');
        return $Session->val('user');
    }


    /**
     * Indicates if a login session exists.
     * Also verify if IP and AGENT is the same.
     * @return boolean
     */
    function hasLogin() {
        Liber::loadClass( array('Session', 'Security') );
        $Sec             = new Security;
        $Session         = new Session('user-login');
        $isUserLogged    = is_array( $Session->val('user') );
        $isClientChanged = $Sec->clientChanged();
        if ( $isClientChanged ) { Liber::log()->add( 'Logged client changed.'.print_r($isSameMachine, true) ); }
        return ($isUserLogged and !$isClientChanged);
    }


    /**
     * Create a valid session to logged user.
     * @param  array $user
     * @return boolean
     */
    function login( $user ) {
        if ( is_array($user) ) {
            Liber::loadClass( array('Session', 'Security') );
            $Sec     = new Security;
            $Session = new Session('user-login');
            unset($user['password_hash'], $user['recover_answer_hash']);
            $Session->val('user', $user);
            Liber::log()->add( 'Monitoring login. '.print_r($Sec->clientWatch(), true) );
            return true;
        }
        return false;
    }


    /**
     * Logout user.
     * @return void
     */
    function logout() {
        Liber::loadClass('Session');
        $Session = new Session('user-login');
        $Session->clear();
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

            $this->_compat_password();
            if ( password_verify($password, $user['password_hash']) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Indicates if $user_id match with $answer specified.
     * @param  String $user_name
     * @param  String $password
     * @return bool
     */
    function isAnswer($user_id, $answer) {

        if ( $user = $this->searchBy('user_id', $user_id)->fetch() ) {
            $this->_compat_password();
            if ( password_verify($answer, $user['recover_answer_hash']) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Return the string of hash algorithm used in $password_hash specified.
     * @param  String $password_hash
     * @return String
     */
    function hashAlgo( $password_hash ) {
        $count = 0;

        foreach ( str_split($password_hash) as $key => $value ) {
            if ( $value == '$' ) { $count++; }
            if ( $count == 3 ) { break; }
        }

        return substr($password_hash, 0, $key+1);
    }


    /**
     * Calculate a hashed $password using $password_salt specified.
     * @param  string $password
     * @param  string $password_salt
     * @return string
     */
    function passwordHash( $password, $password_salt ) {
        return crypt($password, '$2y$14$'.$password_salt);
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
     * Change the $question and $answer of $user_id specified.
     * @param  integer $user_id
     * @param  string $question
     * @param  string $answer
     * @return boolean
     */
    function changeQuestion( $user_id, $question, $answer ) {
        $User = new User();

        if ( $User->get( $user_id ) ) {
            $hash = $this->passwordHash( $answer, $this->passwordSalt() );
            $User->field('recover_question', $question);
            $User->field('recover_answer_hash', $hash);

            return $User->save();
        }

        return false;
    }


    /**
     * Change a password from $user_id specified.
     * @param  integer $user_id
     * @param  string $password
     * @return boolean
     */
    function changePassword( $user_id, $password ) {
        $User = new User();

        if ( $User->get( $user_id ) ) {
            $password_salt = $this->passwordSalt();
            $password_hash = $this->passwordHash( $password, $password_salt );
            $User->field('password_hash', $password_hash);

            // set reset pass token as not used
            $User->field('reset_pass_token', null);
            $User->field('reset_pass_dt', null);

            return $User->save();
        }

        return false;
    }


    /**
     * Update a reset password token of $user specified.
     * If $clear is true, then the token will be set to null.
     * @param  array  $user
     * @param  boolean $clear
     * @return string
     */
    function resetPasswordToken( $user, $clear=false ) {
        $User = new User;
        if ( $User->get($user['user_id']) ) {
            if ( !$clear ) {
                $User->field('reset_pass_token', sha1( $user['user_id'].microtime(true) ));
                $User->field('reset_pass_dt', date('Y-m-d H:i:s'));
            } else {
                $User->field('reset_pass_token', null);
                $User->field('reset_pass_dt', null);
            }
            $User->save();
        }

        return $User->field('reset_pass_token');
    }


    /**
     * Indicates if a $token specified is valid.
     *
     * @param  string  $user_name
     * @param  string  $token
     * @return boolean
     */
    function isValidResetPasswordToken($token) {

        // token should exist
        if ( !($user = $this->searchBy('reset_pass_token', $token)->fetch()) )  {
            return false;
        }

        // should not be expired, token expire in 30 minutes.
        $token_time = strtotime($user['reset_pass_dt']);
        if ( (time()) > ($token_time+(30*60)) ) {
            return false;
        }

        return true;
    }




}

?>