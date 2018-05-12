<?php $isVueRoute = request()->is('/') || request()->is('projects/*'); ?>

<main-nav :is-vue-route='{!! json_encode($isVueRoute) !!}'>
    <template slot='teams'>
        @include('partials.main_nav_teams')
    </template>

    <template slot='users'>
        @can('Manage', Auth::user())
        <li class="dropdown-item nav-item">
            <a href="{{ route('users.index') }}">
                <i class="fa fa-btn fa-users"></i>
                Manage Users
            </a>
        </li>
        @endcan
    </template>
</main-nav>
