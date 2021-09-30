<?php
namespace Didactinaut\Factories\Database;

use Didactinaut\Configuration\Database\DatabaseInterface;

interface DatabaseFactoryInterface
{
    public function create() : DatabaseInterface;
}