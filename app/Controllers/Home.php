<?php

namespace App\Controllers;

use App\Models\HomeModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function save()
    {
        $image = $this->request->getPost('image');
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $locationName = $this->request->getPost('locationName');
        $video = $this->request->getPost('video') || null;


        // Decode the image from base64, you'll need uncode from basee
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = base64_decode($image || null);

        $imageName = 'captured_image_' . time() . '.png';
        $imagePath = WRITEPATH . 'uploads/' . $imageName;
        file_put_contents($imagePath, $image);

        $model = model(HomeModel::class);
        $model->save([
            'image_path' => 'uploads/' . $imageName,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName,
            'video'=> $video
        ]);

        return view('success');
    }
}
