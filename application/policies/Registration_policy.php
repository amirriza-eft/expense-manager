<?php

class Registration_policy
{
    public function can_register($user_id)
    {
        if ($user_id) {
            return false;
        }

        return true;
    }
}
