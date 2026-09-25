<?php

class Category_policy
{
    public function can_create($user_id)
    {
        if (!$user_id) {
            return false;
        }

        return true;
    }

    public function can_update($user_id, $category)
    {
        if (!$user_id) {
            return false;
        }

        if (!$category) {
            return false;
        }

        if ($category->user_id != $user_id) {
            return false;
        }

        return true;
    }

    public function can_delete($user_id, $category)
    {
        if (!$user_id) {
            return false;
        }

        if (!$category) {
            return false;
        }

        if ($category->user_id != $user_id) {
            return false;
        }

        return true;
    }
}
