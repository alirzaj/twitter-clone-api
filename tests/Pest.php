<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;

uses(Tests\TestCase::class)->in('Feature', 'Unit');
uses(RefreshDatabase::class)->in('Feature');
uses(WithFaker::class)->in('Feature');

uses()->beforeEach(fn() => Artisan::call('elastic:create-index'))->in('Feature');
uses()->afterEach(fn() => Artisan::call('elastic:delete-index'))->in('Feature');
