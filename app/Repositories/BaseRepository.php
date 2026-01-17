<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Базовый абстрактный репозиторий для работы с моделями Eloquent
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Получить модель для репозитория
     *
     * @return Model
     */
    abstract public function getModel(): Model;

    /**
     * Установить модель для репозитория
     */
    public function setModel(): void
    {
        $this->model = $this->getModel();
    }

    /**
     * Получить все записи
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Найти запись по ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Найти запись по ID или выбросить исключение
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Создать новую запись
     *
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Обновить запись
     *
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update($id, array $attributes)
    {
        $model = $this->model->find($id);

        if ($model) {
            return $model->update($attributes);
        }

        return false;
    }

    /**
     * Удалить запись
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $model = $this->model->find($id);

        if ($model) {
            return $model->delete();
        }

        return false;
    }

    /**
     * Получить модель с отношениями
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function with(array $relations)
    {
        return $this->model->with($relations);
    }

    /**
     * Получить запись с отношениями
     *
     * @param int $id
     * @param array $relations
     * @return Model|null
     */
    public function findWith($id, array $relations = [])
    {
        return $this->model->with($relations)->find($id);
    }
}
