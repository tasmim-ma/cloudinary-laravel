<?php

namespace Tasmim\CloudinaryLaravel\Traits;

use Exception;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Tasmim\CloudinaryLaravel\CloudinaryEngine;
use Tasmim\CloudinaryLaravel\Model\Media;

/**
 * MediaAlly
 *
 * Provides functionality for attaching Cloudinary files to an eloquent model.
 * Whether the model should automatically reload its media relationship after modification.
 *
 */
trait HasMedia
{
    /**
     * Relationship for all attached media.
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function image_url()
    {
        if ($this->fetchFirstMedia()) {
            return resolve(CloudinaryEngine::class)->getResponsiveMedia($this->fetchFirstMedia(), 't_default');
        }

        return null;
    }

    public function preview_url()
    {
        if ($this->fetchFirstMedia()) {
            return resolve(CloudinaryEngine::class)->getResponsiveMedia($this->fetchFirstMedia(), 't_preview');
        }

        return null;
    }

    public function thumb_url()
    {
        if ($this->fetchFirstMedia()) {
            return resolve(CloudinaryEngine::class)->getResponsiveMedia($this->fetchFirstMedia(), 't_media_lib_thumb');
        }

        return null;
    }

    /**
     * Attach Media Files to a Model
     */
    public function attachMedia($file, array $options = [], string $collection = '')
    {
        if (! file_exists($file)) {
            throw new Exception('Please pass in a file that exists');
        }
        //change file name
        $temporaryDirectory = (new TemporaryDirectory())->create();
        $filename = trim($file->getClientOriginalName());
        move_uploaded_file($file->getRealPath(), $temporaryDirectory->path($filename));

        $response = resolve(CloudinaryEngine::class)->uploadFile($temporaryDirectory->path($filename), $options);

        $this->generateMedia($response, $collection);

        $temporaryDirectory->delete();
    }

    /**
     * Attach Rwmote Media Files to a Model
     */
    public function attachRemoteMedia($remoteFile, array $options = [], string $collection = '')
    {
        $response = resolve(CloudinaryEngine::class)->uploadFile($remoteFile, $options);

        $this->generateMedia($response, $collection);
    }

    protected function generateMedia(CloudinaryEngine $response, string $collection = '')
    {
        $media = new Media();
        $media->collection = $collection;
        $media->public_id = $response->getPublicId();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->file_type = $response->getFileType();
        $media->file_extension = $response->getExtension();
        $media->size = $response->getSize();
        $media->readable_size = $response->getReadableSize();
        $media->original_file_name = $response->getOriginalFileName();
        $media->width = $response->getWidth();
        $media->height = $response->getHeight();
        $media->uploaded_at = $response->getTimeUploaded();
        $this->media()->save($media);
    }

    /**
    * Get all the Media files relating to a particular Model record
    */
    public function fetchAllMedia(string $collection = null)
    {
        if ($collection) {
            return $this->media()->where('collection', $collection)->get();
        }

        return $this->media;
    }

    /**
    * Get the first Media file relating to a particular Model record
    */
    public function fetchFirstMedia(string $collection = null)
    {
        if ($collection) {
            return $this->media()->where('collection', $collection)->first();
        }

        return $this->media->first();
    }

    /**
     * Get the last Media file relating to a particular Model record
     */
    public function fetchLastMedia(string $collection = null)
    {
        if ($collection) {
            return $this->media()->where('collection', $collection)->latest()->first();
        }

        return $this->media->last();
    }

    /**
    * Delete all/one file(s) associated with a particular Model record
    */
    public function detachMedia(Media $media = null)
    {
        if (! is_null($media)) {
            resolve(CloudinaryEngine::class)->destroy($media->getFileName());

            return $media->delete();
        }

        $items = $this->media()->get();

        foreach ($items as $item) {
            resolve(CloudinaryEngine::class)->destroy($item->getFileName());
        }

        return $this->media()->delete();
    }

    /**
    * Update the Media files relating to a particular Model record
    */
    public function updateMedia($file, array $options = [], string $collection = '')
    {
        $this->detachMedia();
        $this->attachMedia($file, $options, $collection);
    }

    /**
    * Update the Media files relating to a particular Model record (Specificially existing remote files)
    */
    public function updateRemoteMedia($file, array $options = [], string $collection = '')
    {
        $this->detachMedia();
        $this->attachRemoteMedia($file, $options, $collection);
    }
}
