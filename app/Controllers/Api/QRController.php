<?php

namespace App\Controllers\Api;

use App\Models\QRCode;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;

class QRController extends Controller
{
    private $qrCode;
    private $item;

    public function __construct(QRCode $qrCode, Item $item)
    {
        $this->qrCode = $qrCode;
        $this->item = $item;
    }

    /**
     * Generate QR code for an item
     */
    public function generate(Request $request)
    {
        $validated = $this->validate($request, [
            'item_id' => 'required|integer|exists:items,id',
            'token' => 'required|string'
        ]);

        // Verify token (implement your token verification logic here)
        if (!$this->verifyToken($validated['token'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        try {
            $qrFilename = $this->qrCode->generate($validated['item_id']);
            return response()->json([
                'success' => true,
                'qr_code' => url('uploads/qr/' . $qrFilename)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Scan QR code
     */
    public function scan(Request $request)
    {
        $validated = $this->validate($request, [
            'qr_data' => 'required|string',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'token' => 'required|string'
        ]);

        // Verify token
        if (!$this->verifyToken($validated['token'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $data = $this->qrCode->validate($validated['qr_data']);
        if (!$data) {
            return response()->json(['error' => 'Invalid QR code'], 400);
        }

        $item = $this->item->getById($data['id']);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Create new item based on scan
        $newItem = $this->item->create(
            $validated['user_id'],
            $item['title'],
            $validated['description'],
            $validated['location'],
            date('Y-m-d'),
            $item['type'] === 'lost' ? 'found' : 'lost'
        );

        // Create match
        $this->item->createMatch($item['id'], $newItem['id']);

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $newItem['id'],
                'title' => $newItem['title'],
                'type' => $newItem['type'],
                'status' => 'matched'
            ]
        ]);
    }

    /**
     * Get scan history
     */
    public function history(Request $request)
    {
        $validated = $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'token' => 'required|string'
        ]);

        // Verify token
        if (!$this->verifyToken($validated['token'])) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $scans = $this->item->getScansByUser($validated['user_id']);
        return response()->json([
            'success' => true,
            'scans' => $scans
        ]);
    }

    /**
     * Verify user token
     */
    private function verifyToken($token)
    {
        // Implement your token verification logic here
        // This is just a placeholder
        return true;
    }
}
