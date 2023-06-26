<?php

function validateEmail($email) {
    // Email validation using a regular expression
    $pattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
    return preg_match($pattern, $email);
}

function validatePassword($password) {
    // Password validation rules
    // At least 8 characters
    // Contains at least one uppercase letter, one lowercase letter, one digit, and one special character
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    return preg_match($pattern, $password);
}

?>
