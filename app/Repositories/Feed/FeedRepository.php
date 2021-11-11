<?php

namespace App\Repositories\Feed;

use App\Exceptions\GeneralException;
use App\Models\Feed;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
     * @param null $id
     * @return bool
     */
    public function checkForDuplicates(array $attributes, $id = null): bool
    {
        $query = Feed::query();

        if ($id) {

            $feeds = DB::select("SELECT * from feeds WHERE id != :id AND (link = :link OR title = :title)", ['id' => $id, 'link' => $attributes['link'], 'title' => $attributes['title'] ]);

            return (bool)$feeds;
        }

        $query->where('link', "=", $attributes['link'], "or")->where('title', "=", $attributes['title'], "or");

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

        if (!$feed) {

            return [404, "Feed not found!"];
        }

        DB::beginTransaction();

        try {

            $attributes['description'] = $attributes['description'] ?? preg_replace('/<[^>]*>/', '', $attributes['description']);

            $duplicate = $this->checkForDuplicates($attributes, $id);

            if ($duplicate) {

                return [422, "Feed with given title and/or link already exists."];
            }

            $feed->fill($attributes);
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
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filter): LengthAwarePaginator
    {
        $query = Feed::query();

        if (isset($filter['link'])) {

            $query->where('link', "LIKE", "%{$filter['link']}%");
        }

        if (isset($filter['title'])) {

            $query->where('title', "LIKE", "%{$filter['title']}%");
        }

        if (isset($filter['publish_date_from'])) {

            $query->whereDate('publish_date', ">=", $filter['publish_date_from']);
        }

        if (isset($filter['publish_date_to'])) {

            $query->whereDate('publish_date', "<=", $filter['publish_date_to']);
        }

        return $query->orderBy('created_at', 'DESC')->paginate();
    }

    /**
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        $feed = $this->model->find($id);

        if (!$feed) {

            return [404, "Feed not found!"];
        }

        $feed->delete();

        return [200, "Feed Deleted Successfully!"];
    }

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Feed::class;
    }
}
