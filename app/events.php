<?php
Event::listen('generic.event',function($client_data){
    Log::info('root event');
    return BrainSocket::message('generic.event',array('message'=>'An important message from a generic event fired in Laravel!'));
});

Event::listen('app.success',function($client_data){
    return BrainSocket::success(array('There was a Laravel App Success Event!'));
});

Event::listen('app.error',function($client_data){
    return BrainSocket::error(array('There was a Laravel App Error!'));
});
