<?php

namespace App\Controllers;

use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Display a listing of items
     */
    public function index()
    {
        $items = $this->item->getByType('lost', 10);
        return $this->view('items.index', ['items' => $items]);
    }

    /**
     * Show the form for creating a new item
     */
    public function create()
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }
        return $this->view('items.create');
    }

    /**
     * Store a newly created item
     */
    public function store(Request $request)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_found' => 'required|date',
            'type' => 'required|in:lost,found',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        } else {
            $imagePath = null;
        }

        $this->item->create(
            Session::get('user_id'),
            $validated['title'],
            $validated['description'],
            $validated['location'],
            $validated['date_found'],
            $validated['type'],
            $imagePath
        );

        return $this->handleSuccess('Item created successfully', 'items.index');
    }

    /**
     * Display the specified item
     */
    public function show($id)
    {
        $item = $this->item->getById($id);
        if (!$item) {
            return $this->handleError('Item not found');
        }

        // Get similar items for matching
        $similarItems = $this->item->search($item['title'], $item['type'] === 'lost' ? 'found' : 'lost');
        
        return $this->view('items.show', [
            'item' => $item,
            'similarItems' => $similarItems
        ]);
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $item = $this->item->getById($id);
        if (!$item || $item['user_id'] !== Session::get('user_id')) {
            return $this->handleError('Unauthorized');
        }

        return $this->view('items.edit', ['item' => $item]);
    }

    /**
     * Update the specified item
     */
    public function update(Request $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date_found' => 'required|date',
            'type' => 'required|in:lost,found',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $item = $this->item->getById($id);
        if (!$item || $item['user_id'] !== Session::get('user_id')) {
            return $this->handleError('Unauthorized');
        }

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
            $validated['image_path'] = $imagePath;
        }

        $this->item->update($id, $validated);
        return $this->handleSuccess('Item updated successfully', 'items.index');
    }

    /**
     * Remove the specified item
     */
    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return $this->redirect('login');
        }

        $item = $this->item->getById($id);
        if (!$item || $item['user_id'] !== Session::get('user_id')) {
            return $this->handleError('Unauthorized');
        }

        $this->item->delete($id);
        return $this->handleSuccess('Item deleted successfully', 'items.index');
    }

    /**
     * Upload image and return path
     */
    private function uploadImage($file)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        return 'uploads/' . $filename;
    }
}
