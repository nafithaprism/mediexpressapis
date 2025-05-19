<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use BunnyCDN\Storage\BunnyCDNStorage;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\upload\UploadRequest;

class UploadController extends Controller
{
    public $bunnyCDNStorage;
    public $storageZone = 'medi-expresss';
    public $directory = '/medi-expresss/images';
    public $base_URL = 'https://medi-expresss.b-cdn.net/images/';
    public $access_key = 'dd0b1c5f-6b6a-443e-99e5816a3a56-e786-4460';

    //public $bunny ;

    public function __construct()
    {

        $this->bunnyCDNStorage = new BunnyCDNStorage($this->storageZone, $this->access_key, "sg");
    }

    public function upload_media(UploadRequest $request)
    {

        $data = $request['data'];


        $images = $request->file('images');

        $files = [];

        if ($data && $images) {

            $i = 0;
            foreach ($data as $d) {
                $d = json_decode($d, true);

                $type = $d['is360'] === 'false' ?  'image' : '3d';
                $folder = $type === 'image' ? 'images' : '360';
                $without_ext_name = $this->slugify(preg_replace('/\..+$/', '', $images[$i]->getClientOriginalName()));

                $name = $without_ext_name . '-' . time() . rand(1, 100) . '.' . $images[$i]->extension();
                $files[$i]['avatar'] = $name;
                $files[$i]['url'] = $this->base_URL . $name;
                $files[$i]['alt_tag'] = $d['alt_text'];
                $files[$i]['type'] = $type;
                $files[$i]['isImg'] = isset($d['isImg']) ? ($d['isImg']) : 1;

                if ($this->bunnyCDNStorage->uploadFile($images[$i]->getPathName(), $this->storageZone . "/images/{$name}")) {

                    $isUploaded = Upload::create(['avatar' => $files[$i]['url'], 'url' => $name, 'alt_tag' => $files[$i]['alt_tag'], 'type' => $type, 'isImg' => $files[$i]['isImg']]);

                    echo json_encode(['message' => 'media has uploaded.', 'data' => $isUploaded,  'status' => 200]);
                } else {

                    return $errors = ['message' => 'server issue', 'status' => 404, 'image_name' => $file->getClientOrignalName()];
                }

                $i++;
            }
        } else {
            echo json_encode(['message' => 'files are not uploaded', 'status' => 404]);
        }
    }
    public function files(Request $request)
    {
        $file = $request->file('files');
        $without_ext_name = $this->slugify(preg_replace('/\..+$/', '', $file->getClientOriginalName()));
        $name = $without_ext_name . '-' . time() . rand(1, 100) . '.' . $file->extension();
        $files['avatar'] = $name;
        $files['url'] =  "https://medi-express.b-cdn.net/files/" . "{$name}";
        $files['alt_tag'] = time() . rand(1, 100);
        $files['type'] = $file->extension();
        if ($this->bunnyCDNStorage->uploadFile($file->getPathName(), $this->storageZone . "/files/{$name}")) {
            return json_encode(['data' => $files['url'], 'status' => 'Data Updated Succesffully']);
        }
    }



    public function get_all_images()
    {

        $data = Upload::orderBy('id', 'DESC')->where('isImg', '=', 1)->take(200)->get();

        echo json_encode(['data' => $data, 'status' => 200]);
    }


    public function update_image($file, $id)
    {

        $existing_data = Upload::select('name')->where('id', $id)->first();
        $existing_name = $existing_data->name;

        $without_ext_name = $this->slugify(preg_replace('/\..+$/', '', $file->getClientOriginalName()));

        $name = $without_ext_name . '-' . time() . rand(1, 100) . '.' . $file->extension();
        $files[$i]['name'] = $name;
        $files[$i]['url'] = $this->base_URL . $name;
        $files[$i]['alt_tag'] = time() . rand(1, 100);

        if ($this->bunnyCDNStorage->uploadFile($file->getPathName(), $this->storageZone . "/images/{$name}")) {

            $isUpdated = Upload::where('_id', $id)->update(['url' => $name, 'avatar' => $files[$i]['url']]);

            if (!$this->bunnyCDNStorage->deleteObject('/medi-expresss/images/' . $existing_name)) {
                echo json_encode(['message' => 'Bucket error', 'status' => 404]);
            }
        } else {

            return $errors = ['message' => 'server issue', 'status' => 404, 'image_name' => $file->getClientOrignalName()];
        }
    }

    public function delete_images(Upload $upload)
    {



        if ($upload->delete()) {


            if (!$this->bunnyCDNStorage->deleteObject('/medi-expresss/images/' . $upload->avatar)) {
                echo json_encode(['message' => 'Bucket error', 'status' => 404]);
            }

            echo json_encode(['message' => 'Data has been deleted', 'status' => 200]);
        } else {
            echo json_encode(['message' => 'Data has not been deleted', 'status' => 404]);
        }
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        // $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            echo 'n-a';
        }

        return $text;
    }
}
