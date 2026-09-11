<?php

test('the root path serves the public landing page to guests', function () {
    $this->get('/')->assertOk();

    $this->get('/dashboard')->assertRedirect('/login');
});
