<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesNoticeFiles
{
    protected function fileRules(): array
    {
        return [
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'link'       => 'nullable|url|max:500',
        ];
    }

    protected function applyFiles(Request $request, ?Notice $notice = null): array
    {
        $out = [
            'link' => $request->filled('link') ? trim($request->input('link')) : null,
        ];

        if ($request->hasFile('image')) {
            if ($notice?->image) {
                Storage::disk('public')->delete($notice->image);
            }
            $out['image'] = $request->file('image')->store('notices/images', 'public');
        } elseif ($notice && $request->boolean('remove_image')) {
            if ($notice->image) {
                Storage::disk('public')->delete($notice->image);
            }
            $out['image'] = null;
        }

        if ($request->hasFile('attachment')) {
            if ($notice?->attachment) {
                Storage::disk('public')->delete($notice->attachment);
            }
            $file = $request->file('attachment');
            $out['attachment']      = $file->store('notices/files', 'public');
            $out['attachment_name'] = $file->getClientOriginalName();
        } elseif ($notice && $request->boolean('remove_attachment')) {
            if ($notice->attachment) {
                Storage::disk('public')->delete($notice->attachment);
            }
            $out['attachment']      = null;
            $out['attachment_name'] = null;
        }

        return $out;
    }

    protected function deleteFiles(Notice $notice): void
    {
        foreach ([$notice->image, $notice->attachment] as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}