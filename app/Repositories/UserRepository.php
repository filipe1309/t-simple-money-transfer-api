<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $model,
        private WalletRepository $walletRepository
    ) {
    }

    public function create(array $data): array
    {
        return $this->model::create($data)->toArray();
    }

    /**
     * @param integer $limit
     * @param array $orderBy
     * @return array
     */
    public function findAll(int $limit = 10, array $orderBy = []): array
    {
        $results = $this->model::query()->with('wallets');

        $results = $this->buildOrderBy($results, $orderBy);

        /** @var Object $this*/
        return $this->buildPaginate($results, $orderBy, $limit)->toArray();
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findOneBy(string $userId): array
    {
        $builder = $this->model::query()->with('wallets');
        return $builder->where('id', $userId)->firstOrFail()->toArray();
    }

    /**
     *
     * Example:
     * name DESC, date ASC
     * $orderBy = [ '-name' => 'DESC', 'date' => 'ASC']
     * http://api/v1/author?order_by=-name,date
     *
     * @param Builder $results
     * @param array $orderBy
     * @return Builder
     */
    protected function buildOrderBy(Builder $results, array $orderBy): Builder
    {
        foreach ($orderBy as $key => $value) {
            if (strstr((string) $key, '-')) {
                $key = substr((string) $key, 1);
            }

            $results->orderBy((string) $key, $value);
        }

        return $results;
    }

    /**
     * Appends = insert into queryStrings into url on pagination
     * http://api/v1/author?order_by=-name,date&limit=12
     * @param Builder $results
     * @param array $orderBy
     * @param integer $limit
     * @param string $query
     * @return LengthAwarePaginator
     */
    protected function buildPaginate(Builder $results, array $orderBy, int $limit, string $query = null): LengthAwarePaginator
    {
        $appends = [
            'order_by' => implode(',', array_keys($orderBy)),
            'limit' => $limit
        ];
        if (!empty($query)) {
            $appends['q'] = $query;
        }

        return $results->paginate($limit)
            ->appends($appends);
    }
}
