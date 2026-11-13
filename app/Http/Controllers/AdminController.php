<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\ChangeRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalClients = Client::count();
        $totalProjects = Project::count();
        $totalPegawai = Pegawai::count();
        $totalUsers = User::count();
        $totalCrs = ChangeRequest::count();

        // Data terbaru
        $recentClients = Client::with(['pics.user', 'projects'])
            ->withCount('changeRequests')
            ->latest()
            ->take(5)
            ->get();
        $recentProjects = Project::with(['client', 'pm.pegawai', 'pms.pegawai', 'changeRequests'])
            ->latest()
            ->take(5)
            ->get();
        $recentPegawais = Pegawai::with('user')->latest()->take(5)->get();
        $recentCrs = ChangeRequest::with(['client', 'project', 'user'])->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalClients',
            'totalProjects',
            'totalPegawai',
            'totalUsers',
            'totalCrs',
            'recentClients',
            'recentProjects',
            'recentPegawais',
            'recentCrs'
        ));
    }
}

