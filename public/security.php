
<?php
/**
 * Sanitize user input to prevent XSS and other injection attacks.
 *
 * @param mixed $input The user input to sanitize.
 * @param string $type The type of input ('string', 'int', 'float', 'email', 'url').
 * @return mixed The sanitized input.
 */

function sanitizeInput($input, $type = 'string') {
    if ($input === null) return '';
    
    switch($type) {
        case 'string':
            return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        case 'int':
            return intval($input);
        case 'float':
            return floatval($input);
        case 'email':
            return filter_var($input, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($input, FILTER_SANITIZE_URL);
        default:
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}