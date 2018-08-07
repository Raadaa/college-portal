<?php

namespace App\Repositories;

use App\User;
use App\Models\Program;
use App\Filters\ProgramFilters;
use Carbon\Carbon;

class ProgramRepository
{
    public function model()
    {
        return app(Program::class);
    }

    public function programs(User $user, ProgramFilters $filters) {
        $items = $user->programs()->filter($filters)->paginate();
        $items->transform(function ($item) use ($filters) {
            return $filters->transform($item);
        });
        return $items;
    }

    public function program($id, ProgramFilters $filters = null) {
        $q = $this->model();
        if ($filters) {
            $q = $q->filter($filters);
        }
        return $filters ? $filters->transform($q->findOrFail($id)) : $q->findOrFail($id);
    }

    public function delete($id) {
        return $this->model()->findOrFail($id)->delete();
    }

    public function create($opts) {
        return $this->model()->create($opts);
    }

    public function update($id, $opts = []) {
        $item = $this->model()->findOrFail($id);
        $item->fill($opts);
        $item->save();
        return $item;
    }

    public function count(ProgramFilters $filters)
    {
        return $this->model()->filter($filters)->select('id', DB::raw('count(*) as total'))->count();
    }
}