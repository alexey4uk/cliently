<?php

namespace App\Repositories;

/**
 * Интерфейс для репозиториев
 */
interface RepositoryInterface
{
    /**
     * Получить все записи
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Найти запись по ID
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id);

    /**
     * Найти запись по ID или выбросить исключение
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail($id);

    /**
     * Создать новую запись
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes);

    /**
     * Обновить запись
     *
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update($id, array $attributes);

    /**
     * Удалить запись
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Получить модель с отношениями
     *
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function with(array $relations);

    /**
     * Получить запись с отношениями
     *
     * @param int $id
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findWith($id, array $relations = []);
}
