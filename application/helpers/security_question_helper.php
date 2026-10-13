<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('security_questions_list')) {
    /** @return array<int,string> */
    function security_questions_list()
    {
        $CI =& get_instance();
        $CI->config->load('security_questions', true);

        $list = $CI->config->item('security_questions', 'security_questions');

        return is_array($list) ? $list : [];
    }
}

if (!function_exists('security_question_text')) {
    function security_question_text($id)
    {
        $list = security_questions_list();
        $id = (int) $id;

        return isset($list[$id]) ? $list[$id] : '';
    }
}

if (!function_exists('is_valid_security_question_id')) {
    function is_valid_security_question_id($id)
    {
        return security_question_text($id) !== '';
    }
}

if (!function_exists('normalize_security_answer')) {
    /**
     * Same rules for register, profile update, and recovery.
     * Trim + lowercase so checks are case-insensitive.
     */
    function normalize_security_answer($answer)
    {
        $answer = trim((string) $answer);
        $answer = preg_replace('/\s+/u', ' ', $answer);

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($answer, 'UTF-8');
        }

        return strtolower($answer);
    }
}

if (!function_exists('hash_security_answer')) {
    function hash_security_answer($answer)
    {
        return password_hash(
            normalize_security_answer($answer),
            PASSWORD_DEFAULT
        );
    }
}

if (!function_exists('verify_security_answer')) {
    function verify_security_answer($answer, $hash)
    {
        if ($hash === null || $hash === '') {
            return false;
        }

        return password_verify(
            normalize_security_answer($answer),
            $hash
        );
    }
}
