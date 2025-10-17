<?php

namespace views\User\LoginForm;

use views\AbstractView;

class  LoginFormView extends AbstractView {
    const string EMAIL_KEY='email';
    const string PASSWORD_VALUE='password';

    public function __construct(private ?string $error = null )
    {
    }

    function path(): string {
        return __DIR__ . DIRECTORY_SEPARATOR . 'LoginForm.html';
    }

    function templateValues(): array {
        $values = [
            'EMAIL_KEY'=>self::EMAIL_KEY,
            'PASSWORD_KEY'=>self::PASSWORD_VALUE,
            'ACTION_KEY'=>'/user/login'
        ];
        if ($this->error !== null) {
            $errorMessage = match($this->error) {
                'invalid_credentials' => 'Invalid email or password.',
                'database_error' => 'A database error occurred. Please try again.',
                default => 'An unknown error occurred.'
            };
            $values['ERROR_MESSAGE'] = $errorMessage;
        } else {
            $values['ERROR_MESSAGE'] = '';
        }

        return $values;
    }

}