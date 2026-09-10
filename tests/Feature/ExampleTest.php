<?php

test('the root path redirects guests to the login screen', function () {
    $this->get('/')->assertRedirect('/dashboard');

    $this->get('/dashboard')->assertRedirect('/login');
});
