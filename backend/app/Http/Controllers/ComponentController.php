<?php

namespace App\Http\Controllers;

use App\Models\Processor;
use App\Models\Motherboard;
use App\Models\Ram;
use App\Models\Storage;
use App\Models\VideoCard;
use App\Models\Psu;
use App\Models\DvdRom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComponentController extends Controller
{
    /**
     * Get all components
     */
    public function index(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'processors' => Processor::all(),
                    'motherboards' => Motherboard::all(),
                    'rams' => Ram::all(),
                    'storages' => Storage::all(),
                    'video_cards' => VideoCard::all(),
                    'psus' => Psu::all(),
                    'dvd_roms' => DvdRom::all(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching components: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new component
     */
    public function create(Request $request): JsonResponse
    {
        $type = $request->input('type');
        
        try {
            switch ($type) {
                case 'processors':
                    return $this->addProcessor($request);
                case 'motherboards':
                    return $this->addMotherboard($request);
                case 'rams':
                    return $this->addRam($request);
                case 'storage':
                    return $this->addStorage($request);
                case 'video-cards':
                    return $this->addVideoCard($request);
                case 'dvd-roms':
                    return $this->addDvdRom($request);
                case 'psus':
                    return $this->addPsu($request);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid component type'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating component: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a component
     */
    public function delete($id): JsonResponse
    {
        try {
            // Try to find and delete from all component tables
            $component = Processor::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = Motherboard::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = Ram::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = Storage::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = VideoCard::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = Psu::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            $component = DvdRom::find($id);
            if ($component) {
                $component->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Component deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Component not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting component: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get component statistics
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_processors' => Processor::count(),
                'total_motherboards' => Motherboard::count(),
                'total_rams' => Ram::count(),
                'total_storages' => Storage::count(),
                'total_video_cards' => VideoCard::count(),
                'total_psus' => Psu::count(),
                'total_dvd_roms' => DvdRom::count(),
                'available_components' => $this->getAvailableComponents(),
            ]
        ]);
    }

    /**
     * Add processor
     */
    public function addProcessor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $processor = Processor::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Processor added successfully',
            'data' => $processor
        ]);
    }

    /**
     * Add motherboard
     */
    public function addMotherboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $motherboard = Motherboard::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Motherboard added successfully',
            'data' => $motherboard
        ]);
    }

    /**
     * Add RAM
     */
    public function addRam(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'capacity' => 'required|in:4GB,8GB,16GB,32GB',
            'type' => 'nullable|string|in:DDR3,DDR4,DDR5,LPDDR4,LPDDR5',
            'speed' => 'nullable|string|max:50',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $ram = Ram::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'RAM added successfully',
            'data' => $ram
        ]);
    }

    /**
     * Add storage
     */
    public function addStorage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'capacity' => 'required|string',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $storage = Storage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Storage added successfully',
            'data' => $storage
        ]);
    }

        /**
     * Add video card
     */
    public function addVideoCard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $videoCard = VideoCard::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Video card added successfully',
            'data' => $videoCard
        ]);
    }

    /**
     * Add PSU
     */
    public function addPsu(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wattage' => 'required|in:350,450,550,650,750,850,1000',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $psu = Psu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'PSU added successfully',
            'data' => $psu
        ]);
    }

    /**
     * Add DVD ROM
     */
    public function addDvdRom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type_field' => 'required|in:CD-ROM,DVD-ROM,DVD-RW,Blu-ray,Blu-ray RW',
            'status' => 'required|in:Available,In Use,Defective',
        ]);

        $dvdRom = DvdRom::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'DVD ROM added successfully',
            'data' => $dvdRom
        ]);
    }

    /**
     * Update component
     */
    public function updateComponent(Request $request, $type, $id): JsonResponse
    {
        $component = $this->getComponentByType($type, $id);
        
        if (!$component) {
            return response()->json([
                'success' => false,
                'message' => 'Component not found'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:Available,In Use,Defective',
            'price' => 'sometimes|numeric',
        ]);

        $component->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Component updated successfully',
            'data' => $component
        ]);
    }

    /**
     * Delete component
     */
    public function deleteComponent($type, $id): JsonResponse
    {
        $component = $this->getComponentByType($type, $id);
        
        if (!$component) {
            return response()->json([
                'success' => false,
                'message' => 'Component not found'
            ], 404);
        }

        $component->delete();

        return response()->json([
            'success' => true,
            'message' => 'Component deleted successfully'
        ]);
    }

    private function getComponentByType($type, $id)
    {
        switch ($type) {
            case 'processors':
                return Processor::find($id);
            case 'motherboards':
                return Motherboard::find($id);
            case 'rams':
                return Ram::find($id);
            case 'storages':
                return Storage::find($id);
            case 'video_cards':
                return VideoCard::find($id);
            case 'psus':
                return Psu::find($id);
            case 'dvd_roms':
                return DvdRom::find($id);
            default:
                return null;
        }
    }

    private function getAvailableComponents(): array
    {
        return [
            'processors' => Processor::where('status', 'Available')->count(),
            'motherboards' => Motherboard::where('status', 'Available')->count(),
            'rams' => Ram::where('status', 'Available')->count(),
            'storages' => Storage::where('status', 'Available')->count(),
            'video_cards' => VideoCard::where('status', 'Available')->count(),
            'psus' => Psu::where('status', 'Available')->count(),
            'dvd_roms' => DvdRom::where('status', 'Available')->count(),
        ];
    }
}
