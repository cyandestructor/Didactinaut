<?php
namespace Didactinaut\Validators;

class Validations  
{
    private $value;
    private $field;
    private $errors;

    private function __construct($value, $field_name, &$errors_array)
    {
        $this->value = $value;
        $this->field = $field_name;
        $this->errors = &$errors_array;
    }

    public static function validate($value, $field_name, &$errors_array)
    {
        return new Validations($value, $field_name, $errors_array);
    }

    public function& alphabetic($error_msg)
    {
        if (!preg_match('/^[\p{L}]+$/u', $this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& alphabeticSpaces($error_msg)
    {
        if (!preg_match('/^[\p{L}\s]+$/u', $this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& numeric($error_msg)
    {
        if (!preg_match('/^\d+$/', $this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& alphanumeric($error_msg)
    {
        if (!preg_match('/^[a-zA-Z\d]+$/', $this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& number($error_msg)
    {
        if (!is_numeric($this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& minValue($value, $error_msg)
    {
        if ($this->value < $value) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& maxValue($value, $error_msg)
    {
        if ($this->value > $value) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& minLength($min, $error_msg)
    {
        if (strlen($this->value) < $min) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& maxLength($max, $error_msg)
    {
        if (strlen($this->value) > $max) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& email($error_msg)
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function& matchExpression($expression, $error_msg)
    {
        if (!preg_match($expression, $this->value)) {
            $this->addError($error_msg);
        }

        return $this;
    }

    public function required($error_msg)
    {
        if (empty($this->value)) {
            $this->addError($error_msg);
        }
    }

    public function notRequired()
    {
        if (empty($this->value) && isset($this->errors[$this->field])) {
            unset($this->errors[$this->field]);
        }
    }

    private function addError($value)
    {
        $this->errors[$this->field] = $value;
    }
}