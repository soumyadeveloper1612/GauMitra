<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = $this->filteredUsersQuery($request)
            ->latest()
            ->get();

        $stats = [
            'totalUsers'       => User::count(),
            'activeUsers'      => User::where('status', 'active')->count(),
            'inactiveUsers'    => User::where('status', 'inactive')->count(),
            'verifiedUsers'    => User::whereNotNull('mobile_verified_at')->count(),
            'notVerifiedUsers' => User::whereNull('mobile_verified_at')->count(),
            'withAddressUsers' => User::has('addresses')->count(),
            'filteredUsers'    => $users->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show($id)
    {
        $user = User::with([
            'addresses' => function ($query) {
                $query->latest();
            },
            'loginOtps' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function export(Request $request)
    {
        $users = $this->filteredUsersQuery($request)
            ->latest()
            ->get();

        $fileName = 'users_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new UsersExport($users), $fileName);
    }

    public function addresses($id)
    {
        $user = User::with([
            'addresses' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        $addresses = $user->addresses->map(function ($address) {
            return [
                'full_address'    => $address->full_address,
                'street'          => $address->street,
                'village'         => $address->village,
                'police_station'  => $address->police_station,
                'city'            => $address->city,
                'district'        => $address->district,
                'state'           => $address->state,
                'pincode'         => $address->pincode,
                'area_name'       => $address->area_name,
                'latitude'        => $address->latitude,
                'longitude'       => $address->longitude,
                'google_place_id' => $address->google_place_id,
                'plus_code'       => $address->plus_code,
                'created_at'      => optional($address->created_at)->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'status'    => true,
            'user'      => [
                'id'     => $user->id,
                'name'   => $user->name,
                'mobile' => $user->mobile,
            ],
            'addresses' => $addresses,
        ]);
    }

    private function filteredUsersQuery(Request $request)
    {
        return User::with(['latestAddress'])
            ->withCount('addresses')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status_filter'), function ($query) use ($request) {
                $query->where('status', $request->status_filter);
            })
            ->when($request->verified_filter === 'verified', function ($query) {
                $query->whereNotNull('mobile_verified_at');
            })
            ->when($request->verified_filter === 'not_verified', function ($query) {
                $query->whereNull('mobile_verified_at');
            });
    }
}