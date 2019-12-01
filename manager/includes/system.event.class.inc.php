<?php
// SystemEvent Class
class SystemEvent {
    public $name;
    public $_propagate;
    public $_output;
    public $_globalVariables;
    public $activated;
    public $activePlugin;
    public $params = array();
    public $vars = array();
    public $cm = null;

    function __construct($name= '') {
        $this->_resetEventObject();
        $this->name= $name;
        $this->activePlugin = '';
    }

    // used for displaying a message to the user
    function alert($msg) {
        if ($msg == '' || !is_array($this->SystemAlertMsgQueque)) {
            return;
        }
        $alert = array();
        if ($this->name && $this->activePlugin) {
            $alert[] = sprintf(
                '<div><b>%s</b> - <span style="color:maroon;">%s</span></div>'
                , $this->activePlugin
                , $this->name
            );
        }
        $alert[] = sprintf('<div style="margin-left:10px;margin-top:3px;">%s</div>', $msg);
        $this->SystemAlertMsgQueque[] = implode('', $alert);
    }

    // used for rendering an out on the screen
    function output($msg) {
        if( is_object($this->cm) ) {
            $this->cm->addOutput($msg);
        }
    }

    // get global variables
    function getGlobalVariable($key) {
        if( isset( $GLOBALS[$key] ) )
        {
            return $GLOBALS[$key];
        }
        return false;
    }

    // set global variables
    function setGlobalVariable($key,$val,$now=0) {
        if (! isset( $GLOBALS[$key] ) ) { return false; }
        if ( $now === 1 || $now === 'now' )
        {
            $GLOBALS[$key] = $val;
        }
        else
        {
            $this->_globalVariables[$key]=$val;
        }
        return true;
    }

    // set all global variables
    function setAllGlobalVariables() {
        if ( empty( $this->_globalVariables ) ) {
            return false;
        }
        foreach ( $this->_globalVariables as $key => $val )
        {
            $GLOBALS[$key] = $val;
        }
        return true;
    }

    function stopPropagation() {
        $this->_propagate= false;
    }

    function _resetEventObject() {
        unset ($this->returnedValues);
        $this->_output= '';
        $this->_globalVariables=array();
        $this->_propagate= true;
        $this->activated= false;
    }

    public function getParam($key, $default=null) {
        if (!isset($this->params[$key])) {
            return $default;
        }
        if(strtolower($this->params[$key]) === 'false') {
            $this->params[$key] = false;
        }
        return $this->params[$key];
    }

    function params($key, $default=null) {
        return array_get($this->params, $key, $default);
    }
}