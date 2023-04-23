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

namespace App\Support;

use ReflectionClass;
use ReflectionMethod;
use App\Contracts\Repositories\CallRepository;
use App\Contracts\Repositories\NoteRepository;
use App\Contracts\Repositories\TeamRepository;
use App\Innoclapps\Criteria\WithTrashedCriteria;
use App\Contracts\Repositories\CompanyRepository;
use App\Contracts\Repositories\ContactRepository;
use App\Contracts\Repositories\ProductRepository;
use App\Contracts\Repositories\WebFormRepository;
use App\Contracts\Repositories\ActivityRepository;
use App\Contracts\Repositories\DocumentRepository;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Contracts\Repositories\DocumentTemplateRepository;
use App\Innoclapps\Contracts\Repositories\FilterRepository;
use App\Innoclapps\Contracts\Repositories\WorkflowRepository;
use App\Contracts\Repositories\PredefinedMailTemplateRepository;

class TransferUserData
{
    /**
     * Create new TransferUserData instance.
     *
     * @param int $toUserID
     * @param int $fromUserID
     */
    public function __construct(protected int $toUserID, protected int $fromUserID)
    {
    }

    /**
     * Invoke the transfer
     *
     * @return void
     */
    public function __invoke()
    {
        $methods = (new ReflectionClass($this))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (! str_starts_with($method->getName(), '__')) {
                $this->{$method->getName()}();
            }
        }
    }

    /**
    * Transfer contacts
    */
    public function contacts()
    {
        $repository = resolve(ContactRepository::class)->pushCriteria(WithTrashedCriteria::class);

        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }

    /**
    * Transfer companies
    */
    public function companies()
    {
        $repository = resolve(CompanyRepository::class)->pushCriteria(WithTrashedCriteria::class);
        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }


    /**
     * Transfer notes
     */
    public function notes()
    {
        resolve(NoteRepository::class)
            ->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }

    /**
     * Transfer calls
     */
    public function calls()
    {
        resolve(CallRepository::class)
            ->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }

    /**
     * Transfer activities
     */
    public function activities()
    {
        $repository = resolve(ActivityRepository::class)->pushCriteria(WithTrashedCriteria::class);
        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }

    /**
     * Transfer accounts created by
     */
    public function accounts()
    {
        resolve(EmailAccountRepository::class)
            ->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
    }

    /**
     * Transfer shared filter
     */
    public function filters()
    {
        resolve(FilterRepository::class)
            ->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID, 'is_shared' => 1]);
    }

    /**
     * Transfer shared predefined mail templates
     */
    public function predefinedMailTemplates()
    {
        resolve(PredefinedMailTemplateRepository::class)
            ->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID, 'is_shared' => 1]);
    }

    /**
     * Transfer created workflows
     */
    public function workflows()
    {
        resolve(WorkflowRepository::class)
            ->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
    }

    /**
    * Transfer created web forms
    */
    public function webForms()
    {
        $repository = resolve(WebFormRepository::class);

        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }

    /**
    * Transfer created products
    */
    public function products()
    {
        $repository = resolve(ProductRepository::class)->pushCriteria(WithTrashedCriteria::class);

        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
    }

    /**
    * Transfer documents
    */
    public function documents()
    {
        $repository = resolve(DocumentRepository::class)->pushCriteria(WithTrashedCriteria::class);

        $repository->massUpdate(['created_by' => $this->toUserID], ['created_by' => $this->fromUserID]);
        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
        $repository->massUpdate(['sent_by' => $this->toUserID], ['sent_by' => $this->fromUserID]);
        $repository->massUpdate(['marked_accepted_by' => $this->toUserID], ['marked_accepted_by' => $this->fromUserID]);
    }

    /**
    * Transfer document templates
    */
    public function documentTemplates()
    {
        $repository = resolve(DocumentTemplateRepository::class);

        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID, 'is_shared' => 1]);
    }

    /**
    * Transfer teams
    */
    public function teams()
    {
        $repository = resolve(TeamRepository::class);

        $repository->massUpdate(['user_id' => $this->toUserID], ['user_id' => $this->fromUserID]);
    }
}
