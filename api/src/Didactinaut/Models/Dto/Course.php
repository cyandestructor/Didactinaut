<?php
namespace Didactinaut\Models\Dto;

class Course
{
    public $id;
    public $title;
    public $description;
    public $publicationDate;
    public $lastUpdate;
    public $published;
    public $imageId;
    public $product = [];
    public $instructor = [];
    public $extra = [];
}