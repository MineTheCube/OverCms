<?php

Class EventManager {

    private static $events;

    public function __construct() {
    }

    public static function listen($pluginName, $plugin, $event) {
        self::$events[$event][$pluginName] = $plugin;
    }

    public static function fire($eventName, Event &$event = null) {

        if (!isset(self::$events[$eventName]) or !is_array(self::$events[$eventName]))
            return true;
        if ($event === null)
            $event = new Event;
        foreach (self::$events[$eventName] as $pluginName => $plugin) {
            if (file_exists($plugin['path'].'events/'.$eventName.EX)) {
                require $plugin['path'].'events/'.$eventName.EX;
                if ($event->isCancelled())
                    return false;
            }
        }
        return true;
    }

}



Class Event {

    private $data = array();
    private $isCancelled = false;
    private $cancellable = true;
    private $cancelReason;

    public function __construct(array $data = array()) {
        foreach ($data as $key => $value)
            $this->set($key, $value);
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function data($key = null, $value = null) {
        if ($key === null)
            return $this->data;
        else if ($value === null)
            return $this->get($key);
        else
            $this->set($key, $value);
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
    }

    public function get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function cancelEvent($reason = null) {
        if (!$this->cancellable)
            return false;

        $this->isCancelled = true;
        $this->cancelReason = $reason;
        return true;
    }

    public function isCancelled() {
        return (bool) $this->isCancelled;
    }

    public function cancelReason() {
        return $this->cancelReason;
    }

    public function setCancellable($isCancellable) {
        $this->cancellable = (bool) $isCancellable;
    }

    public function isCancellable() {
        return (bool) $this->cancellable;
    }

    public function dump() {
        dump($this->data, 'Cancellable: '.($this->isCancellable() ? 'True':'False'), 'Cancelled: '.($this->isCancelled ? 'True':'False'), 'Cancel Reason: '.($this->cancelReason()));
    }

}

