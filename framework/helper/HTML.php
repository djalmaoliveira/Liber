<?php
/**
 *  Html helpers.
 *
 * @package     helpers_html
 * @author		djalmaoliveira@gmail.com
 * @copyright	djalmaoliveira@gmail.com
 * @license     license.txt
 * @link
 * @since		Version 1.0
 */

/**
*   Set or print Html tags for external CSS and JS files included in 'head' tag.
*   <code>
*   Usage:
*       // external assets
*       html_header_('css', 'http://abc.com/somefile.css');
*       // absolute path from root path (i.e. www.domain.com/mycss/my.css)
*       html_header_('css', '/mycss/my.css');
*       // relative to default /css folder (i.e. www.domain.com/css/my.css)
*       html_header_('css', 'my.css');
*       // return only css
*       html_header_('css');
*       // print tags to use among html head tags.
*       html_header_();
*   </code>
*   @param String $type - 'css' or 'js'
*   @param String $url - Relative url to assets folder or external url starting with http:// or https://.
*   @return String - Html tags
*/
function html_header_($type=null, $url=null) {
    static $headers = Array('css'=>Array(), 'js'=>Array());
    static $f;
    if ( !$f ) {
        $f = create_function('$type, $urls', '
            $tag = "";
            foreach ($urls as $url) {
                if ( $url[0] == "/" ) {
                    $url = url_asset_($url, true);
                } elseif( (strpos($url, "://") === false ) ) {
                    $url = url_asset_("/$type/", true).$url;
                }
                if ( trim($type) == "css" ) {
                    $tag .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\"  href=\"$url\" />\r\n";
                } else {
                    $tag .= "<script src=\"$url\" type=\"text/javascript\"></script>\r\n";
                }
            }
            return $tag;
        ');
    }

    $nargs = func_num_args();
    if ( $nargs == 0 ) {
        echo $f('css', $headers['css']);
        echo $f('js', $headers['js']);
    } elseif ( $nargs == 1 ) {
        echo $f($type, $headers[$type]);
    } else {
        $headers[$type][] = $url;
    }

}

/**
*   Set or print html meta tags with some data, that should be included in 'head' tag.
*   <code>
*   Usage:
*       // set a meta author content, used inside template files
*       html_meta_( Array('name'=>'author', 'content'=>'Liber framework') );
*       // print tags to use among html head tags.
*       html_meta_();
*   </code>
*   @param Array $aData
*   @return String - Html meta tags
*/
function html_meta_( $aData=null ) {
    static $metas = Array();
    $tag = '';
    if ( is_array($aData) ) {
        foreach( $aData as $attr => $value) {
            $tag .= $attr.'="'.$value.'" ';
        }
        $metas[] = "<meta $tag />";
    } else {
        echo implode("\r\n", $metas);
    }
}


/**
*   Set or get the text to html title tag.
*   <code>
*   Usage:
*       // set title of page, used inside template files
*       html_title_( 'title of page' );
*       // get title
*       html_title_();
*   </code>
*   @param String $title
*   @return String
*/
function html_title_( $title=null ) {
    static $_title = '';

    if ( !empty($title) ) {
        $_title = $title;
    } else {
        return $_title;
    }
}

/**
*   Set or return script String to use on head tags.
*   @param String $script
*   @return String
*/
function html_script_($script=null) {
    static $_scripts = Array();

    if ( $script ) {
        $_scripts[] = $script;
    } else {
        return implode("\n", $_scripts);
    }
}
?>