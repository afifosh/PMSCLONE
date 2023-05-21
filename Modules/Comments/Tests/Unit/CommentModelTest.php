<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Comments\Tests\Unit;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Comments\Models\Comment;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    public function test_comment_has_commentables()
    {
        $comment = new Comment;

        $this->assertInstanceOf(MorphTo::class, $comment->commentable());
    }
}
