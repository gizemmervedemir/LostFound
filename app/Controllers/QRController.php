<?php

namespace App\Controllers;

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
    public function generate($itemId)
    {
        try {
            $qrFilename = $this->qrCode->generate($itemId);
            return $this->handleSuccess('QR code generated successfully', 'items.show', ['id' => $itemId], [
                'qr_code' => $qrFilename
            ]);
        } catch (\Exception $e) {
            return $this->handleError($e->getMessage());
        }
    }

    /**
     * Scan QR code
     */
    public function scan(Request $request)
    {
        $validated = $this->validate($request, [
            'qr_data' => 'required|string'
        ]);

        $data = $this->qrCode->validate($validated['qr_data']);
        if (!$data) {
            return $this->handleError('Invalid QR code');
        }

        $item = $this->item->getById($data['id']);
        if (!$item) {
            return $this->handleError('Item not found');
        }

        // Create scan record
        $scanData = [
            'item_id' => $data['id'],
            'scanner_id' => Session::get('user_id'),
            'scan_type' => $item['type'] === 'lost' ? 'found' : 'lost',
            'location' => $request->input('location'),
            'description' => $request->input('description')
        ];

        // Create new item based on scan
        $newItem = $this->item->create(
            Session::get('user_id'),
            $item['title'],
            $scanData['description'],
            $scanData['location'],
            date('Y-m-d'),
            $scanData['scan_type']
        );

        // Create match
        $this->item->createMatch($item['id'], $newItem['id']);

        return $this->handleSuccess('Item scanned and matched successfully', 'items.show', ['id' => $newItem['id']]);
    }

    /**
     * Show QR code form
     */
    public function showForm($itemId)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $item = $this->item->getById($itemId);
        if (!$item || $item['user_id'] !== Session::get('user_id')) {
            return $this->handleError('Unauthorized');
        }

        return $this->view('items.qr-form', ['item' => $item]);
    }
}
