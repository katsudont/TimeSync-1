<?php

namespace App\Controllers;

class LandingController extends BaseController
{
    public function index()
    {
        // Render the landing.mustache template
        $this->render('landing', [
            'title' => 'TimeSync - Employee Attendance',
            'description' => 'Welcome to TimeSync, your comprehensive solution for tracking and managing employee attendance seamlessly. With features designed to enhance productivity and simplify workflows, TimeSync helps your business stay organized and efficient.',
        ]);
    }
}
