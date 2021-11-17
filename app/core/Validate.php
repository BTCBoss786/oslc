<?php

class Validate {
    private $_passed = false,
            $_errors = [],
            $_db = null;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    public function check($source, $items = []) {
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                $value = trim($source[$item]);
                $item = htmlentities($item, ENT_QUOTES, "UTF-8");
                if ($rule === "required" && empty($value)) {
                    $this->_errors[] = "{$rules["name"]} is Required";
                } else if (!empty($value)) {
                    switch ($rule) {
                        case "min":
                            if (strlen($value) < $rule_value) {
                                $this->_errors[] = "{$rules["name"]} must be minimum of {$rule_value}";
                            }
                            break;
                        case "max":
                            if (strlen($value) > $rule_value) {
                                $this->_errors[] = "{$rules["name"]} must be maximum of {$rule_value}";
                            }
                            break;
                        case "match":
                            if ($value != $source[$rule_value]) {
                                $this->_errors[] = "{$rules["name"]} must match {$items[$rule_value]["name"]}";
                            }
                            break;
                        case "unique":
                            $check = $this->_db->get($rule_value, [$rules["uniqueItem"], "=", $value]);
                            if ($check->count()) {
                                $this->_errors[] = "{$rules["name"]} Already Exists";
                            }
                            break;
                        case "age":
                            $today = new DateTime(date("Y-m-d"));
                            $date = new DateTime($value);
                            $diff = $date->diff($today);
                            if ($diff->y < $rule_value) {
                                $this->_errors[] = "{$rules["name"]} must be atleast {$rule_value} years";
                            }
                    }
                }
            }
        }
        if (empty($this->errors())) {
            $this->_passed = true;
        }
        return $this;
    }

    public function errors() {
        return $this->_errors;
    }

    public function passed() {
        return $this->_passed;
    }
}