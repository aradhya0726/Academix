<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentDownloadController extends Controller
{
    private const PREVIEWABLE = ['pdf', 'jpg', 'jpeg', 'png'];

    public static function canPreview(?string $fileName): bool
    {
        return $fileName && in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), self::PREVIEWABLE, true);
    }

    private function serve(string $path, ?string $name, bool $inline)
    {
        abort_unless(Storage::disk('public')->exists($path), 404, 'File not found.');

        $name = $name ?: basename($path);

        if ($inline) {
            abort_unless(self::canPreview($name), 404, 'This file type cannot be previewed.');

            return response()->file(Storage::disk('public')->path($path), [
                'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
            ]);
        }

        return Storage::disk('public')->download($path, $name);
    }

    private function canSeeAttachment(Assignment $assignment): bool
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin'   => true,
            'teacher' => (int) $assignment->teacher_id === (int) $user->id,
            'student' => (int) $assignment->class_id === (int) $user->class_id,
            default   => false,
        };
    }

    private function canSeeSubmission(AssignmentSubmission $submission): bool
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin'   => true,
            'teacher' => $submission->assignment && (int) $submission->assignment->teacher_id === (int) $user->id,
            'student' => (int) $submission->student_id === (int) $user->id,
            default   => false,
        };
    }

    public function attachment($id, bool $inline = false)
    {
        $assignment = Assignment::findOrFail($id);
        abort_unless($this->canSeeAttachment($assignment), 403);
        abort_unless($assignment->attachment, 404, 'File not found.');

        return $this->serve($assignment->attachment, $assignment->attachment_name, $inline);
    }

    public function attachmentPreview($id)
    {
        return $this->attachment($id, true);
    }

    public function submission($id, bool $inline = false)
    {
        $submission = AssignmentSubmission::with('assignment')->findOrFail($id);
        abort_unless($this->canSeeSubmission($submission), 403);

        return $this->serve($submission->file, $submission->file_name, $inline);
    }

    public function submissionPreview($id)
    {
        return $this->submission($id, true);
    }
}