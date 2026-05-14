<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pedidos:cargo-expres')->dailyAt('00:00');