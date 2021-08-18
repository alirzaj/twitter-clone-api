<?php

use Illuminate\Support\Facades\Artisan;

uses(Tests\TestCase::class)->in('Feature', 'Unit');

uses()->beforeEach(fn() => Artisan::call('elastic:create-index'))->in('Feature');
uses()->afterEach(fn() => Artisan::call('elastic:delete-index'))->in('Feature');
