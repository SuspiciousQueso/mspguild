<?php
/**
 * Global Helper Functions for MSPGuild
 */

/**
 * Sanitize output for safe display in HTML
 * 
 * @param string|null $data The string to sanitize
 * @return string The sanitized string
 */
function sanitizeOutput($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}