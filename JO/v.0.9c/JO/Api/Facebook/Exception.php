<?php

class JO_Api_Facebook_Exception extends JO_Exception {

	/**
     * The result from the API server that represents the exception information.
     */
    protected $result;

    /**
     * Make a new API Exception with the given result.
     *
     * @param Array $result the result from the API server
     */
    public function __construct($result = '') {
        if (is_object($result)) {
            $result = $this->toArray($result);
        }
        $this->result = $result;

        $code = isset($result['error_code']) ? $result['error_code'] : 0;

        if (isset($result['error_description'])) {
// OAuth 2.0 Draft 10 style
            $msg = $result['error_description'];
        } else if (isset($result['error']) && is_array($result['error'])) {
// OAuth 2.0 Draft 00 style
            $msg = $result['error']['message'];
        } else if (isset($result['error_msg'])) {
// Rest server style
            $msg = $result['error_msg'];
        } elseif (is_array($result)) {
            $msg = 'Unknown Error. Check getResult()';
        } else {
            $msg = $result;
        }

        parent::__construct($msg, $code);
    }

    /**
     * Return the associated result object returned by the API server.
     *
     * @returns Array the result from the API server
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Returns the associated type for the error. This will default to
     * 'Exception' when a type is not available.
     *
     * @return String
     */
    public function getType() {
        if (isset($this->result['error'])) {
            $error = $this->result['error'];
            if (is_string($error)) {
                // OAuth 2.0 Draft 10 style
                return $error;
            } else if (is_array($error)) {
                // OAuth 2.0 Draft 00 style
                if (isset($error['type'])) {
                    return $error['type'];
                }
            }
        }
        return 'Exception';
    }

    /**
     * To make debugging easier.
     *
     * @returns String the string representation of the error
     */
    public function __toString() {
        $str = $this->getType() . ': ';
        if ($this->code != 0) {
            $str .= $this->code . ': ';
        }
        return $str . $this->message;
    }

    function toArray($str) {
        $ob = array();
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $ob[$k] = $this->toArray($v);
            }
            return $ob;
        } else {
            return $str;
        }
    }
	
}

?>