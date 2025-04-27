<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SensorController extends Controller
{
    /**
     * Display a listing of the sensors.
     */
    public function index()
    {
        $sensors = Sensor::all();
        return view('admin.sensors.index', compact('sensors'));
    }

    /**
     * Show the form for creating a new sensor.
     */
    public function create()
    {
        return view('admin.sensors.create');
    }

    /**
     * Store a newly created sensor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sensor_id' => 'required|string|unique:sensors,sensor_id',
            'name' => 'required|string|max:255',
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        Sensor::create($validated);

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor created successfully.');
    }

    /**
     * Display the specified sensor.
     */
    public function show(Sensor $sensor)
    {
        return view('admin.sensors.show', compact('sensor'));
    }

    /**
     * Show the form for editing the specified sensor.
     */
    public function edit(Sensor $sensor)
    {
        return view('admin.sensors.edit', compact('sensor'));
    }

    /**
     * Update the specified sensor in storage.
     */
    public function update(Request $request, Sensor $sensor)
    {
        $validated = $request->validate([
            'sensor_id' => 'required|string|unique:sensors,sensor_id,' . $sensor->id,
            'name' => 'required|string|max:255',
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $sensor->update($validated);

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor updated successfully.');
    }

    /**
     * Remove the specified sensor from storage.
     */
    public function destroy(Sensor $sensor)
    {
        $sensor->delete();

        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor deleted successfully.');
    }
}
