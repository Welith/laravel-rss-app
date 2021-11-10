<?php

namespace App\Repositories\Feed;

use App\Exceptions\GeneralException;
use App\Models\Feed;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class FeedRepository extends BaseRepository implements FeedRepositoryInterface
{

    /**
     * @param array $attributes
     * @return array
     * @throws GeneralException
     */
    public function create(array $attributes): array
    {
        if ($this->checkForDuplicates($attributes)) {

            return [422, "Feed with given title and/or link already exists."];
        }

        DB::beginTransaction();

        try {

            $attributes['description'] = $attributes['description'] ?? preg_replace('/<[^>]*>/', '', $attributes['description']);

            $this->model->create($attributes);

        } catch (Exception $exception) {

            DB::rollBack();
            throw new GeneralException($exception->getMessage());
        }

        DB::commit();

        return [200, "Feed Successfully Added!"];
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function checkForDuplicates(array $attributes, $id = null): bool
    {
        $query = Feed::query();

        if ($id) {

            $query->where('id', "!=", $id);
        }

        if (isset($attributes['link'])) {

            $query->where('link', "=", $attributes['link'], "or");
        }

        if (isset($attributes['title'])) {

            $query->where('title', "=", $attributes['title'], "or");
        }

        return $query->exists();
    }

    /**
     * @param $id
     * @param array $attributes
     * @return array
     * @throws GeneralException
     */
    public function update($id, array $attributes): array
    {
        $feed = $this->model->find($id);

        $duplicate = false;

        if (!$feed) {

            return [404, "Feed not found!"];
        }

        DB::beginTransaction();

        try {

            $attributes['description'] = $attributes['description'] ?? preg_replace('/<[^>]*>/', '', $attributes['description']);

            $feedUpdated = $feed->fill($attributes);

            if ($feedUpdated->isDirty('link') || $feedUpdated->isDirty('title')) {

                $duplicate = $this->checkForDuplicates($attributes);
            }

            if ($duplicate) {

                return [422, "Feed with given title and/or link already exists."];
            }

            $feed->save();

        } catch (Exception $exception) {

            DB::rollBack();
            throw new GeneralException($exception->getMessage());
        }

        DB::commit();

        return [200, "Feed Successfully Updated!"];
    }

    /**
     * @param array $filter
     * @return array
     */
    public function getFiltered(array $filter): array
    {
        $query = Feed::query();

        if (isset($filter['link'])) {

            $query->where('link', "=", $filter['link']);
        }

        if (isset($filter['title'])) {

            $query->where('title', "=", $filter['title']);
        }

        if (isset($filter['publish_date_from'])) {

            $query->whereDate('publish_date', ">=", $filter['publish_date']);
        }

        if (isset($filter['publish_date_to'])) {

            $query->whereDate('publish_date', "<=", $filter['publish_date']);
        }

        return $query->get();
    }

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Feed::class;
    }
}
