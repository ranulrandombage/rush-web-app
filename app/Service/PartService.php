<?php

namespace App\Service;

use App\Models\Part;
use Illuminate\Database\Eloquent\Collection;

class PartService
{
    /**
     * Create a new part
     * @param string $title
     * @param string $part_no
     * @return Part
     */
    public function createPart(string $title, string $part_no): Part
    {
        $part = new Part();
        $part->title = $title;
        $part->part_no = $part_no;
        $part->save();

        return $part;
    }

    /**
     * Get a part by id
     * @param int $id
     * @return Part
     */
    public function getPartById(int $id): Part
    {
        return Part::find($id);
    }

    /**
     * Update a specific part
     * @param Part $part
     * @param $title
     * @param $part_no
     * @return Part
     */
    public function updatePart(Part $part, string $title, string $part_no): Part
    {
        $part->title = $title;
        $part->part_no = $part_no;
        $part->save();

        return $part;
    }

    /**
     * Get all the parts, if needed can filter without parts
     * @param array $without_part
     * @return Collection
     */
    public function getAllParts(array $without_part = []): Collection
    {
        if ($without_part) {
            return Part::whereNotIn('id', $without_part)->orderBy('created_at', 'desc')->get();
        } else {
            return Part::orderBy('created_at', 'desc')->get();
        }
    }

}
