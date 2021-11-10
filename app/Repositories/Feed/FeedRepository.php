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
     * @return mixed
     * @throws GeneralException
     */
    public function create(array $attributes): mixed
    {
        DB::beginTransaction();

        try {

            $attributes['source'] = $attributes['source'] ?? null;
            $attributes['source_url']  = $attributes['source_url'] ?? null;
            $attributes['description'] = $attributes['description'] ?? preg_replace('/<[^>]*>/', '', $attributes['description']);

            $data = $this->model->create($attributes);
        } catch (Exception $exception) {

            DB::rollBack();
            throw new GeneralException($exception->getMessage());
        }

        DB::commit();

        return $data;
    }

    /**
     * @param array $filter
     * @return Builder[]|Model[]
     */
    public function getFiltered(array $filter): array
    {
        $query = Feed::query();

        if (isset($filter['link'])) {

            $query->where('link', $filter['link']);
        }

        if (isset($filter['title'])) {

            $query->where('title', $filter['title']);
        }

        if (isset($filter['publish_date_from'])) {

            $query->whereDate('publish_date', ">=", $filter['publish_date']);
        }

        if (isset($filter['publish_date_to'])) {

            $query->whereDate('publish_date', "<=", $filter['publish_date']);
        }

        return $query->getModels();
    }

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Feed::class;
    }
}
