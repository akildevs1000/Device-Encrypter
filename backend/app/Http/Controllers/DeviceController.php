<?php
namespace App\Http\Controllers;

use App\Http\Requests\Device\ValidationRequest;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Device::where("company_id", request("company_id"))->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        $data = $request->validated();

        $ids = collect($data["devices"])->pluck('id')->filter();

        // Get already existing IDs
        $existingIds = Device::whereIn('id', $ids)->pluck('id')->toArray();

        // Filter out devices with existing IDs
        $newDevices = collect($data["devices"])
            ->whereNotIn('id', $existingIds)
            ->values()
            ->all();

        if (! empty($newDevices)) {
           Device::insert($newDevices);
        }

        return response()->json(['message' => 'Devices stored successfully']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Device $device)
    {
        $device->update($request->validated());

        return $device;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return response()->noContent();
    }
}
