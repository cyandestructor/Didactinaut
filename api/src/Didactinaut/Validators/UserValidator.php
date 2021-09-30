<?php
namespace Didactinaut\Validators;

class UserValidator implements ValidatorInterface
{
    protected $data;
    protected $errors = [];
    private static $fields = ['username', 'name', 'lastname', 'email', 'password'];

    public function __construct($post_data)
    {
        $this->data = $post_data;
    }

    public function validateForm()
    {
        foreach(self::$fields as $field)
        {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in the data");
                return;
            }
        }

        $this->validateUsername();
        $this->validateEmail();
        $this->validateName();
        $this->validateLastname();
        $this->validatePassword();

        return $this->errors;
    }

    protected function validateUsername()
    {
        $field = 'username';
        $username = trim($this->data[$field]);

        Validations::validate($username, $field, $this->errors)
            ->alphanumeric('The username must be alphanumeric')
            ->minLength(6, 'The username must be at least 6 characters long')
            ->maxLength(30, 'The username must be less than 30 characters long')
            ->required('The username cannot be empty');
    }

    protected function validateEmail()
    {
        $field = 'email';
        $email = trim($this->data[$field]);

        Validations::validate($email, $field, $this->errors)
            ->email('The email must be a valid email')
            ->required('The email cannot be empty');
    }

    protected function validateName()
    {
        $field = 'name';
        $name = trim($this->data[$field]);

        Validations::validate($name, $field, $this->errors)
            ->alphabeticSpaces('The name must be alphabetic')
            ->required('The name cannot be empty');
    }

    protected function validateLastname()
    {
        $field = 'lastname';
        $lastname = trim($this->data[$field]);

        Validations::validate($lastname, $field, $this->errors)
            ->alphabeticSpaces('The last name must be alphabetic')
            ->required('The last name cannot be empty');
    }

    protected function validatePassword()
    {
        $field = 'password';
        $password = $this->data[$field];

        Validations::validate($password, $field, $this->errors)
            ->matchExpression('/.*(?=.*[A-Z])(?=.*\d)(?=.*\W).*/', 'The password must have at least one uppercase, one number and one special character')
            ->minLength(8, 'The password must be at least 8 characters long')
            ->required('The password cannot be empty');
    }
}