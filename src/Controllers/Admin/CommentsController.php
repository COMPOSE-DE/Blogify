<?php

namespace Donatix\Blogify\Controllers\Admin;

use Donatix\Blogify\Models\Comment;

class CommentsController extends BaseController
{

    public function index($revised = "pending")
    {
        $this->checkRevised($revised);

        $comments = Comment::byRevised($revised)->paginate($this->config->items_per_page);

        return view('blogify::admin.comments.index', compact('comments', 'revised'));
    }

    public function changeStatus($hash, $new_revised)
    {
        $this->checkRevised($new_revised);

        $comment = Comment::byHash($hash);
        $comment->revised = true;
        $comment->save();

        $message = trans(
            'blogify::notify.comment_success',
            ['action' => $new_revised]
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
