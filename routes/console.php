<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('ads:expire')->daily();
Schedule::command('payments:reconcile-dgepay')
    ->everyFiveMinutes()
    ->withoutOverlapping();
