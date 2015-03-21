<?php

Class Parser {

    private $html;
    private $blocks = array();
    private $htmlBlocks = array();
    private $childs = array();
    private $childsList = array();
    private $data = array();
    private $binds = array();
    private $get = null;

    public function __construct() {}

    public function loadFile($file, $blocks = array()) {
        if (is_file($file))
            $html = strip_comments(@file_get_contents($file));
        else
            return false;
        $this->init($html, $blocks);
    }

    public function loadString($string, $blocks = array()) {
        $html = strip_comments($string);
        $this->init($html, $blocks);
    }

    public function __get($key) {
        $this->get = '%'.str_replace(' ', '_', strtoupper($key)).'%';
        return $this;
    }

    public function getHtml() {
        $key = $this->get;
        if (!$key)
            return false;
        return $this->htmlBlocks[$key];        
    }

    public function replace($html) {
        $key = $this->get;
        if (!$key)
            return false;
        $this->data[$key] = $html;        
    }

    public function add($vars = array()) {
        $key = $this->get;
        if (!$key or !is_array($vars))
            return false;
        foreach ($vars as $k => $v)
            $add['%'.str_replace(' ', '_', strtoupper($k)).'%'] = $v;
        if (!empty($vars))
            $this->data[$key][] = $add;
        else
            $this->data[$key][] = array();
    }

    public function parse($vars = array()) {
        $key = $this->get;
        if (!$key or !is_array($vars))
            return false;
        foreach ($vars as $k => $v)
            $add['%'.str_replace(' ', '_', strtoupper($k)).'%'] = $v;
        if (!empty($vars))
            $this->data[$key] = array($add);
        else
            $this->data[$key] = array();
    }

    public function parseIf($test, $vars = array()) {
        if ($test)
            $this->parse($vars);
    }

    public function addIf($test, $vars = array()) {
        if ($test)
            $this->add($vars);
        else
            $this->remove();
    }

    public function bind($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->bind($k, $v);
            }
            return;
        }
        $this->binds['%'.str_replace(' ', '_', strtoupper($key)).'%'] = $value;
    }

    public function remove() {
        $key = $this->get;
        if (!$key)
            return false;
        if (!$this->isChild())
            return;
        $this->data[$key][] = false;
    }

    public function render() {
        // dump($this->data);
        $html = $this->html;
        foreach ($this->blockOrder as $key => $unused) {
            $i++;
            $add = $this->htmlBlocks[$key];
            $childrens = $this->childs[$key];
            $render = '';
            if (is_array($this->data[$key]) and !empty($this->data[$key])) {
                foreach ($this->data[$key] as $k => $v) {
                    if (is_array($v) and !empty($v)) {
                        $render .= str_replace_array($add, $v);
                    } else {
                        $render .= $add;
                    }
                    if (is_array($childrens) and !empty($childrens)) {
                        foreach ($childrens as $child_key) {

                            if (!empty($this->data[$child_key])) {
                                $child_v = array_shift($this->data[$child_key]);
                                if ($child_v === false) {
                                    $child_render = '';
                                } else {
                                    $child_render = $this->htmlBlocks[$child_key];
                                    $child_render = str_replace_array($child_render, $child_v);
                                }
                            } else {
                                $child_render = $this->htmlBlocks[$child_key];
                            }
                            $render = str_replace($child_key, $child_render, $render);
                        }
                    }

                }
            } else if (is_array($this->data[$key]) and empty($this->data[$key])) {
                $render = $add;
            } else if (is_string($this->data[$key]))
                $render = $this->data[$key];

            $html = str_replace($key, $render, $html);
        }
        return str_replace_array($html, $this->binds);
    }

    private function parseBlocks($html, $rawBlocks) {
        $htmlBlocks = array();
        $blockOrder = array();
        $blocks = array();
        $childs = array();
        $childsList = array();

        foreach ($rawBlocks as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $newKey = '%'.str_replace(' ', '_', strtoupper($v)).'%';
                    $blocks[$newKey] = $newKey;
                    $childs['%'.str_replace(' ', '_', strtoupper($key)).'%'][] = $newKey;
                    $childsList[$newKey] = $newKey;
                }
                $newKey = '%'.str_replace(' ', '_', strtoupper($key)).'%';
                $blocks[$newKey] = $newKey;
            } else if (!is_int($key)) {
                $newKey = '%'.str_replace(' ', '_', strtoupper($value)).'%';
                $blocks[$newKey] = $newKey;
                $childs['%'.str_replace(' ', '_', strtoupper($key)).'%'][] = $newKey;
                $childsList[$newKey] = $newKey;
                $newKey = '%'.str_replace(' ', '_', strtoupper($key)).'%';
                $blocks[$newKey] = $newKey;
            } else {
                $newKey = '%'.str_replace(' ', '_', strtoupper($value)).'%';
                $blocks[$newKey] = $newKey;
            }
        }

        foreach ($blocks as $key) {
            $start = strpos($html, $key);
            if ($start !== false)
                $blockOrder[$key] = $start;
        }

        asort($blockOrder, SORT_NUMERIC);
        $blockInvertOrder = array_reverse($blockOrder);

        foreach ($blockInvertOrder as $key => $value) {
            $start = strpos($html, $key);
            $end = strrpos($html, $key);

            $htmlBlocks[$key] = substr($html, ($start+strlen($key)), ($end-($start+strlen($key))));
            $html = substr_replace($html, '', $start, $end-$start);
        }

        foreach ($childsList as $key) {
            unset($blockOrder[$key]);
        }

        $this->html = $html;
        $this->htmlBlocks = $htmlBlocks;
        $this->blockOrder = $blockOrder;
        $this->childs = $childs;
        $this->childsList = $childsList;
    }

    private function init($html, $blocks) {
        $blocks = is_array($blocks) ? $blocks : array($blocks);
        $this->parseBlocks($html, $blocks);
    }

    private function isChild() {
        return isset($this->childsList[(string)$this->get]);
    }

}
