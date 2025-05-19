<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{

    protected $seconds = 80000;

    public function setCache($ref, $relationship = null, $single = null)
    {
        if (is_null($single)) {

            if (is_null($relationship)) {

                $data = Cache::remember($ref->getTable(), $this->seconds, function () use ($ref) {
                    return $ref::all();
                });
            } else {
                $data = Cache::remember($ref->getTable(), $this->seconds, function () use ($ref, $relationship) {
                    return $ref::with($relationship)->get();
                });
            }
        } else {

            $param = array_key_first($single);
            $value = $single[$param];
            $data = Cache::remember($ref->getTable() . '_' . $param . '_' . $value, $this->seconds, function () use ($ref, $value, $param) {
                return $ref::where($param, $value)->get();
            });
        }

        return $data;
    }



    public function flushCache($ref = null)
    {
        is_null($ref) ? Cache::flush() : Cache::forget($ref->getTable());
    }
}
