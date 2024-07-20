<?php

namespace App\Models;
use CodeIgniter\Model;

class HomeModel extends Model{
    protected $table = 'captured_images';

    protected $allowedFields = ['image_path', 'latitude', 'longitude', 'location_name', 'created_at', 'video'];

}