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

    public function getCloudinaryUrl()
    {
        return 'https://res.cloudinary.com/'.config('cloudinary.cloud_name').'/image/upload/';
    }

    public function getResponsiveMedia($fileName, $options = 'f_auto,q_auto')
    {
        if (empty($fileName)) {
            return null;
        }

        return $this->getCloudinaryUrl().$options.'/'.$fileName;
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
        $media = new Media();
        $media->collection = $collection;
        $media->public_id = $response->getPublicId();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();
        $media->width = $response->getWidth();
        $media->height = $response->getHeight();
        $media->uploaded_at = $response->getTimeUploaded();
        $this->media()->save($media);

        $temporaryDirectory->delete();
    }

    /**
     * Attach Rwmote Media Files to a Model
     */
    public function attachRemoteMedia($remoteFile, array $options = [], string $collection = '')
    {
        $response = resolve(CloudinaryEngine::class)->uploadFile($remoteFile, $options);

        $media = new Media();
        $media->collection = $collection;
        $media->public_id = $response->getPublicId();
        $media->file_name = $response->getFileName();
        $media->file_url = $response->getSecurePath();
        $media->size = $response->getSize();
        $media->file_type = $response->getFileType();
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
        $items = $this->media()->get();

        foreach ($items as $item) {
            resolve(CloudinaryEngine::class)->destroy($item->getFileName());

            if (! is_null($media) && $item->_id === $media->_id) {
                return $item->delete();
            }
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

    public function image()
    {
        return $this->fetchFirstMedia();
    }

    public function thumb_url()
    {
        return $this->getResponsiveMedia($this->image()?->file_name, 't_media_lib_thumb');
    }

    public function preview_url()
    {
        return $this->getResponsiveMedia($this->image()?->file_name, 't_preview');
    }

    public function image_url()
    {
        return $this->getResponsiveMedia($this->image()?->file_name, 't_default');
    }
}
