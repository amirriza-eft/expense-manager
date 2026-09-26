<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_policy
{
    public function can_update($user_id, $data)
    {
        if (empty($user_id)) {
            return false;
        }

        if (empty(trim($data['full_name'] ?? ''))) {
            return false;
        }

        if (empty(trim($data['email'] ?? ''))) {
            return false;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}