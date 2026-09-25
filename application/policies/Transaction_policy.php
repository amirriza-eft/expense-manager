<?php

class Transaction_policy
{
    public function can_create($user_id)
    {
        if (!$user_id) {
            return false;
        }

        return true;
    }

    public function can_update($user_id, $transaction)
    {
        if (!$user_id) {
            return false;
        }

        if (!$transaction) {
            return false;
        }

        if ($transaction->user_id != $user_id) {
            return false;
        }

        return true;
    }

    public function can_delete($user_id, $transaction)
    {
        if (!$user_id) {
            return false;
        }

        if (!$transaction) {
            return false;
        }

        if ($transaction->user_id != $user_id) {
            return false;
        }

        return true;
    }
}
