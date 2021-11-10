<?php


namespace App\Repositories;


use App\Exceptions\GeneralException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{

    protected mixed $model;

    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->model = $this->setModel();
    }

    /**
     * Get model.
     * @return string
     */
    abstract protected function getModel(): string;

    /**
     * Set model.
     * @throws BindingResolutionException
     */
    public function setModel()
    {
        return app()->make(
            $this->getModel()
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $items = 20)
    {
        return $this->model->orderBy('created_at', 'DESC')->paginate($items);
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @inheritDoc
     * @throws GeneralException
     */
    public function create(array $attributes)
    {
        DB::beginTransaction();

        try {
            $data = $this->model->create($attributes);
        } catch (Exception $exception) {
            DB::rollBack();
            throw new GeneralException($exception->getMessage());
        }

        DB::commit();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $attributes)
    {
        DB::beginTransaction();

        try {
            $data = $this->model->findOrFail($id)->update($attributes);
        } catch (Exception $exception) {
            DB::rollBack();
            return false;
        }

        DB::commit();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }
}
