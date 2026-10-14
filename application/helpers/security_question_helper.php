<?php

defined('BASEPATH') or exit('No direct script access allowed');

function security_questions_list(): array
{
    $config = get_instance()->config;
    $config->load('security_questions', true);

    return (array) $config->item('security_questions', 'security_questions');
}

function security_question_text($id): string
{
    return security_questions_list()[(int) $id] ?? '';
}

function is_valid_security_question_id($id): bool
{
    return security_question_text($id) !== '';
}

function normalize_security_answer($answer): string
{
    $answer = preg_replace('/\s+/u', ' ', trim((string) $answer));

    return mb_strtolower($answer, 'UTF-8');
}

function hash_security_answer($answer): string
{
    return password_hash(normalize_security_answer($answer), PASSWORD_DEFAULT);
}

function verify_security_answer($answer, $hash): bool
{
    return !empty($hash)
        && password_verify(normalize_security_answer($answer), $hash);
}