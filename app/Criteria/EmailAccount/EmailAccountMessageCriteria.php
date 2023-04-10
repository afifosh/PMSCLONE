<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Criteria\EmailAccount;

use App\Innoclapps\Contracts\Repository\CriteriaInterface;
use App\Innoclapps\Contracts\Repository\RepositoryInterface;

class EmailAccountMessageCriteria implements CriteriaInterface
{
    /**
     * Initialize EmailAccountMessageCriteria class
     *
     * @param int $accountId
     * @param int $folderId
     */
    public function __construct(protected $accountId, protected $folderId, protected $term=null)
    {
    }

    /**
     * Apply criteria in query repository
     *
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param \App\Innoclapps\Contracts\Repository\RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if($this->term!=null){
            $model= $model->where('subject','like','%'.$this->term.'%')->orWhere('html_body','like','%'.$this->term.'%')->orWhere('text_body','like','%'.$this->term.'%');
        }
        return $model->where('email_account_id', $this->accountId)
            ->whereHas('folders', function ($query) {
                return $query->where('folder_id', $this->folderId);
            });
    }
}
