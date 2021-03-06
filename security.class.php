<?php


class filter_payload {

    /**
     * Clean all request payload
     * @param bool|false $init_val
     * @return array
     */
    public function clean_request_payload($init_val = false) {
        # Check if payload is available or load from exiting request
        $payload_val = is_array($init_val) ? $init_val : array($_GET, $_REQUEST, $_POST);

        #validate value
        if(is_array($payload_val)) {

            # loop payload
            foreach($payload_val as $key=>$row) {

                # If payload is array then call same method recursively or clean the value
                if(is_array($row))
                    $payload_val[$key] = $this->clean_request_payload($row);
                else
                    $payload_val[$key] = $this->clean_value($row);
            }
        }

        # If payload from request all request variable then update all value after cleaning it back
        if(!is_array($init_val))
            list($_GET, $_REQUEST, $_POST) = $payload_val;

        # Return all value after cleaning
        return $payload_val;
    }


    /*
     * XSS filter
     *
     * This was built from numerous sources
     * (thanks all, sorry I didn't track to credit you)
     *
     * It was tested against *most* exploits here: http://ha.ckers.org/xss.html
     * WARNING: Some weren't tested!!!
     * Those include the Actionscript and SSI samples, or any newer than Jan 2011
     *
     *
     * TO-DO: compare to SymphonyCMS filter:
     * https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php
     * (Symphony's is probably faster than my hack)
     */
    public function clean_value($data) {

        # Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        # Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        # Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        # Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        # Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            # Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        # we are done...
        return $data;
    }

}

?>