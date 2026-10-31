<?php

namespace Utils;

class Validator
{
    private $data;
    private $errors = [];
    private $rules = [];
    private $messages = [];
    private $customMessages = [];

    public function __construct(array $data = [], array $rules = [], array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
    }

    public function validate(array $data = null, array $rules = null, array $messages = null): bool
    {
        if ($data !== null) {
            $this->data = $data;
        }
        
        if ($rules !== null) {
            $this->rules = $rules;
        }
        
        if ($messages !== null) {
            $this->customMessages = $messages;
        }

        foreach ($this->rules as $field => $rules) {
            $rules = is_string($rules) ? explode('|', $rules) : $rules;
            
            foreach ($rules as $rule) {
                $this->validateField($field, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors[array_key_first($this->errors)][0] ?? null;
    }

    protected function validateField(string $field, string $rule): void
    {
        $value = $this->getValue($field);
        
        if ($rule === 'required' && $this->isEmpty($value)) {
            $this->addError($field, 'required');
            return;
        }
        
        if ($this->isEmpty($value) && $rule !== 'required') {
            return; // Skip validation for empty fields unless required
        }

        $params = [];
        
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule, 2);
            $params = explode(',', $param);
        }
        
        $method = 'validate' . str_replace('_', '', ucwords($rule, '_'));
        
        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $params)) {
                $this->addError($field, $rule, $params);
            }
        } elseif (function_exists($rule)) {
            if (!$rule($value)) {
                $this->addError($field, $rule, $params);
            }
        }
    }

    protected function getValue(string $field)
    {
        $keys = explode('.', $field);
        $value = $this->data;
        
        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }
        
        return $value;
    }

    protected function isEmpty($value): bool
    {
        if (is_null($value)) {
            return true;
        }
        
        if (is_string($value) && trim($value) === '') {
            return true;
        }
        
        if (is_array($value) && count($value) === 0) {
            return true;
        }
        
        return false;
    }

    protected function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->getMessage($field, $rule, $params);
        $this->errors[$field] = $this->errors[$field] ?? [];
        $this->errors[$field][] = $message;
    }

    protected function getMessage(string $field, string $rule, array $params = []): string
    {
        $key = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }
        
        if (isset($this->customMessages[$field])) {
            return $this->customMessages[$field];
        }
        
        $messages = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least :0 characters.",
            'max' => "The {$field} may not be greater than :0 characters.",
            'between' => "The {$field} must be between :0 and :1.",
            'numeric' => "The {$field} must be a number.",
            'integer' => "The {$field} must be an integer.",
            'string' => "The {$field} must be a string.",
            'date' => "The {$field} is not a valid date.",
            'confirmed' => "The {$field} confirmation does not match.",
            'unique' => "The {$field} has already been taken.",
            'exists' => "The selected {$field} is invalid.",
            'regex' => "The {$field} format is invalid.",
            'url' => "The {$field} format is invalid.",
            'ip' => "The {$field} must be a valid IP address.",
            'ipv4' => "The {$field} must be a valid IPv4 address.",
            'ipv6' => "The {$field} must be a valid IPv6 address.",
            'json' => "The {$field} must be a valid JSON string.",
            'mimes' => "The {$field} must be a file of type: :0.",
            'image' => "The {$field} must be an image.",
            'mimetypes' => "The {$field} must be a file of type: :0.",
            'dimensions' => "The {$field} has invalid image dimensions.",
        ];
        
        $message = $messages[$rule] ?? "The {$field} is invalid.";
        
        foreach ($params as $key => $param) {
            $message = str_replace(":{$key}", $param, $message);
        }
        
        return $message;
    }

    // Validation Rules
    protected function validateEmail(string $field, $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateMin(string $field, $value, array $params): bool
    {
        if (!isset($params[0])) {
            return false;
        }
        
        $min = (int)$params[0];
        
        if (is_numeric($value)) {
            return $value >= $min;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) >= $min;
        }
        
        if (is_array($value)) {
            return count($value) >= $min;
        }
        
        return false;
    }

    protected function validateMax(string $field, $value, array $params): bool
    {
        if (!isset($params[0])) {
            return false;
        }
        
        $max = (int)$params[0];
        
        if (is_numeric($value)) {
            return $value <= $max;
        }
        
        if (is_string($value)) {
            return mb_strlen($value) <= $max;
        }
        
        if (is_array($value)) {
            return count($value) <= $max;
        }
        
        return false;
    }

    protected function validateBetween(string $field, $value, array $params): bool
    {
        if (count($params) < 2) {
            return false;
        }
        
        $min = (int)$params[0];
        $max = (int)$params[1];
        
        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }
        
        if (is_string($value)) {
            $length = mb_strlen($value);
            return $length >= $min && $length <= $max;
        }
        
        if (is_array($value)) {
            $count = count($value);
            return $count >= $min && $count <= $max;
        }
        
        return false;
    }

    protected function validateNumeric(string $field, $value): bool
    {
        return is_numeric($value);
    }

    protected function validateInteger(string $field, $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    protected function validateString(string $field, $value): bool
    {
        return is_string($value);
    }

    protected function validateDate(string $field, $value): bool
    {
        if (strtotime($value) === false) {
            return false;
        }
        
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    protected function validateConfirmed(string $field, $value): bool
    {
        $confirmation = $this->getValue($field . '_confirmation');
        return $value === $confirmation;
    }

    protected function validateRegex(string $field, $value, array $params): bool
    {
        if (empty($params[0])) {
            return false;
        }
        
        return (bool)preg_match($params[0], $value);
    }
}
