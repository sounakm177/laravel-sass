<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\Interfaces\SubscriptionRepositoryInterface;
use Illuminate\Http\Request;

final class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function __construct(Request $request, Subscription $model)
    {
        parent::__construct($request);
        $this->model = $model;
    }
}
