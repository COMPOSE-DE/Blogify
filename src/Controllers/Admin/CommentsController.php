<?php

namespace ComposeDe\Blogify\Controllers\Admin;

use BlogifyCommentModel;

class CommentsController extends BaseController
{

    public function index($revised = "pending", BlogifyCommentModel $commentsModel)
    {
        $this->checkRevised($revised);

        $comments = $commentsModel->with(['post', 'user'])->byRevised($revised)->paginate($this->config->items_per_page);

        return view('blogify::admin.comments.index', compact('comments', 'revised'));
    }

    public function changeStatus($hash, $status, BlogifyCommentModel $comments)
    {
        $this->checkRevised($status);

        $comment =$comments->byHash($hash);
        $comment->changeStatus($status);

        $message = trans(
            'blogify::notify.comment_success',
            ['action' => $status]
        );
        session()->flash('notify', ['success', $message]);

        return redirect()->route('admin.comments.index');
    }

    private function checkRevised($revised)
    {
        if (! in_array($revised, ['pending', 'approved', 'disapproved'])) {
            abort(404);
        }
    }
}
