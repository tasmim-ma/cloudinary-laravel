<?php

namespace Tasmim\CloudinaryLaravel\Model;

use Jenssegers\Mongodb\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;
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
        'size',
        'width',
        'height',
        'uploaded_at',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function getSecurePath(): string
    {
        return $this->file_url;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function getFileType(): string
    {
        return $this->file_type;
    }

    public function getSize(): Integer
    {
        return $this->size;
    }

    public function getReadableSize(): string
    {
        return resolve(CloudinaryEngine::class)->getHumanReadableSize($this->size);
    }
}
