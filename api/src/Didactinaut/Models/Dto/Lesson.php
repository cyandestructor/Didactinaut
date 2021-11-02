<?php
namespace Didactinaut\Models\Dto;

class Lesson
{
    public $id;
    public $title;
    public $text;
    public $sectionId;
    public $courseId;
    public $duration;
    public $extra = [];
}