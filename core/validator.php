<?php

/**
 * Validator description
 * 
 * <p>The validator core perform form validation<p>
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
class Validator
{

    /**
     * The list of rules to pass
     * 
     * @var array
     */
    private $rules = array();

    /**
     * The list of found errors
     * 
     * @var array
     */
    private $errors = array();

    /**
     * The lexicon of errors messages
     * 
     * @var array
     */
    private $messages = array();

    /**
     * Construct
     * 
     */
    public function __construct() {
        $xml = new \DOMDocument;
        $xml->load('./config/errors.xml');

        $errors = $xml->getElementsByTagName('error');

        foreach ($errors as $error) {
            $this->messages[$error->getAttribute('rule')] = $error->getAttribute('message');
        }
    }

    /**
     * Add a rule to the validator
     * 
     * @param string $field The targetted field
     * @param sring $rules The rule name
     * @return \Validator The validator instance
     */
    public function addRules($field, $rules) {
        $this->rules[$field] = $rules;

        return $this;
    }

    /**
     * Remove a field from the validator
     * 
     * @param string $field The targetted field
     * @return \Validator The validator instance
     */
    public function removeRules($field) {

        unset($this->rules[$field]);

        return $this;
    }

    /**
     * Reset the validator
     * 
     * @return \Validator The validator instance
     */
    public function flush() {
        $this->rules = array();
        $this->errors = array();

        return $this;
    }

    /**
     * Run the validation process
     * 
     * @return boolean
     * @throws \RuntimeException
     */
    public function run() {

        if (empty($this->rules)) {
            throw new \RuntimeException('No rules to check');
        }

        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);

            if (isset($_POST[$field]) and $_POST[$field] != '') {
                $value = $_POST[$field];

                foreach ($rules as $rule) {
                    if ($rule != 'required') {

                        if (!$this->check($rule, $field, $value)) {
                            break;
                        }
                    }
                }
            } elseif (in_array('required', $rules)) {
                $this->addError('required', $field);
            } else {
                
            }
        }

        if(empty($this->errors)) {
            return TRUE;
        } else {
            
        }
    }

    /**
     * Check if the value match with the rule
     * 
     * @param string $rule The rule name
     * @param string $field The targetted field
     * @param mixed $value The value to check
     * @param mixed $param The rule parameter
     * @return boolean
     */
    private function check($rule, $field, $value, $param = null) {

        if (preg_match('#\[(.+)\]$#', $rule, $matches)) {
            $split = explode('[', $rule);
            $rule = $split[0];
            $param = $matches[1];
        };

        if (method_exists($this, $rule)) {
            if ($this->$rule($value, $param) == FALSE) {
                $this->addError($rule, $field, $param);
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            throw new RuntimeException('Specified ' . $rule . ' rule does not exist');
        }
    }

    /**
     * Add an error to the validator
     * 
     * @param string $rule The rule name
     * @param string $field The targetted field
     * @param mixed $param The rule parameter
     */
    private function addError($rule, $field, $param = null) {

        if (in_array($rule, array_keys($this->messages))) {
            $message = $this->messages[$rule];

            if (strpos($message, '%f')) {
                $message = str_replace('%f', $field, $message);
            }
            if (strpos($message, '%p')) {
                $message = str_replace('%p', $param, $message);
            }
        } else {
            $message = $this->messages['default'];
        }

        $this->errors[$field] = $message;
    }

    /**
     * Add a custom error to the validator
     * 
     * @param string $field The targetted field
     * @param mixed $message The custom error message
     */
    public function addCustomError($name, $message) {
        $this->errors[$name] = $message;
    }

    /**
     * Get the validation errors
     * 
     * @return array The validation errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get the error message for a field
     * 
     * @param string $field The field name
     * @return mixed
     */
    public function getError($field) {
        return in_array($field, array_keys($this->errors)) ? $this->errors[$field] : FALSE;
    }

    /**
     * Return true if the validator has errors
     * 
     * @return boolean
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Return true if the validator has an error for that field
     * 
     * @param string $field The field name
     * @return boolean
     */
    public function hasError($field) {
        return in_array($field, array_keys($this->errors));
    }

    /**
     * Check equality
     * 
     * @param mixed $value The field value
     * @param mixed $param The value to equal
     * @return boolean
     */
    private function equals($value, $param) {
        return $value == $param;
    }

    /**
     * Check numeric
     * 
     * @param mixed $value The field value
     * @return boolean
     */
    private function numeric($value) {
        return is_numeric($value);
    }

    /**
     * Check greater
     * 
     * @param mixed $value The field value
     * @param mixed $param The max nmber
     * @return boolean
     */
    private function greater($value, $param) {
        if (is_numeric($value) and is_numeric($param)) {
            return $value > $param;
        } else {
            return FALSE;
        }
    }

    /**
     * Check lesser
     * 
     * @param mixed $value The field value
     * @param mixed $param The min number
     * @return boolean
     */
    private function lesser($value, $param) {
        if (is_numeric($value) and is_numeric($param)) {
            return $value < $param;
        } else {
            return FALSE;
        }
    }

    /**
     * Check valid ip address
     * 
     * @param string $value The ip address
     * @return boolean
     */
    private function ip($value) {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    /**
     * Check valid email address
     * 
     * @param string $value The email address
     * @return boolean
     */
    private function email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if the value match another field
     * 
     * @param mixed $value The field value
     * @param mixed $param The other field
     * @return boolean
     */
    private function match($value, $param) {
        if (is_string($param)) {
            return isset($_POST[$param]) ? $value == $_POST[$param] : FALSE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check string for an exact lenght
     * 
     * @param type $value The field value
     * @param type $param The lenght to match
     * @return boolean
     */
    private function length($value, $param) {
        if (is_string($value) and is_numeric($param)) {
            return strlen($value) == $param;
        } else {
            return FALSE;
        }
    }

    /**
     * Check string for a maximum lenght
     * 
     * @param type $value The field value
     * @param type $param The lenght to match
     * @return boolean
     */
    private function maxLength($value, $param) {
        if (is_string($value) and is_numeric($param)) {
            return strlen($value) <= $param;
        } else {
            return FALSE;
        }
    }

    /**
     * Check string for a minimum lenght
     * 
     * @param type $value The field value
     * @param type $param The lenght to match
     * @return boolean
     */
    private function minLength($value, $param) {
        if (is_string($value) and is_numeric($param)) {
            return strlen($value) >= $param;
        } else {
            return FALSE;
        }
    }

    /**
     * Check string for a correct french d/m/Y date format
     * 
     * @param string $value The field value
     * @return boolean
     */
    private function validDate($value) {
        return preg_match('#^\d{2}\/\d{2}\/\d{4}$#', $value);
    }

}