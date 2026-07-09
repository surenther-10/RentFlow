<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\PropertyImageModel;

class Properties extends BaseController
{
    protected $propertyModel;
    protected $imageModel;

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
        $this->imageModel = new PropertyImageModel();
    }

    public function index()
    {
        $search = $this->request->getVar('search');
        $status = $this->request->getVar('status');
        $type = $this->request->getVar('type');
        
        // Query for Grid view (page_grid)
        $queryGrid = $this->propertyModel->orderBy('id', 'DESC');
        if ($search) {
            $queryGrid = $queryGrid->groupStart()
                                   ->like('name', $search)
                                   ->orLike('city', $search)
                                   ->orLike('state', $search)
                                   ->orLike('pincode', $search)
                                   ->orLike('address', $search)
                                   ->groupEnd();
        }
        if ($status) {
            $queryGrid = $queryGrid->where('availability_status', $status);
        }
        if ($type) {
            $queryGrid = $queryGrid->where('type', $type);
        }
        $propertiesGrid = $queryGrid->paginate(8, 'grid');

        // Query for Table view (page_table)
        $queryTable = $this->propertyModel->orderBy('id', 'DESC');
        if ($search) {
            $queryTable = $queryTable->groupStart()
                                     ->like('name', $search)
                                     ->orLike('city', $search)
                                     ->orLike('state', $search)
                                     ->orLike('pincode', $search)
                                     ->orLike('address', $search)
                                     ->groupEnd();
        }
        if ($status) {
            $queryTable = $queryTable->where('availability_status', $status);
        }
        if ($type) {
            $queryTable = $queryTable->where('type', $type);
        }
        $propertiesTable = $queryTable->paginate(8, 'table');

        // Fill images
        foreach ($propertiesGrid as &$property) {
            if (empty($property['image'])) {
                $firstImage = $this->imageModel->where('property_id', $property['id'])->first();
                if ($firstImage) {
                    $property['image'] = $firstImage['image_path'];
                }
            }
        }
        foreach ($propertiesTable as &$property) {
            if (empty($property['image'])) {
                $firstImage = $this->imageModel->where('property_id', $property['id'])->first();
                if ($firstImage) {
                    $property['image'] = $firstImage['image_path'];
                }
            }
        }
        
        $data = [
            'propertiesGrid'  => $propertiesGrid,
            'propertiesTable' => $propertiesTable,
            'pager'           => $this->propertyModel->pager,
            'search'          => $search,
            'status'          => $status,
            'type'            => $type,
        ];

        return view('properties/index', $data);
    }

    public function details($id)
    {
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Property not found']);
        }
        
        $images = $this->imageModel->where('property_id', $id)->findAll();
        $property['images'] = $images;

        return $this->response->setJSON(['status' => 'success', 'data' => $property]);
    }

    public function store()
    {
        $rules = [
            'name'                => 'required|min_length[3]|max_length[255]',
            'type'                => 'required',
            'address'             => 'required',
            'city'                => 'permit_empty|max_length[100]',
            'state'               => 'permit_empty|max_length[100]',
            'pincode'             => 'permit_empty|max_length[20]',
            'rent_amount'         => 'required|numeric',
            'rooms'               => 'required|integer',
            'description'         => 'permit_empty',
            'availability_status' => 'required|in_list[available,rented,maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'                => $this->request->getPost('name'),
            'type'                => $this->request->getPost('type'),
            'address'             => $this->request->getPost('address'),
            'city'                => $this->request->getPost('city'),
            'state'               => $this->request->getPost('state'),
            'pincode'             => $this->request->getPost('pincode'),
            'rent_amount'         => $this->request->getPost('rent_amount'),
            'rooms'               => $this->request->getPost('rooms'),
            'description'         => $this->request->getPost('description'),
            'availability_status' => $this->request->getPost('availability_status'),
        ];

        // Database transaction
        $db = \Config\Database::connect();
        $db->transStart();

        $this->propertyModel->insert($data);
        $propertyId = $this->propertyModel->getInsertID();

        // Handle Multiple Images
        $imageFiles = $this->request->getFiles();
        if (isset($imageFiles['images'])) {
            foreach ($imageFiles['images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/properties', $newName);
                    
                    // Save first valid image as property main image (fallback compatible)
                    if (empty($data['image'])) {
                        $mainImagePath = 'uploads/properties/' . $newName;
                        $this->propertyModel->update($propertyId, ['image' => $mainImagePath]);
                    }

                    $this->imageModel->insert([
                        'property_id' => $propertyId,
                        'image_path'  => 'uploads/properties/' . $newName
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Failed to save property. Please try again.');
        }

        return redirect()->to('/properties')->with('success', 'Property registered successfully!');
    }

    public function update($id)
    {
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        $rules = [
            'name'                => 'required|min_length[3]|max_length[255]',
            'type'                => 'required',
            'address'             => 'required',
            'city'                => 'permit_empty|max_length[100]',
            'state'               => 'permit_empty|max_length[100]',
            'pincode'             => 'permit_empty|max_length[20]',
            'rent_amount'         => 'required|numeric',
            'rooms'               => 'required|integer',
            'description'         => 'permit_empty',
            'availability_status' => 'required|in_list[available,rented,maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'                => $this->request->getPost('name'),
            'type'                => $this->request->getPost('type'),
            'address'             => $this->request->getPost('address'),
            'city'                => $this->request->getPost('city'),
            'state'               => $this->request->getPost('state'),
            'pincode'             => $this->request->getPost('pincode'),
            'rent_amount'         => $this->request->getPost('rent_amount'),
            'rooms'               => $this->request->getPost('rooms'),
            'description'         => $this->request->getPost('description'),
            'availability_status' => $this->request->getPost('availability_status'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->propertyModel->update($id, $data);

        // Handle additional image uploads
        $imageFiles = $this->request->getFiles();
        if (isset($imageFiles['images'])) {
            foreach ($imageFiles['images'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . 'uploads/properties', $newName);
                    
                    // If no main image exists, set this as main image
                    if (empty($property['image'])) {
                        $mainImagePath = 'uploads/properties/' . $newName;
                        $this->propertyModel->update($id, ['image' => $mainImagePath]);
                        $property['image'] = $mainImagePath; // update local value
                    }

                    $this->imageModel->insert([
                        'property_id' => $id,
                        'image_path'  => 'uploads/properties/' . $newName
                    ]);
                }
            }
        }

        $db->transComplete();

        return redirect()->to('/properties')->with('success', 'Property updated successfully!');
    }

    public function deleteImage($id)
    {
        $image = $this->imageModel->find($id);
        if ($image) {
            if (file_exists(FCPATH . $image['image_path'])) {
                @unlink(FCPATH . $image['image_path']);
            }
            $this->imageModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Image removed']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Image not found']);
    }

    public function delete($id)
    {
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return redirect()->to('/properties')->with('error', 'Property not found.');
        }

        // Delete all images in properties
        $images = $this->imageModel->where('property_id', $id)->findAll();
        foreach ($images as $img) {
            if (file_exists(FCPATH . $img['image_path'])) {
                @unlink(FCPATH . $img['image_path']);
            }
        }
        $this->imageModel->where('property_id', $id)->delete();

        // Delete main image file
        if ($property['image'] && file_exists(FCPATH . $property['image'])) {
            @unlink(FCPATH . $property['image']);
        }

        $this->propertyModel->delete($id);

        return redirect()->to('/properties')->with('success', 'Property deleted successfully!');
    }
}
