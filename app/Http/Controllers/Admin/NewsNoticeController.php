<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsNoticeController extends Controller
{
    public function index()
    {
        $newsNotices = NewsNotice::where('status', '!=', 'deleted')
            ->latest()
            ->paginate(10);

        $totalCount    = NewsNotice::count();
        $activeCount   = NewsNotice::where('status', 'active')->count();
        $inactiveCount = NewsNotice::where('status', 'inactive')->count();
        $deletedCount  = NewsNotice::where('status', 'deleted')->count();

        return view('admin.news_notices.manage-news-notice', compact(
            'newsNotices',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'deletedCount'
        ));
    }

    public function create()
    {
        return view('admin.news_notices.create-news-notice');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category'          => 'required|in:' . implode(',', array_keys(NewsNotice::categoryOptions())),
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'description'       => 'required|string',
            'notice_date'       => 'nullable|date',
            'location'          => 'nullable|string|max:255',
            'contact_person'    => 'nullable|string|max:255',
            'contact_number'    => 'nullable|digits_between:10,15',
            'priority'          => 'required|in:' . implode(',', array_keys(NewsNotice::priorityOptions())),
            'status'            => 'required|in:active,inactive',
        ], [
            'category.required'    => 'Please select a category.',
            'title.required'       => 'Title is required.',
            'description.required' => 'Description is required.',
            'priority.required'    => 'Priority is required.',
            'status.required'      => 'Status is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validator->errors()->first())
                ->withErrors($validator);
        }

        try {
            NewsNotice::create([
                'category'          => $request->category,
                'title'             => $request->title,
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'notice_date'       => $request->notice_date,
                'location'          => $request->location,
                'contact_person'    => $request->contact_person,
                'contact_number'    => $request->contact_number,
                'priority'          => $request->priority,
                'status'            => $request->status,
            ]);

            return redirect()->route('admin.news-notices.index')
                ->with('success', 'News / Notice created successfully.');
        } catch (\Throwable $e) {
            Log::error('NewsNotice store error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the record.');
        }
    }

    public function update(Request $request, $id)
    {
        $newsNotice = NewsNotice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category'          => 'required|in:' . implode(',', array_keys(NewsNotice::categoryOptions())),
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:1000',
            'description'       => 'required|string',
            'notice_date'       => 'nullable|date',
            'location'          => 'nullable|string|max:255',
            'contact_person'    => 'nullable|string|max:255',
            'contact_number'    => 'nullable|digits_between:10,15',
            'priority'          => 'required|in:' . implode(',', array_keys(NewsNotice::priorityOptions())),
            'status'            => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validator->errors()->first())
                ->with('open_edit_modal', $id)
                ->withErrors($validator);
        }

        try {
            $newsNotice->update([
                'category'          => $request->category,
                'title'             => $request->title,
                'short_description' => $request->short_description,
                'description'       => $request->description,
                'notice_date'       => $request->notice_date,
                'location'          => $request->location,
                'contact_person'    => $request->contact_person,
                'contact_number'    => $request->contact_number,
                'priority'          => $request->priority,
                'status'            => $request->status,
            ]);

            return redirect()->route('admin.news-notices.index')
                ->with('success', 'News / Notice updated successfully.');
        } catch (\Throwable $e) {
            Log::error('NewsNotice update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong while updating the record.')
                ->with('open_edit_modal', $id);
        }
    }

    public function destroy($id)
    {
        try {
            $newsNotice = NewsNotice::findOrFail($id);
            $newsNotice->update([
                'status' => 'deleted',
            ]);

            return redirect()->route('admin.news-notices.index')
                ->with('success', 'News / Notice deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('NewsNotice delete error: ' . $e->getMessage());

            return redirect()->route('admin.news-notices.index')
                ->with('error', 'Something went wrong while deleting the record.');
        }
    }
}