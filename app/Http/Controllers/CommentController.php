<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\YoutubeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
  public function destroy(string $id)
  {
    try {
      $user = Auth::user();
      $service = new YoutubeService($user->google_token);

      $comment = Comment::find($id);
      if (!$comment) {
        return redirect()->back()->with('error', 'Comment not found.');
      }

      DB::transaction(function () use ($comment, $service) {
        // Delete the comment from YouTube
        $service->setModerationStatus($comment->comment_id, 'rejected');

        // Mark the comment as removed in the database
        $comment->removed_at = now();
        $comment->save();
      });

      return redirect()->back()->with('success', 'Comment deleted successfully.');
    } catch (\Exception $e) {
      report($e);
      return redirect()->back()->with('error', 'Failed to delete comment. Please try again.');
    }
  }
}
