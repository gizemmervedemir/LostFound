<?php

namespace App\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends \App\Controllers\Controller
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Search items
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type');
        
        if (!$query) {
            return response()->json([], 200);
        }

        $items = $this->item->search($query, $type);
        return response()->json($items);
    }

    /**
     * Get item details
     */
    public function show($id)
    {
        $item = $this->item->getById($id);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }
        return response()->json($item);
    }

    /**
     * Get similar items for matching
     */
    public function getSimilarItems($id)
    {
        $item = $this->item->getById($id);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Get similar items based on title, description, and location
        $similarItems = $this->item->search($item['title'], $item['type'] === 'lost' ? 'found' : 'lost');
        
        // Filter out the current item
        $similarItems = array_filter($similarItems, function($i) use ($id) {
            return $i['id'] !== $id;
        });

        return response()->json(array_values($similarItems));
    }
}
