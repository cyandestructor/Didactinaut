<?php
namespace Didactinaut\Validators;

class UserEditionValidator extends UserValidator
{
    public function __construct($post_data)
    {
        $this->data = $post_data;
    }

    public function validateForm()
    {
        if(array_key_exists('username', $this->data)){
            $this->validateUsername();    
        }

        if(array_key_exists('email', $this->data)){
            $this->validateEmail();   
        }

        if(array_key_exists('name', $this->data)){
            $this->validateName();   
        }

        if(array_key_exists('lastname', $this->data)){
            $this->validateLastname();
        }

        if(array_key_exists('description', $this->data)){
            $this->validateDescription();
        }

        if(array_key_exists('password', $this->data)){
            $this->validatePassword();
        }

        return $this->errors;
    }

    protected function validateDescription()
    {
        $field = 'description';
        $description = trim($this->data[$field]);
        $max_length = 1000;

        Validations::validate($description, $field, $this->errors)
            ->maxLength($max_length, "The description must be less than $max_length characters long")
            ->notRequired();
    }
}