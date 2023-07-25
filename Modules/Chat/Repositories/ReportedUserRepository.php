<?php

namespace Modules\Chat\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Chat\Models\ReportedUser;

/**
 * Class RoleRepository
 *
 * @version November 12, 2019, 11:13 am UTC
 */
class ReportedUserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = ['reported_by', 'reported_to', 'notes'];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportedUser::class;
    }

    /**
     * @param  array  $input
     */
    public function createReportedUser($input)
    {
        $input = Arr::only($input, ['reported_to', 'notes']);
        $input['reported_by'] = Auth::id();

        $reportedUser = ReportedUser::firstOrCreate(Arr::except($input, ['notes']));
        $reportedUser->update(['notes' => $input['notes']]);
    }
}
