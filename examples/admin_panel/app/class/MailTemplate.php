<?php

/**
 * Generate mail templates from app.
 */
class MailTemplate {



    /**
     * Mail with username.
     * @param  array $user User data
     * @return String
     */
    function remember_username( $user ) {
        $View = Liber::loadClass('View', true);
        $tpl = $View->element('admin/remember_username_mail_tpl.html', array('user' => $user), true);
        return $tpl;
    }

    /**
     * Mail indicating that password was changed.
     * @param  array $user User data
     * @return String
     */
    function password_changed( $user ) {
        $View = Liber::loadClass('View', true);
        $tpl = $View->element('admin/password_changed_mail_tpl.html', array('user' => $user), true);
        return $tpl;
    }
}

?>