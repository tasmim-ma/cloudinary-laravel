<?php

namespace Tasmim\CloudinaryLaravel\Model;

use Jenssegers\Mongodb\Eloquent\Model;
use Tasmim\CloudinaryLaravel\CloudinaryEngine;

/**
 * Class Media
 * @package CloudinaryLabs\CloudinaryLaravel\Model
 */
class Media extends Model
{
    protected $collection = 'media';

    protected $connection = 'mongodb';

    protected $fillable = [
        'model',
        'collection',
        'public_id',
        'file_url',
        'file_name',
        'file_type',
        'file_extension',
        'size',
        'readable_size',
        'original_file_name',
        'width',
        'height',
        'uploaded_at',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function getReadableSize(): string
    {
        return resolve(CloudinaryEngine::class)->getHumanReadableSize($this->size);
    }

    public function thumb_url()
    {
        return resolve(CloudinaryEngine::class)->getResponsiveMedia($this, 't_media_lib_thumb');
    }

    public function preview_url()
    {
        return resolve(CloudinaryEngine::class)->getResponsiveMedia($this, 't_preview');
    }

    public function image_url()
    {
        return resolve(CloudinaryEngine::class)->getResponsiveMedia($this, 't_default');
    }

    public function preview_document(string $options = 'c_fill,g_center,h_160,q_auto,w_160')
    {
        return resolve(CloudinaryEngine::class)->getResponsiveMedia($this, $options);
    }
}
