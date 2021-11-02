<?php
namespace Didactinaut\Configuration\VideoUpload;

interface VideoUploaderInterface
{
    public function upload(Video $video);
}