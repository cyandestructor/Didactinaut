<?php
namespace Didactinaut\Models\Dto;

class Review
{
    public $id;
    public $body;
    public $date;
    public $score;
    public $published;
    public $reviewer = [];
    public $courseId;
}