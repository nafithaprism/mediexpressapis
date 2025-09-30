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
    $data   = $request->input('data');                 // expect array of JSON strings
    $images = $request->file('images');                // expect array of UploadedFile

    if (!$data || !$images) {
        return response()->json(['message' => 'files are not uploaded', 'status' => 404], 404);
    }

    // Ensure both arrays align
    $count = min(count($data), count($images));
    if ($count === 0) {
        return response()->json(['message' => 'no files found', 'status' => 404], 404);
    }

    $files = [];

    for ($i = 0; $i < $count; $i++) {
        $d = json_decode($data[$i], true) ?: [];

        $is360  = isset($d['is360']) ? $d['is360'] : 'false';
        $type   = $is360 === 'false' ? 'image' : '3d';
        $folder = $type === 'image' ? 'images' : '360';

        // Use the actual file we’re processing
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $images[$i];

        // Base name (without extension), slugged
        $originalName      = $file->getClientOriginalName(); // <- corrected spelling
        $withoutExt        = preg_replace('/\..+$/', '', $originalName);
        $withoutExtSlugged = $this->slugify($withoutExt);

        // Build final filename
        $name = $withoutExtSlugged . '-' . time() . rand(1, 100) . '.' . $file->extension();

        // Use the folder in both URL and storage path
        $files[$i] = [
            'avatar' => $name,
            'url'    => rtrim($this->base_URL, '/') . '/' . $folder . '/' . $name,
            'alt_tag'=> $d['alt_text'] ?? '',
            'type'   => $type,
            'isImg'  => isset($d['isImg']) ? (int)$d['isImg'] : 1,
        ];

        // Upload to Bunny (path should include the folder)
        $uploaded = $this->bunnyCDNStorage->uploadFile(
            $file->getPathName(),
            $this->storageZone . "/{$folder}/{$name}"
        );

        if (!$uploaded) {
            // Use the correct variable and correct method name
            return response()->json([
                'message'    => 'server issue',
                'status'     => 500,
                'image_name' => $file->getClientOriginalName(), // <- corrected
            ], 500);
        }

        // Persist record (note: save the same URL you expose)
        $isUploaded = Upload::create([
            'avatar' => $files[$i]['url'],   // public URL
            'url'    => $name,               // raw name (or store the full path if you prefer)
            'alt_tag'=> $files[$i]['alt_tag'],
            'type'   => $type,
            'isImg'  => $files[$i]['isImg'],
        ]);
    }

    return response()->json([
        'message' => 'media uploaded',
        'data'    => $files, // or collect DB rows if you want
        'status'  => 200
    ], 200);
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


    // public function update_image($file, $id)
    // {

    //     $existing_data = Upload::select('name')->where('id', $id)->first();
    //     $existing_name = $existing_data->name;

    //     $without_ext_name = $this->slugify(preg_replace('/\..+$/', '', $file->getClientOriginalName()));

    //     $name = $without_ext_name . '-' . time() . rand(1, 100) . '.' . $file->extension();
    //     $files[$i]['name'] = $name;
    //     $files[$i]['url'] = $this->base_URL . $name;
    //     $files[$i]['alt_tag'] = time() . rand(1, 100);

    //     if ($this->bunnyCDNStorage->uploadFile($file->getPathName(), $this->storageZone . "/images/{$name}")) {

    //         $isUpdated = Upload::where('_id', $id)->update(['url' => $name, 'avatar' => $files[$i]['url']]);

    //         if (!$this->bunnyCDNStorage->deleteObject('/medi-expresss/images/' . $existing_name)) {
    //             echo json_encode(['message' => 'Bucket error', 'status' => 404]);
    //         }
    //     } else {

    //         return $errors = ['message' => 'server issue', 'status' => 404, 'image_name' => $file->getClientOrignalName()];
    //     }
    // }
public function update_image(Request $request, int $id)
{
    $file = $request->file('image');
    if (!$file) {
        return response()->json(['message' => 'No image provided', 'status' => 422], 422);
    }

    // get the current stored object name (not the full URL)
    $existing = Upload::select('url')->find($id);
    if (!$existing) {
        return response()->json(['message' => 'Record not found', 'status' => 404], 404);
    }

    // build new name & paths
    $withoutExt = $this->slugify(preg_replace('/\..+$/', '', $file->getClientOriginalName()));
    $name       = $withoutExt . '-' . time() . rand(1, 100) . '.' . $file->extension();

    // storage key must match how you upload elsewhere: "<zone>/images/<name>"
    $newStorageKey = $this->storageZone . "/images/{$name}";
    $newPublicUrl  = rtrim($this->base_URL, '/') . '/' . $name;

    // upload replacement
    $ok = $this->bunnyCDNStorage->uploadFile($file->getPathname(), $newStorageKey);
    if (!$ok) {
        return response()->json(['message' => 'Upload failed', 'status' => 502], 502);
    }

    // update DB
    Upload::where('id', $id)->update([
        'url'    => $name,        // stored object name
        'avatar' => $newPublicUrl // full CDN URL
    ]);

    // delete old object (key is relative to the same zone/prefix style)
    $oldName = $existing->url;
    if (!empty($oldName)) {
        $this->bunnyCDNStorage->deleteObject($this->storageZone . "/images/{$oldName}");
    }

    return response()->json(['message' => 'Image updated', 'status' => 200]);
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
