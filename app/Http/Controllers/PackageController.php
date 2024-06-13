<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;

use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\In;

use App\Exports\ExportPackage;
use Maatwebsite\Excel\Facades\Excel;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Get the authenticated user
    $user = auth()->user();

    // If the user is an admin, show all packages that belong to the merchant
    if ($user->isAdmin()) {
        $packages = Package::with('user')
            ->where('merchant_id', $user->merchant_id)
            ->latest()
            ->paginate(1000);
    } 
    // Otherwise, show the user's packages
    else {
        $packages = Package::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(500);
    }

    $packageCount = $packages->count();
    return Inertia::render('Packages/Index', [
        'packages' => $packages->map(function ($package) {
            return [
                'id' => $package->id,
                'uniqueId' => $package->uniqueId,
                'package_name' => $package->package_name,
                'origin' => $package->origin,
                'destination' => $package->destination,
                'sender_name' => $package->sender_name,
                'receiver_name' => $package->receiver_name,
                'priceInfo' => [
                    'currency' => $package->currency,
                    'price' => $package->price,
                ],
                'status' => $package->status,
                'user_id' => $package->user_id,
                'created_at' => $package->created_at->diffForHumans(),
            ];
        }),
        'pagination' => [
            'total' => $packages->total(),
            'perPage' => $packages->perPage(),
            'currentPage' => $packages->currentPage(),
            'lastPage' => $packages->lastPage(),
        ],
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Packages/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'regex:/^\+255[67][0-9]{8}$/', 'max:20'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'regex:/^\+255[67][0-9]{8}$/', 'max:20'],
            'price' => ['required', 'numeric'],
        ]);

        $attributes['uniqueId'] = '99'.fake()->unique()->numberBetween(1000, 9999);
        $attributes['user_id'] = auth()->user()->id;
        $attributes['merchant_id'] = auth()->user()->merchant_id;

        $package = Package::create($attributes);
        Log::info("message: Package created successfully. Package ID: " . $package->id);
        return redirect()->route('packages.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::with('user')->where('uniqueId', $id)->firstOrFail();
        return Inertia::render('Packages/Show', [
            'show_package' => [
                'id' => $package->id,                
                'uniqueId' => $package->uniqueId,
                'package_name' => $package->package_name,
                'description' => $package->description,
                'origin' => $package->origin,
                'destination' => $package->destination,
                'sender_name' => $package->sender_name,
                'sender_phone' => $package->sender_phone,
                'receiver_name' => $package->receiver_name,
                'receiver_phone' => $package->receiver_phone,
                'priceInfo' => [
                    'currency' => $package->currency,
                    'price' => number_format($package->price, 2),
                ],
                'status' => $package->status,
                'user_id' => $package->user_id,
                'created_at' => $package->created_at->format('d/m/Y H:i'),
                'updated_at' => $package->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $package = Package::with('user')->where('uniqueId', $id)->firstOrFail();
        return Inertia::render('Packages/Edit', [
            'show_package' => [
                'id' => $package->id,
                'uniqueId' => $package->uniqueId,
                'package_name' => $package->package_name,
                'description' => $package->description,
                'origin' => $package->origin,
                'destination' => $package->destination,
                'sender_name' => $package->sender_name,
                'sender_phone' => $package->sender_phone,
                'receiver_name' => $package->receiver_name,
                'receiver_phone' => $package->receiver_phone,
                'priceInfo' => [
                    'currency' => $package->currency,
                    'price' => $package->price,
                ],
                'status' => $package->status,
                'user_id' => $package->user_id,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        // Define all possible validation rules
        $rules = [
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'regex:/^\+255[67][0-9]{8}$/', 'max:20'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'regex:/^\+255[67][0-9]{8}$/', 'max:20'],
            'price' => ['required', 'numeric'],
            'status' => ['required', new In(Package::VALID_STATUSES)],
        ];
    
        // Filter the rules to only include those present in the request
        $dynamicRules = array_intersect_key($rules, $request->all());
    
        // Validate the request with the dynamic rules
        $attributes = $request->validate($dynamicRules);
    
        // Update the package with the validated attributes
        $package->update($attributes);
    
        Log::info('USER: "'.auth()->user()->name. '" successfully updated Package ID: ' . $package->id);
        return redirect()->route('packages.show', $package->uniqueId);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);
        $saveName = $package->uniqueId;
        $package->delete();

        Log::info("User ".auth()->user()->name." deleted package with id ". $saveName);
        return response()->json(['message' => 'Package deleted successfully']);
    }

    public function exportPackages(Request $request)
    {
        try {
        Log::info('USER: "'.auth()->user()->name. '" attempted to Export Packages');
        $packages = $request->all();
        $export = new ExportPackage($packages);
        $fileName = 'packages.xlsx';

        return Excel::download($export, $fileName);
        } catch (\Exception $e) {
            Log::error('Error exporting packages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export packages'], 500);
        }
    }
}
