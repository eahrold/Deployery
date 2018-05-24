<?php

namespace App\Models;


class Access
{

    private function guard() {
        return \Auth::guard();
    }
    private function user() {
        return $this->guard()->user();
    }

    private function teams() {
        if ($this->user()) {
            return $this->user()->presenter()->shouldShowTeamMenu();
        }
        return false;
    }

    public function forUser() {
        return [
            'is_guest' => $this->guard()->guest(),
            'teams' => $this->teams(),
        ];
    }
}