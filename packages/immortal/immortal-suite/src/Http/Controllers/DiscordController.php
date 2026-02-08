<?php

namespace Immortal\Suite\Http\Controllers;

use Illuminate\Routing\Controller;
use Immortal\Suite\Services\SettingService;

class DiscordController extends Controller
{
    public function index(SettingService $settings)
    {
        $config = $settings->get('discord');

        return view('immortal-suite::discord.index', compact('config'));
    }
}
